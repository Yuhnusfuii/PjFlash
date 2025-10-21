<div class="space-y-6">
  <div class="flex items-center justify-between gap-3">
    <h1 class="y-section-title">Your decks</h1>
    <a href="{{ route('decks.create') }}" class="y-btn y-btn--brand">+ New deck</a>
  </div>

  <x-ui-card>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="sm:col-span-1">
        <x-ui-input label="Search" name="q" wire:model.live="q" placeholder="Nhập tên deck..." />
      </div>
      <div class="sm:col-span-1 flex items-end">
        <x-ui-button wire:click="$refresh">Refresh</x-ui-button>
      </div>
    </div>
  </x-ui-card>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($decks as $d)
      <x-ui-card class="flex flex-col gap-3">
        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="font-semibold">{{ $d->name }}</div>
            <div class="y-sub">{{ $d->items_count }} items</div>
          </div>
          @if($d->is_public)
            <span class="text-xs rounded-full px-2 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-200">Public</span>
          @endif
        </div>

        <div class="flex gap-2 mt-2">
          <a href="{{ route('decks.show',$d) }}" class="y-btn y-btn--brand grow">Open</a>
          <a href="{{ route('decks.study',$d) }}" class="y-btn border border-[var(--card-br)] grow">Study</a>
        </div>
      </x-ui-card>
    @empty
      <x-ui-card>Chưa có deck nào.</x-ui-card>
    @endforelse
  </div>

  <div>{{ $decks->links() }}</div>
</div>
