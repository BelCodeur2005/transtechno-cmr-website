<x-filament-panels::page>

    <style>
        /* ── Profile header card ─────────────────────────────────── */
        .tt-profile-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: linear-gradient(135deg, #004A80 0%, #003660 60%, #002348 100%);
            border-radius: 1rem;
            padding: 2rem 2.5rem;
            margin-bottom: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Motif de fond subtil */
        .tt-profile-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 80% 20%, rgba(227,30,36,0.12) 0%, transparent 60%),
                              radial-gradient(circle at 10% 80%, rgba(255,255,255,0.04) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Trait rouge en bas */
        .tt-profile-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 2.5rem;
            right: 2.5rem;
            height: 3px;
            background: linear-gradient(90deg, #E31E24 0%, #ba0013 60%, transparent 100%);
            border-radius: 2px 2px 0 0;
        }

        /* ── Avatar ──────────────────────────────────────────────── */
        .tt-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }

        .tt-avatar {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            border: 2.5px solid rgba(255,255,255,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            font-size: 1.875rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.02em;
            position: relative;
            z-index: 1;
        }

        /* Anneau rouge tournant en arrière-plan de l'avatar */
        .tt-avatar-ring {
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            border: 2px solid transparent;
            border-top-color: #E31E24;
            border-right-color: rgba(227,30,36,0.35);
            animation: none;
        }

        /* ── Infos texte ─────────────────────────────────────────── */
        .tt-header-info {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            position: relative;
            z-index: 1;
        }

        .tt-header-name {
            font-family: 'Inter', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.015em;
            margin: 0;
            line-height: 1.2;
        }

        .tt-header-meta {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .tt-job-title {
            font-size: 0.9375rem;
            color: rgba(255,255,255,0.75);
            font-weight: 500;
        }

        .tt-role-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.15rem 0.65rem;
            background: rgba(227,30,36,0.25);
            border: 1px solid rgba(227,30,36,0.5);
            border-radius: 999px;
            font-size: 0.6875rem;
            font-weight: 600;
            color: #fca5a5;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .tt-header-email {
            font-size: 0.8125rem;
            color: rgba(255,255,255,0.5);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .tt-last-seen {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.35);
        }

        /* ── Sections form ───────────────────────────────────────── */
        .fi-section-header-wrapper {
            border-bottom: 1px solid rgb(229 231 235);
        }

        /* ── Dark mode ───────────────────────────────────────────── */
        @media (prefers-color-scheme: dark) {
            .tt-profile-header {
                background: linear-gradient(135deg, #002B4B 0%, #001e35 60%, #001428 100%);
            }
            .fi-section-header-wrapper {
                border-bottom-color: rgb(55 65 81);
            }
        }

        /* ── Mobile ──────────────────────────────────────────────── */
        @media (max-width: 640px) {
            .tt-profile-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 1.5rem;
            }
            .tt-profile-header::after {
                left: 1.5rem;
                right: 1.5rem;
            }
            .tt-header-name { font-size: 1.25rem; }
            .tt-avatar { width: 60px; height: 60px; font-size: 1.5rem; }
        }
    </style>

    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

</x-filament-panels::page>
