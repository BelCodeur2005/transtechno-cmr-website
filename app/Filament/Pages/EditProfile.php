<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\HtmlString;

class EditProfile extends BaseEditProfile
{
    protected static string $view = 'filament.pages.edit-profile';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ── Header avatar ────────────────────────────────────────────
                Forms\Components\Placeholder::make('avatar_header')
                    ->label('')
                    ->columnSpanFull()
                    ->content(fn () => $this->avatarHeader()),

                // ── Identité ─────────────────────────────────────────────────
                Forms\Components\Section::make('Informations personnelles')
                    ->icon('heroicon-o-user-circle')
                    ->description('Votre nom d\'affichage et vos coordonnées internes.')
                    ->columns(2)
                    ->schema([
                        $this->getNameFormComponent()
                            ->label('Nom complet')
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('job_title')
                            ->label('Fonction / Poste')
                            ->placeholder('Ex. : Commercial Senior')
                            ->maxLength(100)
                            ->prefixIcon('heroicon-o-briefcase'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->placeholder('+237 6__ __ __ __')
                            ->maxLength(30)
                            ->prefixIcon('heroicon-o-phone'),
                    ]),

                // ── Compte ───────────────────────────────────────────────────
                Forms\Components\Section::make('Adresse e-mail')
                    ->icon('heroicon-o-envelope')
                    ->description('Votre identifiant de connexion à l\'administration.')
                    ->schema([
                        $this->getEmailFormComponent()
                            ->label('E-mail')
                            ->required(),
                    ]),

                // ── Sécurité ─────────────────────────────────────────────────
                Forms\Components\Section::make('Sécurité')
                    ->icon('heroicon-o-lock-closed')
                    ->description('Laissez les champs vides pour conserver votre mot de passe actuel.')
                    ->columns(2)
                    ->schema([
                        $this->getPasswordFormComponent()
                            ->label('Nouveau mot de passe'),

                        $this->getPasswordConfirmationFormComponent()
                            ->label('Confirmer le mot de passe'),
                    ]),
            ]);
    }

    private function avatarHeader(): HtmlString
    {
        $user = auth()->user();
        $name = $user?->name ?? 'Utilisateur';
        $email = $user?->email ?? '';
        $jobTitle = $user?->job_title ?? null;
        $lastSeen = $user?->last_seen_at;
        $roles = $user?->getRoleNames() ?? collect();

        // Initiales : jusqu'à 2 lettres
        $words = preg_split('/\s+/', trim($name));
        $initials = strtoupper(
            count($words) >= 2
                ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1)
                : mb_substr($name, 0, 2)
        );

        // Toutes les valeurs interpolées dans le HTML doivent être échappées.
        $safeName     = e($name);
        $safeInitials = e($initials);
        $safeEmail    = e($email);
        $safeRole     = e($jobTitle ?? ($roles->isNotEmpty() ? $this->roleLabel($roles->first()) : 'Collaborateur'));

        $lastSeenHtml = $lastSeen
            ? '<span class="tt-last-seen">Dernière activité : ' . e($lastSeen->diffForHumans()) . '</span>'
            : '';

        $roleBadgeHtml = '';
        if ($roles->isNotEmpty()) {
            $roleBadgeHtml = '<span class="tt-role-badge">' . e($this->roleLabel($roles->first())) . '</span>';
        }

        return new HtmlString(<<<HTML
<div class="tt-profile-header">
    <div class="tt-avatar-wrap">
        <div class="tt-avatar">{$safeInitials}</div>
        <div class="tt-avatar-ring"></div>
    </div>
    <div class="tt-header-info">
        <h2 class="tt-header-name">{$safeName}</h2>
        <div class="tt-header-meta">
            <span class="tt-job-title">{$safeRole}</span>
            {$roleBadgeHtml}
        </div>
        <span class="tt-header-email">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="14" height="14" style="display:inline;vertical-align:middle;margin-right:4px;opacity:.7"><path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/><path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/></svg>
            {$safeEmail}
        </span>
        {$lastSeenHtml}
    </div>
</div>
HTML);
    }

    private function roleLabel(string $role): string
    {
        return match ($role) {
            'super_admin'      => 'Super Administrateur',
            'commercial'       => 'Commercial',
            'technicien_sav'   => 'Technicien SAV',
            'editeur_contenu'  => 'Éditeur de contenu',
            'magasinier'       => 'Magasinier',
            default            => ucfirst(str_replace('_', ' ', $role)),
        };
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ne mettre à jour le mot de passe que s'il a été renseigné.
        if (blank($data['password'] ?? null)) {
            unset($data['password'], $data['password_confirmation']);
        }

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Profil mis à jour avec succès.';
    }
}
