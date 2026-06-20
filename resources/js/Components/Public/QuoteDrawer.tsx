import { onOpenQuoteDrawer, StockStatus, useQuoteCart } from '@/hooks/useQuoteCart';
import { Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

const STOCK: Record<StockStatus, { text: string; cls: string; dot: string }> = {
    en_stock: { text: 'En stock à Douala', cls: 'text-green-600', dot: 'bg-green-600' },
    en_transit: { text: 'Disponible (Importation 15 jours)', cls: 'text-orange-600', dot: 'bg-orange-600' },
    rupture: { text: 'Sur commande', cls: 'text-primary', dot: 'bg-primary-container' },
};

export default function QuoteDrawer() {
    const cart = useQuoteCart();
    const [open, setOpen] = useState(false);
    const [note, setNote] = useState('');

    useEffect(() => onOpenQuoteDrawer(() => setOpen(true)), []);

    useEffect(() => {
        const onKey = (e: KeyboardEvent) => e.key === 'Escape' && setOpen(false);
        window.addEventListener('keydown', onKey);
        return () => window.removeEventListener('keydown', onKey);
    }, []);

    if (!open) {
        return null;
    }

    return (
        <div className="fixed inset-0 z-[60] flex justify-end" role="dialog" aria-modal="true" aria-label="Ma sélection de devis">
            <div className="absolute inset-0 bg-black/50" onClick={() => setOpen(false)} aria-hidden="true" />

            <div className="relative flex h-full w-full flex-col bg-white shadow-[-4px_0_15px_rgba(0,0,0,0.1)] md:w-[400px]">
                {/* En-tête */}
                <div className="flex items-center justify-between border-b border-outline-variant p-sm">
                    <h2 className="text-headline-md font-bold text-secondary">
                        Ma Sélection ({cart.count} article{cart.count > 1 ? 's' : ''})
                    </h2>
                    <button
                        type="button"
                        onClick={() => setOpen(false)}
                        aria-label="Fermer"
                        className="flex h-10 w-10 items-center justify-center rounded text-secondary/70 hover:text-secondary"
                    >
                        <span className="material-symbols-outlined" aria-hidden="true">
                            close
                        </span>
                    </button>
                </div>

                {/* Items */}
                <div className="flex-1 space-y-sm overflow-y-auto bg-surface p-sm">
                    {cart.items.length === 0 ? (
                        <div className="py-lg text-center text-secondary">
                            <span className="material-symbols-outlined text-[48px] text-secondary/30" aria-hidden="true">
                                request_quote
                            </span>
                            <p className="mt-sm text-body-md">Votre sélection est vide.</p>
                            <p className="text-body-sm text-secondary/70">Ajoutez des produits depuis le catalogue.</p>
                        </div>
                    ) : (
                        cart.items.map((item) => {
                            const stock = STOCK[item.stockStatus] ?? STOCK.en_stock;
                            return (
                                <div key={item.id} className="flex gap-sm rounded border border-outline-variant bg-white p-sm">
                                    <div className="flex h-20 w-20 shrink-0 items-center justify-center rounded bg-surface-3">
                                        {item.image ? (
                                            <img src={item.image} alt="" className="h-full w-full object-contain p-1" />
                                        ) : (
                                            <span className="material-symbols-outlined text-3xl text-secondary/40" aria-hidden="true">
                                                medical_services
                                            </span>
                                        )}
                                    </div>
                                    <div className="flex flex-1 flex-col">
                                        <div className="flex items-start justify-between gap-1">
                                            <h3 className="text-body-sm font-bold leading-tight text-secondary">
                                                {item.name}
                                                {item.brand ? ` (${item.brand})` : ''}
                                            </h3>
                                            <button
                                                type="button"
                                                onClick={() => cart.removeItem(item.id)}
                                                aria-label={`Retirer ${item.name}`}
                                                className="shrink-0 p-1 text-secondary/50 hover:text-primary"
                                            >
                                                <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                                                    delete
                                                </span>
                                            </button>
                                        </div>
                                        <div className="mt-auto flex items-end justify-between">
                                            <div className="space-y-1">
                                                <div className="flex items-center rounded border border-outline-variant">
                                                    <button
                                                        type="button"
                                                        onClick={() => cart.updateQuantity(item.id, item.quantity - 1)}
                                                        aria-label="Diminuer"
                                                        className="flex h-7 w-7 items-center justify-center text-secondary hover:bg-surface-2"
                                                    >
                                                        <span className="material-symbols-outlined text-[16px]" aria-hidden="true">
                                                            remove
                                                        </span>
                                                    </button>
                                                    <span className="w-7 text-center text-body-sm font-medium">{item.quantity}</span>
                                                    <button
                                                        type="button"
                                                        onClick={() => cart.updateQuantity(item.id, item.quantity + 1)}
                                                        aria-label="Augmenter"
                                                        className="flex h-7 w-7 items-center justify-center text-secondary hover:bg-surface-2"
                                                    >
                                                        <span className="material-symbols-outlined text-[16px]" aria-hidden="true">
                                                            add
                                                        </span>
                                                    </button>
                                                </div>
                                                <div className={`flex items-center gap-1 text-[10px] font-bold ${stock.cls}`}>
                                                    <span className={`h-1.5 w-1.5 rounded-full ${stock.dot}`} aria-hidden="true" />
                                                    {stock.text}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })
                    )}
                </div>

                {/* Pied */}
                <div className="space-y-sm border-t border-outline-variant bg-white p-sm">
                    <div className="space-y-2">
                        <label htmlFor="drawer-note" className="text-body-sm font-bold text-secondary">
                            Besoin technique ou urgence particulière ?
                        </label>
                        <textarea
                            id="drawer-note"
                            value={note}
                            onChange={(e) => setNote(e.target.value)}
                            rows={3}
                            placeholder="Précisez votre besoin ici…"
                            className="w-full rounded border-outline-variant p-2 text-sm focus:border-primary focus:ring-primary"
                        />
                    </div>
                    <Link
                        href="/cotation"
                        data={{ note }}
                        className={`flex w-full items-center justify-center gap-2 rounded py-3 text-body-md font-bold text-white transition-colors ${
                            cart.items.length === 0 ? 'pointer-events-none bg-secondary/40' : 'bg-primary-container hover:bg-primary'
                        }`}
                    >
                        <span>FINALISER MA DEMANDE</span>
                        <span className="material-symbols-outlined text-lg" aria-hidden="true">
                            send
                        </span>
                    </Link>
                    <div className="text-center">
                        <button
                            type="button"
                            onClick={() => setOpen(false)}
                            className="text-body-sm font-bold text-secondary underline hover:text-primary"
                        >
                            Continuer mes achats
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
