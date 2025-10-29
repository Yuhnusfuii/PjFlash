<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TestHello extends Component
{
    public function render()
    {
        return <<<'BLADE'
<div class="max-w-xl mx-auto p-8">
  <h1 class="text-2xl font-semibold">Livewire OK</h1>
  <p class="text-slate-500 mt-2">Nếu bạn thấy trang này, route → Livewire hoạt động bình thường.</p>
</div>
BLADE;
    }
}
