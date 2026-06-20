<div>
    @php $isPicker = $this->mode === 'picker'; @endphp

    <style>
        .mlib * { box-sizing: border-box; }
        .mlib-overlay { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; background: rgba(2, 6, 23, .6); padding: 1rem; }
        .mlib-panel { display: flex; flex-direction: column; overflow: hidden; background: #fff; border-radius: .75rem; }
        .mlib-panel.modal { width: 100%; max-width: 72rem; height: 88vh; box-shadow: 0 25px 50px -12px rgba(0,0,0,.4); }
        .mlib-panel.page { height: 76vh; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .mlib-head { display: flex; align-items: center; justify-content: space-between; padding: .75rem 1.25rem; border-bottom: 1px solid #e5e7eb; }
        .mlib-head h2 { font-size: 1rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: .5rem; }
        .mlib-x { border: 0; background: transparent; cursor: pointer; color: #94a3b8; padding: .35rem; border-radius: 9999px; display: flex; }
        .mlib-x:hover { background: #f1f5f9; color: #334155; }
        .mlib-tabs { display: flex; gap: .25rem; padding: 0 1.25rem; border-bottom: 1px solid #e5e7eb; }
        .mlib-tab { border: 0; background: transparent; cursor: pointer; padding: .65rem .75rem; font-size: .875rem; font-weight: 600; color: #64748b; border-bottom: 2px solid transparent; }
        .mlib-tab.active { color: #004A80; border-bottom-color: #004A80; }
        .mlib-body { display: flex; min-height: 0; flex: 1; }
        .mlib-left { display: flex; flex-direction: column; min-width: 0; flex: 1; }
        .mlib-search { padding: .75rem; border-bottom: 1px solid #f1f5f9; position: relative; }
        .mlib-search input { width: 100%; border: 1px solid #cbd5e1; border-radius: .5rem; padding: .5rem .75rem .5rem 2.25rem; font-size: .875rem; }
        .mlib-search input:focus { outline: none; border-color: #004A80; box-shadow: 0 0 0 1px #004A80; }
        .mlib-search svg { position: absolute; left: 1.4rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #94a3b8; }
        .mlib-grid-wrap { flex: 1; min-height: 0; overflow-y: auto; padding: .75rem; }
        .mlib-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .5rem; }
        @media (min-width: 640px) { .mlib-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        @media (min-width: 1024px) { .mlib-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); } }
        .mlib-cell { position: relative; aspect-ratio: 1 / 1; overflow: hidden; border-radius: .5rem; cursor: pointer; border: 0; padding: 0; background: #f1f5f9; box-shadow: 0 0 0 2px transparent; transition: box-shadow .15s; }
        .mlib-cell:hover { box-shadow: 0 0 0 2px #7dd3fc; }
        .mlib-cell.sel { box-shadow: 0 0 0 3px #004A80; }
        .mlib-cell img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .mlib-check { position: absolute; top: .25rem; right: .25rem; width: 1.25rem; height: 1.25rem; border-radius: 9999px; background: #004A80; color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,.3); }
        .mlib-aside { width: 19rem; flex-shrink: 0; border-left: 1px solid #e5e7eb; background: #f8fafc; display: flex; flex-direction: column; }
        .mlib-aside-body { flex: 1; min-height: 0; overflow-y: auto; padding: 1rem; }
        .mlib-aside .cap { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #64748b; margin-bottom: .5rem; }
        .mlib-prev { overflow: hidden; border-radius: .5rem; border: 1px solid #e5e7eb; }
        .mlib-prev img { width: 100%; height: 9rem; object-fit: cover; display: block; }
        .mlib-fname { margin-top: .5rem; font-size: .875rem; font-weight: 600; color: #1f2937; word-break: break-word; }
        .mlib-meta { font-size: .75rem; color: #64748b; }
        .mlib-field { margin-top: .65rem; }
        .mlib-field label { display: block; font-size: .72rem; font-weight: 500; color: #475569; margin-bottom: .2rem; }
        .mlib-field input, .mlib-field textarea { width: 100%; border: 1px solid #cbd5e1; border-radius: .375rem; padding: .4rem .5rem; font-size: .8rem; }
        .mlib-field input:focus, .mlib-field textarea:focus { outline: none; border-color: #004A80; box-shadow: 0 0 0 1px #004A80; }
        .mlib-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .5rem; padding: 1.5rem; text-align: center; color: #94a3b8; }
        .mlib-foot { border-top: 1px solid #e5e7eb; padding: .75rem; }
        .mlib-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; font-weight: 700; border: 0; cursor: pointer; border-radius: .5rem; }
        .mlib-btn-primary { width: 100%; padding: .65rem; font-size: .875rem; color: #fff; background: #004A80; }
        .mlib-btn-primary:hover { background: #003a66; }
        .mlib-btn-primary:disabled { background: #cbd5e1; cursor: not-allowed; }
        .mlib-btn-sm { padding: .35rem .6rem; font-size: .72rem; }
        .mlib-btn-soft { background: #e2e8f0; color: #334155; }
        .mlib-btn-soft:hover { background: #cbd5e1; }
        .mlib-link { display: inline-flex; align-items: center; gap: .25rem; font-size: .72rem; font-weight: 600; color: #004A80; text-decoration: none; padding: .35rem .4rem; border-radius: .375rem; }
        .mlib-link:hover { background: #eff6ff; }
        .mlib-del { margin-top: .5rem; display: inline-flex; align-items: center; gap: .25rem; font-size: .72rem; font-weight: 600; color: #E31E24; background: none; border: 0; cursor: pointer; }
        .mlib-del:hover { text-decoration: underline; }
        .mlib-drop { flex: 1; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
        .mlib-drop label { width: 100%; height: 100%; min-height: 16rem; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .6rem; text-align: center; border: 2px dashed #cbd5e1; border-radius: .75rem; background: #f8fafc; color: #64748b; transition: .15s; }
        .mlib-drop label:hover { border-color: #38bdf8; background: #f0f9ff; }
        .mlib-svg-lg { width: 2.5rem; height: 2.5rem; }
        [x-cloak] { display: none !important; }
    </style>

    <div
        class="mlib {{ $isPicker ? 'mlib-overlay' : '' }}"
        @if ($isPicker)
            x-data="{ open: @entangle('open') }"
            x-show="open"
            x-cloak
            x-transition.opacity
            x-on:keydown.escape.window="open = false"
        @endif
    >
        <div class="mlib-panel {{ $isPicker ? 'modal' : 'page' }}" @if ($isPicker) x-show="open" x-transition @endif>

            {{-- En-tête --}}
            <div class="mlib-head">
                <h2>
                    <x-heroicon-o-photo style="width:1.25rem;height:1.25rem;color:#004A80" />
                    {{ $isPicker ? 'Médiathèque — Sélectionner une image' : 'Médiathèque' }}
                </h2>
                @if ($isPicker)
                    <button type="button" class="mlib-x" wire:click="close" aria-label="Fermer">
                        <x-heroicon-o-x-mark style="width:1.25rem;height:1.25rem" />
                    </button>
                @endif
            </div>

            {{-- Onglets --}}
            <div class="mlib-tabs">
                <button type="button" wire:click="$set('tab', 'library')" class="mlib-tab {{ $tab === 'library' ? 'active' : '' }}">Bibliothèque de médias</button>
                <button type="button" wire:click="$set('tab', 'upload')" class="mlib-tab {{ $tab === 'upload' ? 'active' : '' }}">Téléverser des fichiers</button>
            </div>

            {{-- Corps --}}
            <div class="mlib-body">
                <div class="mlib-left">
                    @if ($tab === 'upload')
                        <div class="mlib-drop"
                            x-data="{ uploading: false }"
                            x-on:livewire-upload-start="uploading = true"
                            x-on:livewire-upload-finish="uploading = false"
                            x-on:livewire-upload-error="uploading = false">
                            <label>
                                <span x-show="!uploading" style="display:flex;flex-direction:column;align-items:center;gap:.5rem">
                                    <x-heroicon-o-arrow-up-tray class="mlib-svg-lg" />
                                    <span style="font-size:.875rem;font-weight:600">Glissez une image ici ou cliquez pour parcourir</span>
                                    <span style="font-size:.75rem;color:#94a3b8">PNG, JPG, WEBP — jusqu'à 4 Mo</span>
                                </span>
                                <span x-show="uploading" x-cloak style="display:flex;flex-direction:column;align-items:center;gap:.5rem;color:#004A80">
                                    <svg class="mlib-svg-lg" style="animation:spin 1s linear infinite" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"/><path fill="currentColor" opacity=".75" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                    <span style="font-size:.875rem;font-weight:600">Téléversement…</span>
                                </span>
                                <input type="file" wire:model="upload" accept="image/*" style="display:none" />
                            </label>
                        </div>
                        @error('upload') <p style="padding:0 1.5rem 1rem;color:#E31E24;font-size:.875rem">{{ $message }}</p> @enderror
                    @else
                        <div class="mlib-search">
                            <x-heroicon-o-magnifying-glass />
                            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Rechercher un média…" />
                        </div>
                        <div class="mlib-grid-wrap">
                            @if ($this->mediaItems->isEmpty())
                                <div class="mlib-empty" style="height:100%">
                                    <x-heroicon-o-photo style="width:2.5rem;height:2.5rem" />
                                    <p style="font-size:.875rem">Aucun média. Utilisez l'onglet « Téléverser des fichiers ».</p>
                                </div>
                            @else
                                <div class="mlib-grid">
                                    @foreach ($this->mediaItems as $item)
                                        <button type="button" wire:key="m-{{ $item->id }}" wire:click="select({{ $item->id }})" class="mlib-cell {{ $selectedId === $item->id ? 'sel' : '' }}">
                                            <img src="{{ $item->url() }}" alt="{{ $item->alt }}" loading="lazy" />
                                            @if ($selectedId === $item->id)
                                                <span class="mlib-check"><x-heroicon-m-check style="width:.85rem;height:.85rem" /></span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                                <div style="margin-top:.75rem">{{ $this->mediaItems->links() }}</div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Détails --}}
                <aside class="mlib-aside">
                    @if ($this->selectedMedia)
                        <div class="mlib-aside-body">
                            <p class="cap">Détails du média</p>
                            <div class="mlib-prev"><img src="{{ $this->selectedMedia->url() }}" alt="{{ $this->selectedMedia->alt }}" /></div>
                            <p class="mlib-fname">{{ $this->selectedMedia->filename }}</p>
                            <p class="mlib-meta">{{ $this->selectedMedia->created_at?->format('d/m/Y') }}@if ($this->selectedMedia->humanSize()) · {{ $this->selectedMedia->humanSize() }}@endif</p>

                            <div class="mlib-field"><label>Texte alternatif</label><input type="text" wire:model="alt" /></div>
                            <div class="mlib-field"><label>Titre</label><input type="text" wire:model="title" /></div>
                            <div class="mlib-field"><label>Légende</label><input type="text" wire:model="caption" /></div>
                            <div class="mlib-field"><label>Description</label><textarea rows="3" wire:model="description"></textarea></div>

                            <div style="display:flex;align-items:center;gap:.5rem;margin-top:.65rem">
                                <button type="button" wire:click="saveDetails" class="mlib-btn mlib-btn-sm mlib-btn-soft"><x-heroicon-m-check style="width:1rem;height:1rem" /> Enregistrer</button>
                                <a href="{{ \App\Filament\Resources\MediaResource::getUrl('edit', ['record' => $this->selectedMedia->id]) }}" class="mlib-link"><x-heroicon-m-scissors style="width:1rem;height:1rem" /> Recadrer / redimensionner</a>
                            </div>
                            @if (! $isPicker)
                                <button type="button" wire:click="deleteMedia({{ $this->selectedMedia->id }})" wire:confirm="Supprimer définitivement ce média ?" class="mlib-del"><x-heroicon-m-trash style="width:1rem;height:1rem" /> Supprimer</button>
                            @endif
                        </div>
                    @else
                        <div class="mlib-empty">
                            <x-heroicon-o-cursor-arrow-rays style="width:2rem;height:2rem" />
                            <p style="font-size:.875rem">Sélectionnez un média pour voir ses détails.</p>
                        </div>
                    @endif

                    @if ($isPicker)
                        <div class="mlib-foot">
                            <button type="button" wire:click="insert" @disabled(! $selectedId) class="mlib-btn mlib-btn-primary">
                                <x-heroicon-m-check-circle style="width:1.25rem;height:1.25rem" /> Insérer l'image
                            </button>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>

    {{-- Médiathèque ↔ éditeur de texte riche (Trix) : icône dans la barre d'outils + insertion. --}}
    <script>
        if (! window.__mediaRichEditorBound) {
            window.__mediaRichEditorBound = true;

            // 1) Injecte une icône « image » dans la barre d'outils de chaque éditeur Trix.
            //    Au clic, elle mémorise l'éditeur actif et ouvre la médiathèque.
            // Icône SVG « image » (taille des icônes de la barre d'outils Filament).
            var MEDIA_ICON = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>';

            // Filament v3 remplace la barre d'outils Trix par la sienne (trix-toolbar.fi-fo-rich-editor-toolbar).
            var injectInto = function (toolbar) {
                if (! toolbar || toolbar.querySelector('.media-lib-trix-btn')) return;
                var group = toolbar.querySelector('[data-trix-button-group]') || toolbar.querySelector('div');
                if (! group) return;
                // Retrouve l'éditeur associé à cette barre d'outils (pour cibler la bonne insertion).
                var editor = (toolbar.id && document.querySelector('trix-editor[toolbar="' + toolbar.id + '"]'))
                    || document.querySelector('trix-editor');

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'fi-fo-rich-editor-toolbar-btn media-lib-trix-btn flex h-8 min-w-[2rem] cursor-pointer items-center justify-center rounded-lg text-gray-700 transition duration-75 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5';
                btn.title = 'Insérer une image depuis la médiathèque';
                btn.innerHTML = MEDIA_ICON;
                btn.addEventListener('click', function () {
                    window.__mediaTargetTrix = editor;
                    window.dispatchEvent(new CustomEvent('open-media-library', { detail: { fieldKey: 'richeditor' } }));
                });
                group.appendChild(btn);
            };

            var scanToolbars = function () {
                document.querySelectorAll('trix-toolbar.fi-fo-rich-editor-toolbar').forEach(injectInto);
            };

            document.addEventListener('trix-initialize', scanToolbars);
            scanToolbars();
            // Les barres d'outils peuvent être (re)montées par Livewire après coup.
            new MutationObserver(scanToolbars).observe(document.body, { childList: true, subtree: true });

            // 2) Insère l'image choisie dans l'éditeur actif (celui dont l'icône a été cliquée).
            window.addEventListener('media-inserted', function (e) {
                if (! e.detail || e.detail.fieldKey !== 'richeditor') return;
                var path = e.detail.path || '';
                var url = (path.startsWith('http') || path.startsWith('/')) ? path : '/media-proxy/' + path;
                var editorEl = window.__mediaTargetTrix || document.querySelector('trix-editor');
                if (editorEl && editorEl.editor) {
                    editorEl.editor.insertHTML('<img src="' + url + '" alt="">');
                    editorEl.focus();
                }
            });
        }
    </script>
</div>
