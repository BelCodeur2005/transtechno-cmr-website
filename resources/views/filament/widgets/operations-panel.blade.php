<div class="flex flex-col gap-6">

    {{-- ── Alertes Stock ── --}}
    <x-filament::section>
        <x-slot name="heading">
            <span class="flex items-center gap-2">
                <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-500" />
                Alertes Stock
            </span>
        </x-slot>

        @if (empty($stockAlerts))
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune alerte — tous les stocks sont au vert.</p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-white/10">
                @foreach ($stockAlerts as $item)
                    <li class="flex items-start gap-3 py-2.5 first:pt-0 last:pb-0">
                        <span @class([
                            'mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                            'bg-danger-50 text-danger-600 dark:bg-danger-400/10' => $item['critical'],
                            'bg-warning-50 text-warning-600 dark:bg-warning-400/10' => ! $item['critical'],
                        ])>
                            <x-heroicon-m-cube class="h-4 w-4" />
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</p>
                            <p class="text-xs {{ $item['critical'] ? 'text-danger-600' : 'text-warning-600' }}">
                                {{ $item['label'] }}
                                @if ($item['reference']) · {{ $item['reference'] }} @endif
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <x-slot name="footerActions">
            <x-filament::button
                tag="a"
                :href="\App\Filament\Resources\ProductResource::getUrl()"
                size="sm"
                color="gray"
                icon="heroicon-m-archive-box"
            >
                Gérer les stocks
            </x-filament::button>
        </x-slot>
    </x-filament::section>

    {{-- ── Activité Récente ── --}}
    <x-filament::section>
        <x-slot name="heading">
            <span class="flex items-center gap-2">
                <x-heroicon-o-bolt class="h-5 w-5 text-primary-500" />
                Activité Récente
            </span>
        </x-slot>

        @if (empty($activity))
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune activité récente.</p>
        @else
            <ul class="space-y-3.5">
                @foreach ($activity as $event)
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-white/10 {{ $event['color'] }}">
                            <x-filament::icon :icon="$event['icon']" class="h-4 w-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm leading-snug text-gray-700 dark:text-gray-200">{!! $event['text'] !!}</p>
                            <p class="mt-0.5 text-xs text-gray-400">{{ $event['ago'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>

</div>
