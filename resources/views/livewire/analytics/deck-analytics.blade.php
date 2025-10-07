{{-- Deck analytics page --}}
<div class="max-w-5xl p-6 mx-auto space-y-8">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Deck Analytics</h1>
        <a href="{{ route('analytics.decks') }}" class="text-sm text-gray-500 underline hover:text-gray-800">
            ← Back to decks
        </a>
    </div>

    <div class="text-sm text-gray-500">
        <div class="text-lg font-medium text-gray-800">{{ $deck->name }}</div>
        <div>#{{ $deck->id }} • created {{ $deck->created_at?->diffForHumans() }}</div>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
        <div class="p-4 bg-white border rounded-2xl">
            <div class="text-xs text-gray-500">Items</div>
            <div class="mt-1 text-2xl font-semibold">{{ $totalItems }}</div>
        </div>
        <div class="p-4 bg-white border rounded-2xl">
            <div class="text-xs text-gray-500">Due now</div>
            <div class="mt-1 text-2xl font-semibold text-red-600">{{ $dueNowCount }}</div>
        </div>
        <div class="p-4 bg-white border rounded-2xl">
            <div class="text-xs text-gray-500">Scheduled</div>
            <div class="mt-1 text-2xl font-semibold">{{ $scheduledCount }}</div>
        </div>
        <div class="p-4 bg-white border rounded-2xl">
            <div class="text-xs text-gray-500">Learned (rep ≥ 1)</div>
            <div class="mt-1 text-2xl font-semibold">{{ $learnedCount }}</div>
        </div>
        <div class="p-4 bg-white border rounded-2xl">
            <div class="text-xs text-gray-500">Reviewed today</div>
            <div class="mt-1 text-2xl font-semibold">{{ $reviewedTodayCount }}</div>
        </div>
        <div class="p-4 bg-white border rounded-2xl">
            <div class="text-xs text-gray-500">Avg EF</div>
            <div class="mt-1 text-2xl font-semibold">
                {{ number_format($avgEf, 2) }}
            </div>
        </div>
    </div>

    {{-- 7-day mini chart --}}
    @php
        $max = max(1, collect($dailyReviewedSeries)->max('count'));
    @endphp
    <div class="p-4 bg-white border rounded-2xl">
        <div class="flex items-center justify-between">
            <div class="text-sm font-medium">Reviews (last 7 days)</div>
            <div class="text-xs text-gray-500">based on <code>last_reviewed_at</code></div>
        </div>
        <div class="grid grid-cols-7 gap-2 mt-4">
            @foreach($dailyReviewedSeries as $d)
                @php
                    $pct = (int) round(($d['count'] / $max) * 100);
                @endphp
                <div class="flex flex-col items-center h-32">
                    <div class="w-6 h-full bg-gray-100 rounded">
                        <div class="w-full bg-gray-900 rounded" style="height: {{ $pct }}%"></div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">{{ $d['label'] }}</div>
                    <div class="text-xs font-medium">{{ $d['count'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-center justify-end gap-2">
        <a href="{{ route('decks.show', $deck) }}" class="px-4 py-2 border rounded-xl hover:bg-gray-50">Open deck</a>
        <a href="{{ route('decks.study', $deck) }}" class="px-4 py-2 text-white bg-black rounded-xl hover:opacity-90">Study</a>
    </div>
</div>
