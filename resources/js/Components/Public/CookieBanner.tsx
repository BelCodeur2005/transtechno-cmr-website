import { Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

const STORAGE_KEY = 'ttc_cookie_consent';

export default function CookieBanner() {
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        if (!localStorage.getItem(STORAGE_KEY)) {
            setVisible(true);
        }
    }, []);

    const accept = () => {
        localStorage.setItem(STORAGE_KEY, 'accepted');
        setVisible(false);
    };

    const refuse = () => {
        localStorage.setItem(STORAGE_KEY, 'refused');
        setVisible(false);
    };

    if (!visible) return null;

    return (
        <div
            role="dialog"
            aria-label="Gestion des cookies"
            aria-live="polite"
            className="fixed bottom-0 left-0 right-0 z-50 border-t-2 border-brand-red bg-dark-navy text-white shadow-2xl"
        >
            <div className="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 md:flex-row md:items-center md:justify-between md:px-margin">
                <p className="text-body-sm text-white/80">
                    Ce site utilise des cookies pour améliorer votre expérience de navigation et mesurer notre
                    audience.{' '}
                    <Link
                        href="/politique-confidentialite#cookies"
                        className="underline underline-offset-2 hover:text-white"
                    >
                        En savoir plus
                    </Link>
                    .
                </p>
                <div className="flex shrink-0 gap-3">
                    <button
                        onClick={refuse}
                        className="rounded border border-white/30 px-4 py-2 text-body-sm text-white/70 transition-colors hover:border-white/60 hover:text-white"
                    >
                        Refuser
                    </button>
                    <button
                        onClick={accept}
                        className="rounded bg-primary-container px-5 py-2 text-body-sm font-semibold text-white transition-colors hover:bg-primary"
                    >
                        Accepter
                    </button>
                </div>
            </div>
        </div>
    );
}
