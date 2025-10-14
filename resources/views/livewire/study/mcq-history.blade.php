<div class="max-w-5xl py-6 container-app">
    <h1 class="mb-4 text-2xl font-semibold">MCQ History</h1>

    <div class="space-y-3">
        @forelse($quizzes as $qz)
            @php
                $total = $qz->results->count();
                $score = $qz->results->where('is_correct', true)->count();
            @endphp
            <div class="p-4 bg-white border rounded-xl dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm">
                            <span class="font-medium">{{ $qz->deck->name }}</span>
                            <span class="text-slate-500">• {{ $qz->mode }}</span>
                        </div>
                        <div class="text-xs text-slate-500">
                            {{ $qz->created_at?->format('Y-m-d H:i') }}
                            @if($qz->completed_at) • completed {{ $qz->completed_at->diffForHumans() }} @endif
                        </div>
                    </div>
                    <div class="text-sm">
                        Score:
                        <span class="font-semibold">{{ $score }}</span> / {{ $total }}
                    </div>
                </div>

                @if($total)
                    <details class="mt-3">
                        <summary class="text-sm cursor-pointer text-slate-600">Show details</summary>
                        <div class="grid gap-2 mt-2 sm:grid-cols-2">
                            @foreach($qz->results as $r)
                                <div class="border rounded-lg p-3 text-sm {{ $r->is_correct ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-rose-50 dark:bg-rose-900/20' }}">
                                    <div class="text-slate-600">{{ $r->direction === 'front_to_back' ? 'Front → Back' : 'Back → Front' }}</div>
                                    <div class="font-medium">{{ $r->question }}</div>
                                    <div class="mt-1">
                                        <span class="text-slate-500">Correct:</span> {{ $r->correct }}
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Picked:</span> {{ $r->picked ?? '—' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>
        @empty
            <div class="text-slate-500">No quizzes yet.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $quizzes->links() }}
    </div>
</div>
