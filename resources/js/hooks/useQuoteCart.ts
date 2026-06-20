import { useCallback, useEffect, useState } from 'react';

/** Un article du panier de devis (équipement ou réactif). */
export type StockStatus = 'en_stock' | 'en_transit' | 'rupture';

export interface QuoteCartItem {
    id: number;
    name: string;
    slug: string;
    reference: string | null;
    brand: string | null;
    image: string | null;
    stockStatus: StockStatus;
    quantity: number;
}

const STORAGE_KEY = 'transtechno_quote_cart';
const EVENT_NAME = 'quote-cart-updated';
const OPEN_DRAWER_EVENT = 'open-quote-drawer';

/** Demande l'ouverture du drawer de sélection (écouté par QuoteDrawer global). */
export function openQuoteDrawer(): void {
    window.dispatchEvent(new CustomEvent(OPEN_DRAWER_EVENT));
}

/** Abonne un callback à la demande d'ouverture du drawer. Retourne le cleanup. */
export function onOpenQuoteDrawer(handler: () => void): () => void {
    window.addEventListener(OPEN_DRAWER_EVENT, handler);
    return () => window.removeEventListener(OPEN_DRAWER_EVENT, handler);
}

function readCart(): QuoteCartItem[] {
    if (typeof window === 'undefined') {
        return [];
    }
    try {
        const raw = window.localStorage.getItem(STORAGE_KEY);
        return raw ? (JSON.parse(raw) as QuoteCartItem[]) : [];
    } catch {
        return [];
    }
}

function writeCart(items: QuoteCartItem[]): void {
    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    // Notifie les autres composants montés (header, drawer) dans le même onglet.
    window.dispatchEvent(new CustomEvent(EVENT_NAME));
}

/**
 * Panier de devis persistant (localStorage), synchronisé entre composants
 * via un évènement custom + l'évènement natif `storage` (multi-onglets).
 */
export function useQuoteCart() {
    const [items, setItems] = useState<QuoteCartItem[]>([]);

    useEffect(() => {
        setItems(readCart());
        const sync = () => setItems(readCart());
        window.addEventListener(EVENT_NAME, sync);
        window.addEventListener('storage', sync);
        return () => {
            window.removeEventListener(EVENT_NAME, sync);
            window.removeEventListener('storage', sync);
        };
    }, []);

    const addItem = useCallback((item: Omit<QuoteCartItem, 'quantity'>, quantity = 1) => {
        const current = readCart();
        const existing = current.find((i) => i.id === item.id);
        const next = existing
            ? current.map((i) => (i.id === item.id ? { ...i, quantity: i.quantity + quantity } : i))
            : [...current, { ...item, quantity }];
        writeCart(next);
    }, []);

    const removeItem = useCallback((id: number) => {
        writeCart(readCart().filter((i) => i.id !== id));
    }, []);

    const updateQuantity = useCallback((id: number, quantity: number) => {
        if (quantity < 1) {
            writeCart(readCart().filter((i) => i.id !== id));
            return;
        }
        writeCart(readCart().map((i) => (i.id === id ? { ...i, quantity } : i)));
    }, []);

    const clear = useCallback(() => writeCart([]), []);

    const count = items.reduce((sum, i) => sum + i.quantity, 0);
    const has = useCallback((id: number) => items.some((i) => i.id === id), [items]);

    return { items, count, addItem, removeItem, updateQuantity, clear, has };
}
