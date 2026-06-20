import { openQuoteDrawer, useQuoteCart } from '@/hooks/useQuoteCart';
import { Link, router, usePage } from '@inertiajs/react';
import { FormEvent, useEffect, useRef, useState } from 'react';

interface Suggestion {
    name: string;
    slug: string;
    reference: string | null;
    product_type: string;
}

/** Catégorie de navigation telle que partagée par le backend (méga-menus pilotés par la BDD). */
interface NavCategory {
    name: string;
    slug: string;
    icon: string | null;
    description: string | null;
}

interface SharedNav {
    equipement: NavCategory[];
    reactif: NavCategory[];
}

interface NavItem {
    label: string;
    href: string;
    /** Type de méga-menu à afficher (les données viennent des props partagées). */
    megaType?: 'produits' | 'reactifs';
}

const navItems: NavItem[] = [
    { label: 'Accueil', href: '/' },
    { label: 'Produits', href: '/catalogue', megaType: 'produits' },
    { label: 'Réactifs', href: '/reactifs', megaType: 'reactifs' },
    { label: 'Actualités', href: '/actualites' },
    { label: 'Services & SAV', href: '/services-sav' },
    { label: 'Qui sommes-nous ?', href: '/qui-sommes-nous' },
    { label: 'Contactez-nous', href: '/contact' },
];

/** Répartit une liste en N colonnes équilibrées (pour le méga-menu Produits, type maquette). */
function splitColumns<T>(items: T[], columns: number): T[][] {
    const perCol = Math.ceil(items.length / columns);
    return Array.from({ length: columns }, (_, i) => items.slice(i * perCol, (i + 1) * perCol)).filter((c) => c.length > 0);
}

interface HeaderProps {
    /** Override optionnel du compteur (sinon lu depuis le panier localStorage). */
    quoteCount?: number;
}

