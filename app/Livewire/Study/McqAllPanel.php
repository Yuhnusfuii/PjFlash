<?php

namespace App\Livewire\Study;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\Quiz\McqGenerator;

#[Layout('layouts.app')]
class McqAllPanel extends Component
{
    #[Url(as: 'mode')]
    public string $mode = 'mixed';

    #[Url(as: 'n')]
    public int $num = 10;

    public array $questions = [];
    public int $i = 0;
    public ?int $picked = null;
    public int $correctCount = 0;
    public bool $finished = false;

    public function mount(): void
    {
        $gen = McqGenerator::makeGlobalForUser(Auth::id(), $this->num, $this->mode);

        if (!empty($gen['meta']['error'] ?? null)) {
            session()->flash('error', $gen['meta']['error']);
            $this->redirectRoute('mcq.home', navigate: true);
            return;
        }

        $this->questions = $gen['questions'];
        $this->num = $gen['meta']['total'] ?: $this->num;

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
        if ($q && $idx === $q['correctIndex']) $this->correctCount++;
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
        $this->mount();
    }

    public function render()
    {
        $q = $this->questions[$this->i] ?? null;

        return view('livewire.study.mcq-all-panel', [
            'q'        => $q,
            'title'    => 'All decks',
            'total'    => count($this->questions),
            'progress' => ($this->i + 1) . '/' . max(1, count($this->questions)),
            'score'    => $this->finished ? $this->correctCount : null,
        ]);
    }
}
