<div class="max-w-4xl mx-auto p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $deck->name }}</h1>
        <a href="{{ route('decks.index') }}" class="text-sm underline">← Back</a>
    </div>

    {{-- Flash --}}
    @if (session('ok'))
        <div class="p-3 rounded bg-green-100 text-green-800 text-sm">{{ session('ok') }}</div>
    @endif

    {{-- Create new item --}}
    <div class="space-y-2 border rounded p-4">
        <div class="font-medium">Add flashcard</div>
        <input
            type="text"
            placeholder="Front (question / term)..."
            wire:model="front"
            class="border rounded px-3 py-2 w-full focus:outline-none focus:ring focus:ring-gray-200"
        >
        @error('front') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

        <textarea
            placeholder="Back (answer / definition)..."
            wire:model="back"
            class="border rounded px-3 py-2 w-full min-h-[120px] focus:outline-none focus:ring focus:ring-gray-200"
        ></textarea>
        @error('back')  <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

        <div class="flex items-center justify-end">
            <button
                wire:click="addItem"
                class="px-4 py-2 rounded bg-black text-white hover:opacity-90"
            >Add</button>
        </div>
    </div>

    {{-- Items list --}}
    <div class="border rounded divide-y">
        @forelse($deck->items as $it)
            <div class="p-4 space-y-2">
                {{-- Meta --}}
                <div class="text-xs text-gray-400">
                    #{{ $it->id }} • {{ $it->created_at?->diffForHumans() }} • Type: {{ $it->type }}
                </div>

                {{-- View mode --}}
                @if ($editingId !== $it->id)
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="font-medium break-words">Q: {{ $it->front }}</div>
                            <div class="text-gray-700 text-sm mt-1 break-words">A: {{ $it->back }}</div>
                        </div>
                        <div class="shrink-0 space-x-2">
                            <button
                                wire:click="startEditItem({{ $it->id }})"
                                class="text-sm underline"
                                title="Edit this item"
                            >Edit</button>
                            <button
                                wire:click="deleteItem({{ $it->id }})"
                                class="text-red-600 text-sm underline"
                                title="Delete this item"
                            >Delete</button>
                        </div>
                    </div>
                @else
                    {{-- Edit mode --}}
                    <div class="space-y-2 border rounded p-3 bg-gray-50">
                        <input
                            type="text"
                            placeholder="Front..."
                            wire:model="editFront"
                            class="border rounded px-3 py-2 w-full focus:outline-none focus:ring focus:ring-gray-200"
                        >
                        @error('editFront') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

                        <textarea
                            placeholder="Back..."
                            wire:model="editBack"
                            class="border rounded px-3 py-2 w-full min-h-[100px] focus:outline-none focus:ring focus:ring-gray-200"
                        ></textarea>
                        @error('editBack')  <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

                        <div class="flex items-center justify-end gap-2">
                            <button
                                wire:click="saveEditItem"
                                class="px-4 py-2 rounded bg-black text-white hover:opacity-90"
                            >Save</button>
                            <button
                                wire:click="cancelEditItem"
                                class="px-4 py-2 rounded border"
                            >Cancel</button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-4 text-gray-500">No items yet.</div>
        @endforelse
    </div>
</div>
