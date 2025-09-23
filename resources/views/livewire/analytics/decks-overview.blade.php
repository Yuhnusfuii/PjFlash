<div class="max-w-6xl p-6 mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Decks Overview</h1>
        <a href="{{ route('decks.index') }}" class="text-sm underline">← Back to decks</a>
    </div>

    {{-- Toolbar --}}
    <div class="grid gap-3 p-4 border rounded md:grid-cols-3">
        <div>
            <div class="mb-1 text-xs text-gray-500">Search deck</div>
            <input
                type="text"
                placeholder="Type deck name…"
                wire:model.live.debounce.300ms="q"
                class="w-full px-3 py-2 border rounded"
            >
        </div>
        <div>
            <div class="mb-1 text-xs text-gray-500">Sort by</div>
            <select wire:model.live="sort" class="w-full px-3 py-2 border rounded">
                <option value="due_desc">Due now ↓</option>
                <option value="due_asc">Due now ↑</option>
                <option value="items_desc">Items ↓</option>
                <option value="items_asc">Items ↑</option>
                <option value="progress_desc">Progress % ↓</option>
                <option value="progress_asc">Progress % ↑</option>
                <option value="ef_desc">Avg EF ↓</option>
                <option value="ef_asc">Avg EF ↑</option>
                <option value="reviewed_desc">Reviewed today ↓</option>
                <option value="reviewed_asc">Reviewed today ↑</option>
            </select>
        </div>
        <div>
            <div class="mb-1 text-xs text-gray-500">Per page</div>
            <select wire:model.live="perPage" class="w-full px-3 py-2 border rounded">
                <option>5</option>
                <option selected>10</option>
                <option>15</option>
                <option>20</option>
                <option>30</option>
                <option>50</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-sm">
            <thead class="text-gray-600 bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Deck</th>
                    <th class="p-3 text-right">Items</th>
                    <th class="p-3 text-right">Due now</th>
                    <th class="p-3 text-right">Scheduled</th>
                    <th class="p-3 text-right">Learned</th>
                    <th class="p-3 text-right">Reviewed today</th>
                    <th class="p-3 text-right">Avg EF</th>
                    <th class="p-3 text-left">Progress</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($rows as $d)
                    <tr>
                        <td class="p-3">
                            <div class="font-medium">{{ $d->name }}</div>
                            <div class="text-xs text-gray-500">#{{ $d->id }} • {{ $d->created_at?->diffForHumans() }}</div>
                        </td>
                        <td class="p-3 text-right">{{ $d->items_count }}</td>
                        <td class="p-3 text-right">
                            <span class="{{ $d->due_now_count > 0 ? 'text-red-600 font-semibold' : '' }}">
                                {{ $d->due_now_count }}
                            </span>
                        </td>
                        <td class="p-3 text-right">{{ $d->scheduled_count }}</td>
                        <td class="p-3 text-right">{{ $d->learned_count }}</td>
                        <td class="p-3 text-right">{{ $d->reviewed_today_count }}</td>
                        <td class="p-3 text-right">{{ number_format((float)$d->avg_ef, 2) }}</td>
                        <td class="p-3">
                            <div class="w-44">
                                <div class="flex items-center justify-between text-xs">
                                    <span>{{ $d->progress }}%</span>
                                    <span>{{ $d->learned_count }}/{{ max(1, $d->items_count) }}</span>
                                </div>
                                <div class="h-2 mt-1 bg-gray-200 rounded">
                                    <div class="h-2 bg-gray-800 rounded" style="width: {{ $d->progress }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3 text-right">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('study.panel', $d) }}" class="underline">Study</a>
                                <a href="{{ route('decks.analytics', $d) }}" class="underline">Analytics</a>
                                <a href="{{ route('decks.show', $d) }}" class="underline">Open</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="p-4 text-center text-gray-500">No decks found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $rows->links() }}
    </div>
</div>
