<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ReviewState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudyController extends Controller
{
    public function queue(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $dueCount = ReviewState::where('user_id', $userId)
            ->where('due_at', '<=', Carbon::now())
            ->count();

        $newCount = Item::whereDoesntHave('reviewStates', fn($q) => $q->where('user_id', $userId))
            ->count();

        return response()->json(['due' => $dueCount, 'new' => $newCount]);
    }

    public function review(Item $item, Request $request): JsonResponse
    {
        // TODO: SRS real logic
        return response()->json(['message' => 'Review accepted (stub).', 'item_id' => $item->id]);
    }

    public function generateMcq(Item $item, Request $request): JsonResponse
    {
        $data = $item->data ?? [];

        if (!isset($data['mcq']) || !is_array($data['mcq'])) {
            $question = $item->front ?: 'Question';
            $answer   = $item->back  ?: 'Answer';

            $data['mcq'] = [
                'question'      => $question,
                'options'       => [$answer, 'Distractor A', 'Distractor B', 'Distractor C'],
                'answer_index'  => 0,
            ];
        }

        $item->data = $data;
        $item->save();

        return response()->json(['message' => 'MCQ generated', 'item_id' => $item->id, 'mcq' => $item->data['mcq']]);
    }

    public function generateMatching(Item $item, Request $request): JsonResponse
    {
        $data = $item->data ?? [];

        // TẠO PAYLOAD CHUẨN: mỗi cặp đều có 'left' & 'right' là STRING không rỗng
        $pairs = [
            ['left' => $item->front ?: 'Hello',  'right' => $item->back ?: 'Xin chào'],
            ['left' => 'Cat',  'right' => 'Mèo'],
            ['left' => 'Dog',  'right' => 'Chó'],
            ['left' => 'Bird', 'right' => 'Chim'],
        ];

        // Lọc bảo đảm ổn định
        $pairs = array_values(array_filter($pairs, function ($p) {
            return isset($p['left'], $p['right'])
                && is_string($p['left']) && is_string($p['right'])
                && trim($p['left']) !== '' && trim($p['right']) !== '';
        }));

        $data['matching'] = ['pairs' => $pairs];

        $item->data = $data;
        $item->save();

        return response()->json([
            'message'  => 'Matching generated',
            'item_id'  => $item->id,
            'matching' => $item->data['matching'],
        ]);
    }
}
