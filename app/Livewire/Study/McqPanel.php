<?php

namespace App\Livewire\Study;

use App\Models\Deck;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class McqPanel extends Component
{
    public Deck $deck;

    #[Url(as: 'mode')]
    public string $mode = 'mixed'; // mixed|front_to_back|back_to_front

    #[Url(as: 'n')]
    public int $num = 10;

    public int $i = 0;                 // index câu hiện tại
    public ?int $picked = null;        // đáp án đã chọn
    public int $correctCount = 0;
    public bool $finished = false;

    public array $questions = [];

    public function mount(Deck $deck): void
    {
        // Bảo vệ quyền truy cập deck
        abort_unless($deck->user_id === Auth::id(), 403);
        $this->authorize('view', $deck);

        // Lưu vào property + eager load items
        $this->deck = $deck->load(['items:id,deck_id,front,back']);

        // Tạo bộ câu hỏi
        $this->questions = $this->generateQuestions($this->deck, $this->num, $this->mode);

        // Reset trạng thái
        $this->i = 0;
        $this->picked = null;
        $this->correctCount = 0;
        $this->finished = false;
    }

    /** Sinh câu hỏi (1 đúng + 3 nhiễu) với 2 chiều hỏi đáp */
    protected function generateQuestions(Deck $deck, int $n, string $mode): array
    {
        $items = $deck->items;

        if ($items->count() < 4) {
            session()->flash('error', 'Deck cần tối thiểu 4 thẻ để tạo câu hỏi.');
            $this->redirectRoute('mcq.home', navigate: true);
            return [];
        }

        $pool = $items->shuffle()->take(min($n, $items->count()))->values();
        $out = [];

        foreach ($pool as $item) {
            $direction = match ($mode) {
                'front_to_back' => 'front_to_back',
                'back_to_front' => 'back_to_front',
                default => (rand(0,1) ? 'front_to_back' : 'back_to_front'),
            };

            [$promptField, $answerField] = $direction === 'front_to_back'
                ? ['front', 'back']
                : ['back', 'front'];

            $prompt  = trim((string) $item->{$promptField});
            $correct = trim((string) $item->{$answerField});

            $distractors = $items
                ->where('id', '!=', $item->id)
                ->pluck($answerField)
                ->filter(fn ($v) => filled($v) && trim((string) $v) !== $correct)
                ->shuffle()
                ->take(3)
                ->values()
                ->all();

            while (count($distractors) < 3) {
                $distractors[] = '—';
            }

            $options = $distractors;
            $options[] = $correct;
            shuffle($options);

            $out[] = [
                'item_id'      => $item->id,
                'direction'    => $direction,
                'prompt'       => $prompt,
                'correct'      => $correct,
                'options'      => $options,
                'correctIndex' => array_search($correct, $options, true),
            ];
        }

        return $out;
    }

    public function choose(int $idx): void
    {
        if ($this->finished || $this->picked !== null) return;
        $this->picked = $idx;

        $q = $this->questions[$this->i] ?? null;
        if ($q && $idx === $q['correctIndex']) {
            $this->correctCount++;
        }
    }

    public function next(): void
    {
        if ($this->finished || $this->picked === null) return;

        if ($this->i < count($this->questions) - 1) {
            $this->i++;
            $this->picked = null;
        } else {
            $this->finished = true;
        }
    }

    public function retry(): void
    {
        $this->mount($this->deck);
    }

    public function render()
    {
        $q = $this->questions[$this->i] ?? null;

        return view('livewire.study.mcq-panel', [
            'q'        => $q,
            'total'    => count($this->questions),
            'progress' => ($this->i + 1) . '/' . max(1, count($this->questions)),
            'score'    => $this->finished ? $this->correctCount : null,
        ]);
    }
}
