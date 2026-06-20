<?php

namespace App\Livewire;

use App\Models\Media;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Médiathèque type WordPress (2 panneaux : grille + détails).
 * Deux modes :
 *  - "page"   : écran de gestion de la médiathèque (page Filament).
 *  - "picker" : modale de sélection, ouverte depuis un champ MediaPicker.
 */
class MediaLibrary extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $mode = 'page';

    /** Statepath du champ qui a demandé l'ouverture (mode picker). */
    public ?string $fieldKey = null;

    public bool $open = false;

    public string $tab = 'library';

    public string $search = '';

    public ?int $selectedId = null;

    // Champs éditables du panneau de détails.
    public string $alt = '';
    public string $title = '';
    public string $caption = '';
    public string $description = '';

    /** Fichier en cours de téléversement (mode upload). */
    public $upload = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('open-media-library')]
    public function openPicker(?string $fieldKey = null): void
    {
        if ($this->mode !== 'picker') {
            return;
        }

        $this->fieldKey = $fieldKey;
        $this->open = true;
        $this->tab = 'library';
        $this->reset(['selectedId', 'alt', 'title', 'caption', 'description', 'upload']);
        $this->resetPage();
    }

    public function close(): void
    {
        $this->open = false;
        $this->upload = null;
    }

    public function select(int $id): void
    {
        $media = Media::find($id);
        if (! $media) {
            return;
        }

        $this->selectedId = $media->id;
        $this->alt = $media->alt ?? '';
        $this->title = $media->title ?? '';
        $this->caption = $media->caption ?? '';
        $this->description = $media->description ?? '';
    }

    public function saveDetails(): void
    {
        $media = $this->selectedMedia;
        if (! $media) {
            return;
        }

        $media->update([
            'alt' => $this->alt,
            'title' => $this->title,
            'caption' => $this->caption,
            'description' => $this->description,
        ]);

        $this->dispatch('media-saved');
    }

    public function updatedUpload(): void
    {
        $this->validate([
            'upload' => ['image', 'max:4096'],
        ]);

        $path = $this->upload->storePublicly('media', 's3');

        $media = Media::create([
            'path' => $path,
            'filename' => $this->upload->getClientOriginalName(),
            'alt' => '',
        ]);

        $this->upload = null;
        $this->tab = 'library';
        $this->resetPage();
        $this->select($media->id);
    }

    public function deleteMedia(int $id): void
    {
        Media::find($id)?->delete();
        if ($this->selectedId === $id) {
            $this->selectedId = null;
        }
        $this->resetPage();
    }

    /** Insère l'image sélectionnée dans le champ demandeur (mode picker). */
    public function insert(): void
    {
        $media = $this->selectedMedia;
        if (! $media || ! $this->fieldKey) {
            return;
        }

        // Sauve d'éventuelles modifs de métadonnées avant insertion.
        $this->saveDetails();

        // Évènement navigateur global capté par le champ MediaPicker concerné.
        $this->js(
            "window.dispatchEvent(new CustomEvent('media-inserted', { detail: "
            . json_encode(['fieldKey' => $this->fieldKey, 'path' => $media->path])
            . ' }))'
        );

        $this->close();
    }

    public function getSelectedMediaProperty(): ?Media
    {
        return $this->selectedId ? Media::find($this->selectedId) : null;
    }

    public function getMediaItemsProperty()
    {
        return Media::query()
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('title', 'like', "%{$this->search}%")
                ->orWhere('filename', 'like', "%{$this->search}%")
                ->orWhere('alt', 'like', "%{$this->search}%")))
            ->latest()
            ->paginate(18);
    }

    public function render()
    {
        return view('livewire.media-library');
    }
}
