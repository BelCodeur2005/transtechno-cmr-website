<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * Champ de sélection d'image relié à la médiathèque WordPress-like.
 * Ouvre le composant Livewire MediaLibrary (mode picker) et reçoit le chemin choisi.
 * L'état stocké est le chemin de l'image (string).
 */
class MediaPicker extends Field
{
    protected string $view = 'forms.components.media-picker';
}
