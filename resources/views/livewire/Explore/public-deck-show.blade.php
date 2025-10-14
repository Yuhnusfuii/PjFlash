<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">{{ $deck->name }}</h1>
            <div class="text-sm text-slate-500">Public deck • {{ $deck->items->count() }} items</div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('explore.index') }}" class="btn-outline">Back to Explore</a>

            @auth
                @if (auth()->id() !== $deck->user_id)
                    <button wire:click="saveToMyDeck" class="btn">Save to my decks</button>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn">Login to save</a>
            @endauth
        </div>
    </div>

    @if (session('ok'))
        <div class="p-3 rounded-lg bg-emerald-50 text-emerald-700">{{ session('ok') }}</div>
    @endif
    @if (session('error'))
        <div class="p-3 rounded-lg bg-rose-50 text-rose-700">{{ session('error') }}</div>
    @endif

    @if ($deck->description)
        <div class="p-4 card">{{ $deck->description }}</div>
    @endif

    <div class="p-0 overflow-hidden card">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th class="px-4 py-2 text-left">Front</th>
                    <th class="px-4 py-2 text-left">Back</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($deck->items as $it)
                    <tr class="border-t border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-2 align-top">{{ $it->front }}</td>
                        <td class="px-4 py-2 align-top text-emerald-700 dark:text-emerald-400">{{ $it->back }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-4 py-6 text-slate-500">No items in this deck yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
