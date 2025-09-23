<div class="max-w-5xl p-6 mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold">{{ $deck->name }}</h1>

    <div class="flex items-center gap-2">
    <span class="text-sm text-gray-500">Due: {{ $this->dueCount }}</span>

    <a href="{{ route('study.panel', $deck) }}"
       class="px-3 py-2 text-sm text-white bg-black rounded hover:opacity-90">Study</a>

    <a href="{{ route('decks.analytics', $deck) }}"
       class="px-3 py-2 text-sm border rounded hover:bg-gray-50">Analytics</a>

    <a href="{{ route('decks.index') }}" class="text-sm underline">← Back</a>
    </div>

    </div>


    {{-- Flash --}}
    @if (session('ok'))
        <div class="p-3 text-sm text-green-800 bg-green-100 rounded">{{ session('ok') }}</div>
    @endif

    {{-- Toolbar --}}
    <div class="grid gap-3 p-4 border rounded md:grid-cols-3">
        <div>
            <div class="mb-1 text-xs text-gray-500">Search</div>
            <input
                type="text"
                placeholder="Find in front/back/type…"
                wire:model.live.debounce.300ms="q"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-gray-200"
            >
        </div>
        <div>
            <div class="mb-1 text-xs text-gray-500">Sort by</div>
            <select wire:model.live="sort" class="w-full px-3 py-2 border rounded">
                <option value="latest">Newest first</option>
                <option value="oldest">Oldest first</option>
                <option value="front_az">Front A → Z</option>
                <option value="front_za">Front Z → A</option>
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

    {{-- Action bar --}}
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500">
            {{ $items->total() }} item(s)
        </div>
        <button
            wire:click="openCreate"
            class="px-4 py-2 text-white bg-black rounded hover:opacity-90"
        >
            + New flashcard
        </button>
    </div>

    {{-- Items list (paginated) --}}
    <div class="border divide-y rounded">
        @forelse($items as $it)
            <div class="p-4 space-y-2">
                <div class="text-xs text-gray-400">
                    #{{ $it->id }} • {{ $it->created_at?->diffForHumans() }} • Type: {{ $it->type }}
                </div>

                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="font-medium break-words">Q: {{ $it->front }}</div>
                        <div class="mt-1 text-sm text-gray-700 break-words">A: {{ $it->back }}</div>
                    </div>
                    <div class="space-x-2 shrink-0">
                        <button
                            wire:click="openEdit({{ $it->id }})"
                            class="text-sm underline"
                        >Edit</button>
                        <button
                            wire:click="deleteItem({{ $it->id }})"
                            class="text-sm text-red-600 underline"
                        >Delete</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-4 text-gray-500">No items found.</div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $items->links() }}
    </div>

    {{-- ==== Modal: Create ==== --}}
    @if ($showCreate)
        <dialog open class="w-full max-w-xl p-0 rounded-lg shadow-2xl">
            <div class="p-5 space-y-3">
                <div class="text-lg font-semibold">New flashcard</div>

                <input
                    type="text"
                    placeholder="Front (question / term)…"
                    wire:model="createFront"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-gray-200"
                >
                @error('createFront') <div class="text-sm text-red-600">{{ $message }}</div> @enderror

                <textarea
                    placeholder="Back (answer / definition)…"
                    wire:model="createBack"
                    class="border rounded px-3 py-2 w-full min-h-[120px] focus:outline-none focus:ring focus:ring-gray-200"
                ></textarea>
                @error('createBack') <div class="text-sm text-red-600">{{ $message }}</div> @enderror

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button wire:click="closeCreate" class="px-4 py-2 border rounded">Cancel</button>
                    <button wire:click="storeItem" class="px-4 py-2 text-white bg-black rounded hover:opacity-90">Create</button>
                </div>
            </div>
        </dialog>
        <div class="fixed inset-0 bg-black/40"></div>
    @endif

    {{-- ==== Modal: Edit ==== --}}
    @if ($showEdit)
        <dialog open class="w-full max-w-xl p-0 rounded-lg shadow-2xl">
            <div class="p-5 space-y-3">
                <div class="text-lg font-semibold">Edit flashcard #{{ $editId }}</div>

                <input
                    type="text"
                    placeholder="Front…"
                    wire:model="editFront"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-gray-200"
                >
                @error('editFront') <div class="text-sm text-red-600">{{ $message }}</div> @enderror

                <textarea
                    placeholder="Back…"
                    wire:model="editBack"
                    class="border rounded px-3 py-2 w-full min-h-[120px] focus:outline-none focus:ring focus:ring-gray-200"
                ></textarea>
                @error('editBack') <div class="text-sm text-red-600">{{ $message }}</div> @enderror

                <div class="flex items-center justify-between pt-2">
                    <button wire:click="closeEdit" class="px-4 py-2 border rounded">Cancel</button>
                    <div class="space-x-2">
                        <button wire:click="updateItem" class="px-4 py-2 text-white bg-black rounded hover:opacity-90">Save</button>
                    </div>
                </div>
            </div>
        </dialog>
        <div class="fixed inset-0 bg-black/40"></div>
    @endif
</div>
