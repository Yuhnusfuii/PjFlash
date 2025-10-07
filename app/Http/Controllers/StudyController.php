<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ReviewState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

use App\Services\McqGeneratorService;
use App\Services\MatchingGeneratorService;
use App\Enums\ItemType;

class StudyController extends Controller
{
    public function __construct()
    {
        // Bảo vệ toàn bộ controller bằng Sanctum
        $this->middleware('auth:sanctum');
    }

    /**
     * POST /api/study/{item}/review
     * Chấm điểm một item theo SRS và cập nhật review_states của user hiện tại.
     */
    public function review(Request $request, Item $item): JsonResponse
    {
        $this->authorize('view', $item);

        $data = $request->validate([
            'grade'       => ['required', Rule::in(['again', 'hard', 'good', 'easy'])],
            'answered_at' => ['nullable', 'date'],
            'duration_ms' => ['nullable', 'integer', 'min:0'],
        ]);

        $userId = Auth::id();

        /** @var ReviewState $state */
        $state = ReviewState::firstOrCreate(
            ['user_id' => $userId, 'item_id' => $item->id],
            [
                // nếu bảng review_states không có các cột dưới, bạn có thể bỏ bớt
                'due_at'   => now(),
                'interval' => 0,
                'ease'     => 250,
                'reps'     => 0,
                'lapses'   => 0,
            ]
        );

        // Chỉ chủ nhân review_state mới được cập nhật
        $this->authorize('review', $state);

        // SRS đơn giản
        $grade    = $data['grade'];
        $ease     = (int) $state->ease;
        $interval = (int) $state->interval;
        $reps     = (int) $state->reps;
        $lapses   = (int) $state->lapses;

        switch ($grade) {
            case 'again':
                $lapses++;
                $interval = 1;                 // 1 ngày
                $ease = max(130, $ease - 20);
                break;
            case 'hard':
                $interval = max(1, (int)ceil($interval * 1.2));
                $ease = max(130, $ease - 5);
                break;
            case 'good':
                $interval = max(1, (int)ceil($interval * ($ease / 250)));
                break;
            case 'easy':
                $interval = max(1, (int)ceil($interval * ($ease / 200)));
                $ease = min(350, $ease + 10);
                break;
        }
        $reps++;

        $state->fill([
            'ease'             => $ease,
            'interval'         => $interval,
            'reps'             => $reps,
            'lapses'           => $lapses,
            'last_answered_at' => now(),
            'due_at'           => now()->addDays($interval),
        ])->save();

        return response()->json([
            'ok'    => true,
            'state' => [
                'id'       => $state->id,
                'due_at'   => $state->due_at,
                'interval' => $state->interval,
                'ease'     => $state->ease,
                'reps'     => $state->reps,
                'lapses'   => $state->lapses,
            ],
        ]);
    }

    /**
     * POST /api/items/{item}/mcq/generate
     */
    public function generateMcq(Request $request, Item $item, McqGeneratorService $service): JsonResponse
    {
        $this->authorize('view', $item);

        // (tuỳ) yêu cầu deck có đủ dữ liệu để tạo nhiễu
        $deckItemCount = Item::where('deck_id', $item->deck_id)->count();
        if ($deckItemCount < 4) {
            throw ValidationException::withMessages([
                'deck' => "Not enough items in this deck to generate MCQ (needs ≥ 4, has $deckItemCount).",
            ]);
        }

        // Gọi service
        $payload = $service->generate($item);

        // Validate payload
        validator($payload, [
            'question'     => ['required', 'string'],
            'options'      => ['required', 'array', 'min:2', 'max:8'],
            'options.*'    => ['present'],
            'answer_index' => ['required', 'integer', 'min:0'],
        ])->validate();

        if (! array_key_exists($payload['answer_index'], $payload['options'])) {
            throw ValidationException::withMessages(['mcq' => 'Answer index is out of range.']);
        }

        // Lưu vào item->data + set type
        $data = $item->data ?? [];
        $data['mcq'] = $payload;
        $item->data = $data;

        // nếu cột type cast enum thì dùng Enum, còn không thì dùng chuỗi
        if (class_exists(ItemType::class)) {
            $item->type = ItemType::MCQ;
        } else {
            $item->type = 'mcq';
        }

        $item->save();

        return response()->json(['ok' => true, 'mcq' => $payload]);
    }

    /**
     * POST /api/items/{item}/matching/generate
     */
    public function generateMatching(Request $request, Item $item, MatchingGeneratorService $service): JsonResponse
    {
        $this->authorize('view', $item);

        $deckItemCount = Item::where('deck_id', $item->deck_id)->count();
        if ($deckItemCount < 4) {
            throw ValidationException::withMessages([
                'deck' => "Not enough items in this deck to generate Matching (needs ≥ 4, has $deckItemCount).",
            ]);
        }

        $payload = $service->generate($item);

        validator($payload, [
            'pairs'         => ['required', 'array', 'min:3', 'max:10'],
            'pairs.*.left'  => ['required'],
            'pairs.*.right' => ['required'],
        ])->validate();

        $data = $item->data ?? [];
        $data['matching'] = $payload;
        $item->data = $data;

        if (class_exists(ItemType::class)) {
            $item->type = ItemType::MATCHING;
        } else {
            $item->type = 'matching';
        }

        $item->save();

        return response()->json(['ok' => true, 'matching' => $payload]);
    }
}
