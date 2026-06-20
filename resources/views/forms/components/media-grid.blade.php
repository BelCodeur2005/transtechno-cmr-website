@php
    $statePath = $getStatePath();
@endphp

<div
    x-data="{ selected: $wire.$entangle('{{ $statePath }}') }"
    class="rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40"
>
    @if ($media->isEmpty())
        <div class="flex flex-col items-center justify-center gap-2 py-10 text-gray-400">
            <x-heroicon-o-photo class="h-10 w-10" />
            <p class="text-sm">La médiathèque est vide. Téléversez une image ci-dessous.</p>
        </div>
    @else
        <div class="grid max-h-[420px] grid-cols-2 gap-3 overflow-y-auto sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($media as $m)
                <button
                    type="button"
                    @click="selected = '{{ $m->path }}'"
                    :class="selected === '{{ $m->path }}' ? 'ring-2 ring-primary-500 ring-offset-1' : 'ring-1 ring-gray-200 dark:ring-gray-700 hover:ring-primary-300'"
                    class="group relative flex flex-col overflow-hidden rounded-lg bg-white text-left transition dark:bg-gray-800"
                >
                    <span class="block h-24 w-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <img src="{{ $m->url() }}" alt="{{ $m->alt }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                    </span>
                    <span class="truncate px-2 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-200">
                        {{ $m->title ?: $m->filename }}
                    </span>

                    <span
                        x-show="selected === '{{ $m->path }}'"
                        x-cloak
                        class="absolute right-1.5 top-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-primary-600 text-white shadow"
                    >
                        <x-heroicon-m-check class="h-4 w-4" />
                    </span>
                </button>
            @endforeach
        </div>
    @endif
</div>
