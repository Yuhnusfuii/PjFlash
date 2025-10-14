<x-app-layout>
    <div class="container-app py-6 space-y-6">
        <h1 class="text-2xl font-bold mb-6">Danh sách Test Case theo Function</h1>

        @foreach ($tests as $module => $cases)
            <div class="card p-4 mb-4">
                <h2 class="font-semibold text-lg mb-2">{{ $module }}</h2>
                <ul class="list-disc pl-6 space-y-1 text-slate-700 dark:text-slate-300">
                    @foreach ($cases as $id => $desc)
                        <li><strong>{{ $id }}</strong> — {{ $desc }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</x-app-layout>
