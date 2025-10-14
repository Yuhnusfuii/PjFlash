<?php

namespace App\Livewire\Mcq;

use App\Models\Deck;
use App\Services\Quiz\McqGenerator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Take extends Component
{
    public Deck $deck;

    #[Url(as: 'mode')]
    public string $mode = 'mixed';

    #[Url(as: 'n')]
    public int $n = 10;

    public array $questions = [];
    public int $i = 0;                 // câu hiện tại
    public ?int $picked = null;        // đáp án chọn (0..3)
    public int $correctCount = 0;
    public bool $finished = false;

    public function mount(Deck $deck): void
    {
        abort_unless($deck->user_id === Auth::id(), 403);
        $this->deck = $deck;

        $gen = McqGenerator::make($deck, $this->n, $this->mode);
        if (!empty($gen['meta']['error'] ?? null)) {
            session()->flash('error', $gen['meta']['error']);
            $this->redirectRoute('mcq.home', navigate: true);
            return;
        }

        $this->questions = $gen['questions'];
        $this->n = $gen['meta']['total'] ?: $this->n;

        // reset
        $this->i = 0;
        $this->picked = null;
        $this->correctCount = 0;
        $this->finished = false;
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
        if ($this->finished) return;

        // nếu chưa chọn, bỏ qua
        if ($this->picked === null) return;

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

        return view('livewire.mcq.take', [
            'q' => $q,
            'total' => count($this->questions),
            'progress' => ($this->i + 1) . '/' . max(1, count($this->questions)),
            'score' => $this->finished ? $this->correctCount : null,
        ]);
    }
}
