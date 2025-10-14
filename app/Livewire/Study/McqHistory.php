<?php

namespace App\Livewire\Study;

use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class McqHistory extends Component
{
    public function render()
    {
        $quizzes = Quiz::with(['deck','results'])
            ->where('user_id', Auth::id())
            ->latest('id')
            ->paginate(20);

        return view('livewire.study.mcq-history', compact('quizzes'));
    }
}
