<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            get previewUrl() {
                if (! this.state) return null;
                if (this.state.startsWith('http') || this.state.startsWith('/')) return this.state;
                return '/media-proxy/' + this.state;
            },
        }"
        x-on:media-inserted.window="if ($event.detail.fieldKey === '{{ $getStatePath() }}') { state = $event.detail.path }"
        class="space-y-3"
    >
        {{-- Aperçu --}}
        <template x-if="previewUrl">
            <div class="relative inline-block overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <img :src="previewUrl" alt="Aperçu de l'image sélectionnée" class="h-44 w-auto max-w-full object-cover" />
            </div>
        </template>
        <template x-if="! previewUrl">
            <div class="flex h-44 w-full max-w-md flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 text-gray-400 dark:border-gray-600 dark:bg-gray-800/40">
                <x-heroicon-o-photo class="h-9 w-9" />
                <span class="text-sm">Aucune image définie</span>
            </div>
        </template>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                x-on:click="$dispatch('open-media-library', { fieldKey: '{{ $getStatePath() }}' })"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700"
            >
                <x-heroicon-o-photo class="h-4 w-4" />
                Choisir dans la médiathèque
            </button>

            <button
                type="button"
                x-show="previewUrl"
                x-on:click="state = null"
                class="inline-flex items-center gap-1 text-sm font-semibold text-gray-500 transition hover:text-danger-600"
            >
                <x-heroicon-o-x-mark class="h-4 w-4" />
                Retirer
            </button>
        </div>
    </div>
</x-dynamic-component>
