<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Paramètres';
    protected static ?string $navigationGroup = 'Système';
    protected static ?int $navigationSort = 90;
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'phone_1'       => Setting::get('phone_1'),
            'phone_2'       => Setting::get('phone_2'),
            'phone_3'       => Setting::get('phone_3'),
            'whatsapp'      => Setting::get('whatsapp'),
            'email'         => Setting::get('email'),
            'address_line1' => Setting::get('address_line1'),
            'address_line2' => Setting::get('address_line2'),
            'address_city'  => Setting::get('address_city'),
            'facebook_url'  => Setting::get('facebook_url'),
            'linkedin_url'  => Setting::get('linkedin_url'),
            'company_name'  => Setting::get('company_name'),
            'rccm'          => Setting::get('rccm'),
            'niu'           => Setting::get('niu'),
            'rib'           => Setting::get('rib'),
            'cnps'          => Setting::get('cnps'),
            'host_name'     => Setting::get('host_name'),
            'host_address'  => Setting::get('host_address'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contact')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('phone_1')
                            ->label('Téléphone 1')
                            ->placeholder('+237 674 64 53 54'),
                        Forms\Components\TextInput::make('phone_2')
                            ->label('Téléphone 2')
                            ->placeholder('+237 699 67 40 36'),
                        Forms\Components\TextInput::make('phone_3')
                            ->label('Téléphone fixe')
                            ->placeholder('+237 233 37 33 62'),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('Numéro WhatsApp (sans + ni espaces)')
                            ->placeholder('237699674036')
                            ->helperText('Ex : 237699674036 — utilisé pour les liens wa.me'),
                        Forms\Components\TextInput::make('email')
                            ->label('Email de contact')
                            ->email()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Adresse')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\TextInput::make('address_line1')
                            ->label('Ligne 1')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('address_line2')
                            ->label('Ligne 2')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('address_city')
                            ->label('Ville / Pays'),
                    ]),

                Forms\Components\Section::make('Réseaux sociaux')
                    ->icon('heroicon-o-share')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('facebook_url')
                            ->label('Page Facebook (URL complète)')
                            ->url(),
                        Forms\Components\TextInput::make('linkedin_url')
                            ->label('Page LinkedIn (URL complète)')
                            ->url(),
                    ]),

                Forms\Components\Section::make('Informations légales')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Raison sociale'),
                        Forms\Components\TextInput::make('rccm')
                            ->label('RCCM'),
                        Forms\Components\TextInput::make('niu')
                            ->label('NIU (numéro fiscal)'),
                        Forms\Components\TextInput::make('rib')
                            ->label('RIB bancaire'),
                        Forms\Components\TextInput::make('cnps')
                            ->label('N° CNPS'),
                        Forms\Components\TextInput::make('host_name')
                            ->label('Hébergeur — Nom'),
                        Forms\Components\TextInput::make('host_address')
                            ->label('Hébergeur — Adresse')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $groups = [
            'phone_1'       => 'contact',
            'phone_2'       => 'contact',
            'phone_3'       => 'contact',
            'whatsapp'      => 'contact',
            'email'         => 'contact',
            'address_line1' => 'address',
            'address_line2' => 'address',
            'address_city'  => 'address',
            'facebook_url'  => 'social',
            'linkedin_url'  => 'social',
            'company_name'  => 'legal',
            'rccm'          => 'legal',
            'niu'           => 'legal',
            'rib'           => 'legal',
            'cnps'          => 'legal',
            'host_name'     => 'legal',
            'host_address'  => 'legal',
        ];

        foreach ($state as $key => $value) {
            Setting::set($key, $value, $groups[$key] ?? 'general');
        }

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer les paramètres')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }
}
