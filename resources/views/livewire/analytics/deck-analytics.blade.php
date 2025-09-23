<div class="max-w-5xl p-6 mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Analytics — {{ $deck->name }}</h1>
        <a href="{{ route('decks.show', $deck) }}" class="text-sm underline">← Back to deck</a>
    </div>

    {{-- KPI cards --}}
    <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-6">
        <div class="p-4 border rounded">
            <div class="text-xs text-gray-500">Total items</div>
            <div class="text-2xl font-semibold">{{ $total }}</div>
        </div>
        <div class="p-4 border rounded">
            <div class="text-xs text-gray-500">Due now</div>
            <div class="text-2xl font-semibold">{{ $dueNow }}</div>
        </div>
        <div class="p-4 border rounded">
            <div class="text-xs text-gray-500">Scheduled</div>
            <div class="text-2xl font-semibold">{{ $scheduled }}</div>
        </div>
        <div class="p-4 border rounded">
            <div class="text-xs text-gray-500">Learned (rep≥1)</div>
            <div class="text-2xl font-semibold">{{ $learned }}</div>
        </div>
        <div class="p-4 border rounded">
            <div class="text-xs text-gray-500">Avg EF</div>
            <div class="text-2xl font-semibold">{{ number_format($avgEf, 2) }}</div>
        </div>
        <div class="p-4 border rounded">
            <div class="text-xs text-gray-500">Reviewed today</div>
            <div class="text-2xl font-semibold">{{ $reviewedToday }}</div>
        </div>
    </div>

    {{-- Mini bar chart (7 days) --}}
    @php
        $max = max(1, collect($daily)->max('count'));
    @endphp
    <div class="p-4 border rounded">
        <div class="mb-4 text-sm font-medium">Reviews in the last 7 days</div>
        <div class="grid items-end h-48 grid-cols-7 gap-3">
            @foreach($daily as $d)
                @php
                    $h = intval(($d['count'] / $max) * 100); // %
                @endphp
                <div class="flex flex-col items-center gap-2">
                    <div class="w-8 bg-gray-800 rounded" style="height: {{ $h }}%"></div>
                    <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::of($d['date'])->afterLast('-') }}</div>
                    <div class="text-xs">{{ $d['count'] }}</div>
                </div>
            @endforeach
        </div>
        <div class="mt-2 text-xs text-gray-500">* bar height is relative to the max of last 7 days (max={{ $max }})</div>
    </div>
</div>