export default function Header({ quoteCount }: HeaderProps) {
    const nav = (usePage().props as { nav?: SharedNav }).nav ?? { equipement: [], reactif: [] };
    const [openMenu, setOpenMenu] = useState<string | null>(null);
    const [mobileOpen, setMobileOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [suggestions, setSuggestions] = useState<Suggestion[]>([]);
    const [showSuggestions, setShowSuggestions] = useState(false);
    const searchRef = useRef<HTMLDivElement>(null);
    const { count } = useQuoteCart();
    const badgeCount = quoteCount ?? count;

    // Autocomplétion : interroge l'endpoint Scout (débounce 250 ms) dès 2 caractères.
    useEffect(() => {
        const term = searchQuery.trim();
        if (term.length < 2) {
            setSuggestions([]);
            return;
        }
        const ctrl = new AbortController();
        const t = setTimeout(() => {
            fetch(`/recherche/suggestions?q=${encodeURIComponent(term)}`, {
                headers: { Accept: 'application/json' },
                signal: ctrl.signal,
            })
                .then((r) => (r.ok ? r.json() : []))
                .then((data: Suggestion[]) => {
                    setSuggestions(data);
                    setShowSuggestions(true);
                })
                .catch(() => {});
        }, 250);
        return () => {
            clearTimeout(t);
            ctrl.abort();
        };
    }, [searchQuery]);

    // Ferme le menu de suggestions au clic extérieur.
    useEffect(() => {
        function onClick(e: MouseEvent) {
            if (searchRef.current && !searchRef.current.contains(e.target as Node)) {
                setShowSuggestions(false);
            }
        }
        document.addEventListener('mousedown', onClick);
        return () => document.removeEventListener('mousedown', onClick);
    }, []);

    function submitSearch(e: FormEvent) {
        e.preventDefault();
        const q = searchQuery.trim();
        setShowSuggestions(false);
        router.get('/catalogue', q ? { q } : {}, { preserveScroll: false });
    }

    function goToSuggestion(slug: string) {
        setShowSuggestions(false);
        setSearchQuery('');
        router.visit(`/produits/${slug}`);
    }

    const productColumns = splitColumns(nav.equipement, 4);

    return (
        <header className="sticky top-0 z-50 w-full">
            {/* ── Niveau 1 — Barre institutionnelle ───────────────── */}
            <div className="bg-navy-blue text-white">
                <div className="mx-auto flex max-w-7xl items-center justify-between gap-sm px-4 py-1.5 text-body-sm md:px-margin">
                    <p className="flex items-center gap-1.5 font-medium">
                        <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                            verified
                        </span>
                        <span className="hidden sm:inline">Agréé Minsanté : arrêté N° 0683</span>
                        <span className="sm:hidden">Agréé Minsanté</span>
                    </p>
                    <div className="flex items-center gap-sm">
                        <Link href="/contact" className="hidden items-center gap-1 hover:text-white/80 md:flex">
                            <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                                help
                            </span>
                            Aide / Contact
                        </Link>
                        <a href="tel:+237674645354" className="flex items-center gap-1 font-medium hover:text-white/80">
                            <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                                call
                            </span>
                            +237 674 64 53 54
                        </a>
                    </div>
                </div>
            </div>

            {/* ── Niveau 2 — Logo + Recherche + CTA ───────────────── */}
            <div className="border-b border-outline-variant bg-white">
                <div className="mx-auto flex max-w-7xl items-center gap-sm px-4 py-3 md:px-margin">
                    <Link href="/" aria-label="Accueil Transtechno Cameroun" className="shrink-0">
                        <img src="/images/logo.png" alt="Transtechno Cameroun" className="h-12 w-auto object-contain" />
                    </Link>

                    {/* Recherche + autocomplétion */}
                    <div ref={searchRef} className="relative hidden flex-1 md:block">
                        <form onSubmit={submitSearch} role="search">
                            <span
                                className="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-secondary"
                                aria-hidden="true"
                            >
                                search
                            </span>
                            <input
                                type="search"
                                name="q"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                onFocus={() => suggestions.length > 0 && setShowSuggestions(true)}
                                placeholder="Rechercher un équipement, une référence…"
                                aria-label="Rechercher un produit"
                                autoComplete="off"
                                className="w-full rounded-lg border-outline-variant bg-surface-2 py-2.5 pl-11 pr-4 text-body-md text-secondary placeholder:text-secondary/60 focus:border-tertiary focus:bg-white focus:ring-1 focus:ring-tertiary"
                            />
                        </form>

                        {showSuggestions && suggestions.length > 0 && (
                            <ul className="absolute left-0 right-0 top-full z-50 mt-1 overflow-hidden rounded-lg border border-outline-variant bg-white shadow-xl">
                                {suggestions.map((s) => (
                                    <li key={s.slug}>
                                        <button
                                            type="button"
                                            onClick={() => goToSuggestion(s.slug)}
                                            className="flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-surface-2"
                                        >
                                            <span className="material-symbols-outlined text-[18px] text-tertiary" aria-hidden="true">
                                                {s.product_type === 'reactif' ? 'science' : 'inventory_2'}
                                            </span>
                                            <span className="min-w-0 flex-1">
                                                <span className="block truncate text-body-sm font-medium text-secondary">{s.name}</span>
                                                {s.reference && (
                                                    <span className="block truncate text-[12px] text-secondary/50">Réf. {s.reference}</span>
                                                )}
                                            </span>
                                        </button>
                                    </li>
                                ))}
                                <li className="border-t border-outline-variant">
                                    <button
                                        type="button"
                                        onClick={submitSearch}
                                        className="flex w-full items-center gap-1.5 px-3 py-2 text-left text-body-sm font-semibold text-primary hover:bg-surface-2"
                                    >
                                        <span className="material-symbols-outlined text-[18px]" aria-hidden="true">search</span>
                                        Voir tous les résultats pour « {searchQuery.trim()} »
                                    </button>
                                </li>
                            </ul>
                        )}
                    </div>

                    {/* Panier devis — ouvre le drawer global */}
                    <button
                        type="button"
                        onClick={openQuoteDrawer}
                        aria-label={`Ma sélection, ${badgeCount} article${badgeCount > 1 ? 's' : ''}`}
                        className="relative ml-auto flex h-11 min-w-11 flex-col items-center justify-center px-2 text-secondary hover:text-primary md:ml-0"
                    >
                        <span className="relative">
                            <span className="material-symbols-outlined text-[28px]" aria-hidden="true">
                                request_quote
                            </span>
                            {badgeCount > 0 && (
                                <span className="absolute -right-1.5 -top-1.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-primary-container px-1 text-[10px] font-bold text-white">
                                    {badgeCount}
                                </span>
                            )}
                        </span>
                        <span className="hidden text-[10px] font-bold uppercase tracking-tight lg:block">Demandes</span>
                    </button>

                    {/* CTA principal */}
                    <Link
                        href="/cotation"
                        className="hidden h-11 items-center rounded-lg bg-primary-container px-4 text-body-sm font-semibold text-white transition-colors hover:bg-primary md:flex"
                    >
                        Demander un devis
                    </Link>

                    {/* Burger mobile */}
                    <button
                        type="button"
                        onClick={() => setMobileOpen((v) => !v)}
                        aria-label="Ouvrir le menu"
                        aria-expanded={mobileOpen}
                        className="flex h-11 w-11 items-center justify-center rounded-lg text-secondary hover:bg-surface-2 md:hidden"
                    >
                        <span className="material-symbols-outlined" aria-hidden="true">
                            {mobileOpen ? 'close' : 'menu'}
                        </span>
                    </button>
                </div>
            </div>

            {/* ── Niveau 3 — Navigation principale (desktop) ──────── */}
            <nav aria-label="Navigation principale" className="hidden border-b border-outline-variant bg-white shadow-sm md:block">
                <div className="mx-auto max-w-7xl px-4 md:px-margin">
                    <ul className="flex items-center gap-1">
                        {navItems.map((item) => (
                            <li
                                key={item.label}
                                className="relative"
                                onMouseEnter={() => item.megaType && setOpenMenu(item.label)}
                                onMouseLeave={() => setOpenMenu(null)}
                            >
                                <Link
                                    href={item.href}
                                    className="flex h-12 items-center gap-1 px-3 text-body-sm font-medium text-secondary hover:text-primary"
                                >
                                    {item.label}
                                    {item.megaType && (
                                        <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                                            expand_more
                                        </span>
                                    )}
                                </Link>

                                {/* ── Méga-menu PRODUITS (liste plate sur 4 colonnes, type maquette) ── */}
                                {item.megaType === 'produits' && openMenu === item.label && (
                                    <div className="absolute left-0 top-full z-50 w-[1000px] rounded-b-lg border border-outline-variant bg-white p-lg shadow-2xl">
                                        {productColumns.length === 0 ? (
                                            <p className="text-body-sm text-secondary/60">Aucune catégorie disponible.</p>
                                        ) : (
                                            <div className="grid grid-cols-4 gap-md">
                                                {productColumns.map((col, ci) => (
                                                    <div key={ci} className="flex flex-col gap-1">
                                                        {col.map((cat) => (
                                                            <Link
                                                                key={cat.slug}
                                                                href={`/catalogue?categorie=${cat.slug}`}
                                                                className="py-1 text-[13px] font-medium uppercase text-secondary transition-colors hover:text-primary"
                                                            >
                                                                {cat.name}
                                                            </Link>
                                                        ))}
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                        <div className="mt-md border-t border-outline-variant pt-md">
                                            <Link href="/categories" className="flex items-center gap-1 text-body-sm font-bold text-primary hover:underline">
                                                Afficher toutes les catégories
                                                <span className="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_right_alt</span>
                                            </Link>
                                        </div>
                                    </div>
                                )}

                                {/* ── Méga-menu RÉACTIFS (catégories + carte partenaire) ── */}
                                {item.megaType === 'reactifs' && openMenu === item.label && (
                                    <div className="absolute left-0 top-full z-50 w-[820px] rounded-b-lg border border-outline-variant bg-white p-md shadow-2xl">
                                        <div className="grid grid-cols-[1.4fr_1fr] gap-md">
                                            <div>
                                                <h3 className="mb-2 border-b border-outline-variant pb-1.5 text-label-caps uppercase text-navy-blue">
                                                    Gamme réactifs
                                                </h3>
                                                {nav.reactif.length === 0 ? (
                                                    <p className="py-1 text-body-sm text-secondary/60">Aucune catégorie disponible.</p>
                                                ) : (
                                                    <ul className="grid grid-cols-2 gap-x-md gap-y-1">
                                                        {nav.reactif.map((cat) => (
                                                            <li key={cat.slug}>
                                                                <Link
                                                                    href={`/reactifs?categorie=${cat.slug}`}
                                                                    className="flex items-center gap-1.5 rounded py-1 text-body-sm text-secondary hover:text-tertiary"
                                                                >
                                                                    <span className="material-symbols-outlined text-[16px] text-tertiary" aria-hidden="true">
                                                                        {cat.icon || 'science'}
                                                                    </span>
                                                                    {cat.name}
                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </ul>
                                                )}
                                            </div>

                                            {/* Carte partenaire ABM mise en valeur */}
                                            <Link
                                                href="/reactifs/abm"
                                                className="flex flex-col justify-between rounded-lg bg-navy-blue p-3 text-white transition-colors hover:bg-tertiary"
                                            >
                                                <div>
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-white/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide">
                                                        <span className="material-symbols-outlined text-[14px]" aria-hidden="true">handshake</span>
                                                        Partenaire
                                                    </span>
                                                    <p className="mt-2 text-body-md font-bold leading-tight">Applied Biological Materials</p>
                                                    <p className="mt-1 text-body-sm text-white/70">
                                                        Réactifs biologie moléculaire &amp; microbiologie, livrés sous chaîne du froid.
                                                    </p>
                                                </div>
                                                <span className="mt-3 inline-flex items-center gap-1 text-body-sm font-semibold">
                                                    Découvrir le partenariat
                                                    <span className="material-symbols-outlined text-[16px]" aria-hidden="true">arrow_forward</span>
                                                </span>
                                            </Link>
                                        </div>
                                    </div>
                                )}
                            </li>
                        ))}
                    </ul>
                </div>
            </nav>

            {/* ── Menu mobile ─────────────────────────────────────── */}
            {mobileOpen && (
                <nav aria-label="Navigation mobile" className="border-b border-outline-variant bg-white md:hidden">
                    <ul className="mx-auto max-w-7xl px-4 py-2">
                        {navItems.map((item) => (
                            <li key={item.label}>
                                <Link
                                    href={item.href}
                                    onClick={() => setMobileOpen(false)}
                                    className="flex h-12 items-center text-body-md font-medium text-secondary hover:text-primary"
                                >
                                    {item.label}
                                </Link>
                            </li>
                        ))}
                        <li className="pt-2">
                            <Link
                                href="/cotation"
                                onClick={() => setMobileOpen(false)}
                                className="flex h-11 items-center justify-center rounded-lg bg-primary-container px-4 text-body-sm font-semibold text-white"
                            >
                                Demander un devis
                            </Link>
                        </li>
                    </ul>
                </nav>
            )}
        </header>
    );
}
