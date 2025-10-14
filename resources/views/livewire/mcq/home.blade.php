<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">MCQ – Kiểm tra nhanh</h1>
        <div class="text-sm text-slate-500">Chọn deck, chế độ & số câu → Start</div>
    </div>

    <div class="card p-4 flex flex-col md:flex-row md:items-end gap-3">
        <div class="flex-1">
            <label class="text-sm text-slate-500">Tìm deck</label>
            <input type="text" wire:model.live="q" class="w-full border rounded-lg px-3 py-2" placeholder="Nhập từ khóa...">
        </div>

        <div>
            <label class="text-sm text-slate-500">Chế độ</label>
            <select wire:model="mode" class="border rounded-lg px-3 py-2">
                <option value="mixed">Mixed</option>
                <option value="front_to_back">Front → Back</option>
                <option value="back_to_front">Back → Front</option>
            </select>
        </div>
        <div>
            <label class="text-sm text-slate-500">Số câu</label>
            <input type="number" min="5" max="50" step="1" wire:model="num" class="w-28 border rounded-lg px-3 py-2">
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        @foreach ($decks as $deck)
            <div class="card p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-semibold">{{ $deck->name }}</div>
                        <div class="text-sm text-slate-500">{{ $deck->items_count }} items</div>
                    </div>
                    <input type="radio" wire:model="deckId" value="{{ $deck->id }}">
                </div>
                <button wire:click="start" class="btn w-full">Start quiz</button>
            </div>
        @endforeach
    </div>

    <div>
        {{ $decks->links() }}
    </div>
</div>
