<div class="py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold">Your decks</h1>

        <div class="flex items-center gap-3">
            {{-- Study all --}}
            <a href="{{ route('study.all') }}"
               class="inline-flex items-center gap-2 px-5 py-2 rounded-xl font-semibold bg-indigo-500 text-white hover:bg-indigo-600 shadow-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v10l11-5L8 7z"/>
                </svg>
                Study All
            </a>

            {{-- New deck --}}
            <a href="{{ route('decks.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2 rounded-xl font-semibold bg-emerald-500 text-white hover:bg-emerald-600 shadow-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                </svg>
                New deck
            </a>
        </div>
    </div>

    {{-- Search + Refresh --}}
    <div class="y-card y-card-pad mb-6">
        <div class="flex items-center gap-3">
            <input
                type="text"
                placeholder="Nhập tên deck..."
                class="y-input"
                @if (property_exists($this, 'q')) wire:model.debounce.300ms="q" @endif
            >
            <button type="button" class="y-btn" wire:click="$refresh">Refresh</button>
        </div>
    </div>

    {{-- Empty state --}}
    @if(($decks->count() ?? 0) === 0)
        <div class="y-card y-card-pad text-slate-500 dark:text-slate-400">
            Không có deck nào. Hãy tạo deck đầu tiên của bạn!
        </div>
    @else
        {{-- Grid of decks --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($decks as $deck)
                <div class="y-card h-full hover:shadow-md transition">
                    <div class="y-card-pad flex flex-col h-full">
                        {{-- Title row --}}
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $deck->name }}
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $deck->items_count ?? $deck->items_count ?? $deck->items()->count() }} items
                                </p>
                            </div>

                            @if (!empty($deck->is_public))
                                <span
                                    class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800">
                                    Public
                                </span>
                            @endif
                        </div>

                        {{-- Spacer --}}
                        <div class="flex-1"></div>

                        {{-- Actions --}}
                        <div class="mt-4 flex items-center justify-between gap-2">
                            {{-- Open = solid button --}}
                            <a href="{{ route('decks.show', $deck->id) }}"
                               class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-xl font-medium text-white bg-emerald-500 hover:bg-emerald-600 transition">
                                Open
                            </a>

                            {{-- Study = nổi bật (outline + icon) --}}
                            <a href="{{ route('decks.study', $deck->id) }}"
                               class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-xl font-semibold border border-emerald-500 text-emerald-600 hover:bg-emerald-50 transition shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 6v12m6-6H6"/>
                                </svg>
                                Study
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination (nếu có) --}}
        @if(method_exists($decks, 'links'))
            <div class="mt-6">
                {{ $decks->links() }}
            </div>
        @endif
    @endif
</div>
