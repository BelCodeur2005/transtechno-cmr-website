import { openQuoteDrawer, StockStatus, useQuoteCart } from '@/hooks/useQuoteCart';
import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

interface ProductItem {
    id: number;
    name: string;
    slug: string;
    reference: string | null;
    short_description: string | null;
    brand: string | null;
    image: string | null;
    stock_status: StockStatus;
    is_featured?: boolean;
    product_type: 'equipement' | 'reactif' | 'consommable';
}

interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface Filters {
    type: string | null;
    categorie: string | null;
    q: string | null;
    en_stock: boolean;
    poles: string[];
    marques: string[];
    tri: string;
}

interface Option {
    name: string;
    slug: string;
    count: number;
}

interface TypeOption {
    value: string;
    label: string;
    count: number;
}

interface CurrentCategory {
    name: string;
    description: string | null;
    icon: string | null;
}

interface CataloguePageProps {
    products: Paginated<ProductItem>;
    filters: Filters;
    scope: 'produits' | 'reactifs';
    types: TypeOption[];
    poles: Option[];
    brands: Option[];
    categories: { name: string; slug: string }[];
    currentCategory: CurrentCategory | null;
}

function stockBadge(p: ProductItem): { text: string; cls: string } {
    if (p.is_featured) {
        return { text: 'Top Vente', cls: 'bg-brand-red text-white' };
    }
    const map: Record<StockStatus, { text: string; cls: string }> = {
        en_stock: { text: 'Disponible', cls: 'bg-green-600 text-white' },
        en_transit: { text: 'Sur commande', cls: 'bg-blue-600 text-white' },
        rupture: { text: 'Indisponible', cls: 'bg-surface-5 text-secondary' },
    };
    return map[p.stock_status];
}

export default function Catalogue({
    products,
    filters,
    scope,
    types,
    poles,
    brands,
    categories,
    currentCategory,
}: CataloguePageProps) {
    const cart = useQuoteCart();
    const [mobileFiltersOpen, setMobileFiltersOpen] = useState(false);
    const [search, setSearch] = useState(filters.q ?? '');

    const isReagent = scope === 'reactifs';
    const baseUrl = isReagent ? '/reactifs' : '/catalogue';
    const scopeLabel = isReagent ? 'Réactifs' : 'Catalogue';
    const defaultTitle = isReagent ? 'Réactifs & Biologie Moléculaire' : 'Catalogue Industriel';
    const defaultDesc = isReagent
        ? "Réactifs de biologie moléculaire et de microbiologie de notre partenaire ABM (Applied Biological Materials). Conservation contrôlée, usage IVD ou recherche."
        : 'Solutions de pointe pour les infrastructures hospitalières modernes. Précision clinique garantie.';

    // Recherche produit (sidebar) : déclenche le filtre serveur après une courte pause.
    useEffect(() => {
        const handle = setTimeout(() => {
            if ((search.trim() || null) !== (filters.q || null)) {
                applyFilters({ q: search.trim() || null });
            }
        }, 400);
        return () => clearTimeout(handle);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    function applyFilters(patch: Partial<Filters>) {
        const next = { ...filters, ...patch };
        router.get(
            baseUrl,
            {
                type: next.type ?? undefined,
                categorie: next.categorie ?? undefined,
                q: next.q ?? undefined,
                en_stock: next.en_stock ? 1 : undefined,
                poles: next.poles,
                marques: next.marques,
                tri: next.tri !== 'pertinence' ? next.tri : undefined,
            },
            { preserveScroll: true, preserveState: true, replace: true },
        );
    }

    function toggleArray(list: string[], slug: string): string[] {
        return list.includes(slug) ? list.filter((s) => s !== slug) : [...list, slug];
    }

    function requestQuote(product: ProductItem) {
        cart.addItem({
            id: product.id,
            name: product.name,
            slug: product.slug,
            reference: product.reference,
            brand: product.brand,
            image: product.image,
            stockStatus: product.stock_status,
        });
        openQuoteDrawer();
    }

    const activeFilterCount =
        filters.poles.length +
        filters.marques.length +
        (filters.categorie ? 1 : 0) +
        (filters.type ? 1 : 0);

    /* ── Contenu de la sidebar (filtres) ─────────────────────── */
    const filtersBody = (
        <>
            <div className="mb-xs">
                <div className="mb-sm flex items-center justify-between">
                    <h2 className="text-[20px] font-semibold text-secondary">{scopeLabel}</h2>
                    {activeFilterCount > 0 && (
                        <Link
                            href={baseUrl}
                            preserveScroll
                            className="flex items-center gap-1 text-[11px] font-bold text-brand-red hover:underline"
                        >
                            <span className="material-symbols-outlined text-[14px]" aria-hidden="true">
                                close
                            </span>
                            Effacer
                        </Link>
                    )}
                </div>
                <div className="relative mb-md">
                    <span
                        className="material-symbols-outlined absolute left-2 top-1/2 -translate-y-1/2 text-[18px] text-secondary/60"
                        aria-hidden="true"
                    >
                        search
                    </span>
                    <input
                        type="search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Rechercher un produit…"
                        aria-label="Rechercher un produit dans le catalogue"
                        className="w-full rounded border border-outline-variant bg-white py-1.5 pl-8 pr-2 text-[13px] focus:border-brand-red focus:ring-1 focus:ring-brand-red"
                    />
                </div>
            </div>

            {/* Type de produit */}
            {types.length > 1 && (
                <div className="space-y-base">
                    <h3 className="mb-xs text-label-caps uppercase tracking-wider text-brand-red">Type de produit</h3>
                    <div className="flex flex-col gap-xs pl-xs">
                        <label className="flex cursor-pointer items-center gap-2 text-[13px] text-secondary hover:text-brand-red">
                            <input
                                type="radio"
                                name="type"
                                checked={!filters.type}
                                onChange={() => applyFilters({ type: null })}
                                className="border-outline-variant text-brand-red focus:ring-brand-red"
                            />
                            Tous les produits
                        </label>
                        {types.map((t) => (
                            <label
                                key={t.value}
                                className="flex cursor-pointer items-center justify-between text-[13px] text-secondary hover:text-brand-red"
                            >
                                <span className="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        name="type"
                                        checked={filters.type === t.value}
                                        onChange={() => applyFilters({ type: t.value })}
                                        className="border-outline-variant text-brand-red focus:ring-brand-red"
                                    />
                                    {t.label}
                                </span>
                                <span className="text-[11px] text-secondary/50">({t.count})</span>
                            </label>
                        ))}
                    </div>
                </div>
            )}

            {/* Catégorie produit */}
            <div className={types.length > 1 ? 'mt-md border-t border-outline-variant pt-md' : 'space-y-base'}>
                <h3 className="mb-xs text-label-caps uppercase tracking-wider text-brand-red">Catégorie produit</h3>
                <select
                    value={filters.categorie ?? ''}
                    onChange={(e) => applyFilters({ categorie: e.target.value || null })}
                    aria-label="Filtrer par catégorie"
                    className="w-full rounded border border-outline-variant bg-white py-1.5 px-2 text-[13px] focus:border-brand-red focus:ring-1 focus:ring-brand-red"
                >
                    <option value="">Toutes les catégories</option>
                    {categories.map((cat) => (
                        <option key={cat.slug} value={cat.slug}>
                            {cat.name}
                        </option>
                    ))}
                </select>
            </div>

            {/* Pôles d'Expertise */}
            <div className="mt-md border-t border-outline-variant pt-md">
                <h3 className="mb-xs text-label-caps uppercase tracking-wider text-brand-red">Pôles d'Expertise</h3>
                <div className="flex flex-col gap-xs pl-xs">
                    {poles.map((pole) => (
                        <label
                            key={pole.slug}
                            className="flex cursor-pointer items-center justify-between text-[13px] text-secondary hover:text-brand-red"
                        >
                            <span className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={filters.poles.includes(pole.slug)}
                                    onChange={() => applyFilters({ poles: toggleArray(filters.poles, pole.slug) })}
                                    className="rounded border-outline-variant text-brand-red focus:ring-brand-red"
                                />
                                {pole.name}
                            </span>
                            <span className="text-[11px] text-secondary/50">({pole.count})</span>
                        </label>
                    ))}
                </div>
            </div>

            {/* Marques Partenaires */}
            <div className="mt-md border-t border-outline-variant pt-md">
                <h3 className="mb-xs text-label-caps uppercase tracking-wider text-brand-red">Marques Partenaires</h3>
                <div className="flex flex-col gap-xs pl-xs">
                    {brands.map((brand) => (
                        <label
                            key={brand.slug}
                            className="flex cursor-pointer items-center justify-between text-[13px] text-secondary hover:text-brand-red"
                        >
                            <span className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={filters.marques.includes(brand.slug)}
                                    onChange={() => applyFilters({ marques: toggleArray(filters.marques, brand.slug) })}
                                    className="rounded border-outline-variant text-brand-red focus:ring-brand-red"
                                />
                                {brand.name}
                            </span>
                            <span className="text-[11px] text-secondary/50">({brand.count})</span>
                        </label>
                    ))}
                </div>
            </div>

            {/* Carte conseil expert */}
            <div className="mt-md rounded border border-secondary/20 bg-[#d4e3ff] p-sm">
                <p className="text-[13px] italic text-[#001c39]">Besoin d'un conseil d'expert ?</p>
                <Link
                    href="/contact"
                    className="mt-xs flex w-full items-center justify-center rounded bg-secondary py-xs text-label-caps uppercase text-white transition-colors hover:bg-navy-blue"
                >
                    Contacter Spécialiste
                </Link>
            </div>
        </>
    );

    return (
        <PublicLayout>
            <Head title={currentCategory ? `${scopeLabel} — ${currentCategory.name}` : scopeLabel}>
                <meta name="description" content={currentCategory ? `Découvrez nos équipements ${currentCategory.name} : matériel médical certifié OMS, disponible à Douala. Demandez un devis personnalisé.` : "Catalogue complet d'équipements médicaux et scientifiques certifiés OMS. Monitoring, imagerie, bloc opératoire, laboratoire — disponibles au Cameroun."} head-key="description" />
                <meta property="og:title" content={currentCategory ? `${scopeLabel} — ${currentCategory.name}` : scopeLabel} head-key="og:title" />
                <meta property="og:type" content="website" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                <meta name="twitter:card" content="summary" head-key="twitter:card" />
            </Head>

            <div className="mx-auto flex max-w-7xl">
                {/* ── Sidebar filtres (desktop) ─────────────────────── */}
                <aside className="hidden w-72 shrink-0 flex-col gap-sm self-start border-r border-outline-variant bg-surface-2 p-sm lg:flex lg:sticky lg:top-[140px] lg:max-h-[calc(100vh-160px)] lg:overflow-y-auto">
                    {filtersBody}
                </aside>

                {/* ── Contenu principal ─────────────────────────────── */}
                <main className="flex-1 p-4 md:p-margin">
                    {/* Bouton filtres mobile */}
                    <button
                        type="button"
                        onClick={() => setMobileFiltersOpen((v) => !v)}
                        className="mb-md flex min-h-[44px] items-center gap-1 rounded border border-outline-variant px-3 text-body-sm text-secondary lg:hidden"
                    >
                        <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                            tune
                        </span>
                        Filtres{activeFilterCount > 0 ? ` (${activeFilterCount})` : ''}
                    </button>
                    {mobileFiltersOpen && (
                        <div className="mb-md rounded-lg border border-outline-variant bg-surface-2 p-md lg:hidden">{filtersBody}</div>
                    )}

                    {/* Bandeau de catégorie */}
                    <section className="relative mb-lg flex min-h-[180px] w-full items-center overflow-hidden rounded-xl border border-outline-variant bg-dark-navy px-md sm:px-lg shadow-sm">
                        <span
                            className="material-symbols-outlined pointer-events-none absolute -right-6 top-1/2 -translate-y-1/2 text-[220px] text-white/5"
                            aria-hidden="true"
                        >
                            {currentCategory?.icon || 'medical_services'}
                        </span>
                        <div className="relative z-10 max-w-lg py-md">
                            <span className="mb-xs inline-block rounded bg-primary-container px-xs py-1 text-label-caps uppercase text-white">
                                {currentCategory ? 'Catégorie' : scopeLabel}
                            </span>
                            <h1 className="mb-xs text-2xl font-bold leading-tight text-white sm:text-4xl lg:text-headline-xl">
                                {currentCategory?.name ?? defaultTitle}
                            </h1>
                            <p className="text-body-sm text-white/80 sm:text-body-md">
                                {currentCategory?.description ?? defaultDesc}
                            </p>
                        </div>
                    </section>

                    {/* Barre d'outils : compte + tri */}
                    <div className="mb-md flex items-center justify-between gap-sm">
                        <p className="text-body-sm font-semibold text-secondary">
                            {products.total} produit{products.total > 1 ? 's' : ''}
                        </p>
                        <div className="flex items-center gap-2 text-body-sm text-secondary/80">
                            <label htmlFor="tri" className="hidden sm:inline">
                                Trier par :
                            </label>
                            <select
                                id="tri"
                                value={filters.tri}
                                onChange={(e) => applyFilters({ tri: e.target.value })}
                                className="rounded border-outline-variant bg-white py-1 pl-2 pr-8 text-sm focus:border-tertiary focus:ring-tertiary"
                            >
                                <option value="pertinence">Pertinence</option>
                                <option value="az">Nom A-Z</option>
                                <option value="za">Nom Z-A</option>
                                <option value="nouveautes">Nouveautés</option>
                            </select>
                        </div>
                    </div>

                    {/* Grille produits */}
                    {products.data.length === 0 ? (
                        <div className="rounded-lg border border-dashed border-outline-variant bg-white p-lg text-center">
                            <span className="material-symbols-outlined text-[48px] text-secondary/40" aria-hidden="true">
                                search_off
                            </span>
                            <p className="mt-sm text-body-lg font-bold text-navy-blue">Aucun résultat</p>
                            <p className="mt-1 text-body-md text-secondary">
                                Vous cherchez un équipement spécifique ? Transtechno l'importe pour vous.
                            </p>
                            <Link
                                href="/demande-cotation"
                                className="mt-sm inline-flex h-11 items-center rounded-lg bg-primary-container px-4 font-semibold text-white hover:bg-primary"
                            >
                                Faire une demande sur-mesure
                            </Link>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 gap-md md:grid-cols-2 xl:grid-cols-3">
                            {products.data.map((product) => {
                                const badge = stockBadge(product);
                                return (
                                    <div
                                        key={product.id}
                                        className="group flex flex-col rounded-lg border border-outline-variant bg-white p-md transition-shadow hover:shadow-md"
                                    >
                                        {/* Visuel */}
                                        <div className="relative mb-sm flex h-48 w-full items-center justify-center overflow-hidden rounded bg-surface-2">
                                            <span
                                                className={`absolute left-2 top-2 z-10 rounded px-2 py-0.5 text-[10px] font-bold uppercase shadow-sm ${badge.cls}`}
                                            >
                                                {badge.text}
                                            </span>
                                            {product.product_type === 'reactif' && (
                                                <span className="absolute right-2 top-2 z-10 rounded bg-orange-100 px-2 py-0.5 text-[10px] font-bold uppercase text-orange-700">
                                                    Réactif
                                                </span>
                                            )}
                                            <Link
                                                href={`/produits/${product.slug}`}
                                                className="flex h-full w-full items-center justify-center"
                                            >
                                                {product.image ? (
                                                    <img
                                                        src={product.image}
                                                        alt={product.name}
                                                        loading="lazy"
                                                        className="h-40 object-contain mix-blend-multiply transition-transform duration-300 group-hover:scale-105"
                                                    />
                                                ) : (
                                                    <span
                                                        className="material-symbols-outlined text-6xl text-secondary/30"
                                                        aria-hidden="true"
                                                    >
                                                        {product.product_type === 'reactif' ? 'biotech' : 'medical_services'}
                                                    </span>
                                                )}
                                            </Link>
                                        </div>

                                        {/* Contenu */}
                                        <div className="flex-1">
                                            {product.brand && (
                                                <div className="mb-1 text-label-caps uppercase text-secondary/60">{product.brand}</div>
                                            )}
                                            <Link href={`/produits/${product.slug}`}>
                                                <h3 className="mb-xs text-body-lg font-bold leading-snug text-secondary hover:text-brand-red">
                                                    {product.name}
                                                </h3>
                                            </Link>
                                            {product.short_description && (
                                                <p className="mb-md line-clamp-2 text-body-sm text-secondary/70">
                                                    {product.short_description}
                                                </p>
                                            )}
                                        </div>

                                        {/* Actions */}
                                        <div className="mt-auto flex flex-col gap-2">
                                            <button
                                                type="button"
                                                onClick={() => requestQuote(product)}
                                                className="flex w-full items-center justify-center gap-xs rounded bg-primary py-sm text-label-caps uppercase text-white transition-colors hover:bg-primary-container"
                                            >
                                                <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                                                    request_quote
                                                </span>
                                                Demander un devis
                                            </button>
                                            <Link
                                                href={`/produits/${product.slug}`}
                                                className="flex items-center justify-center gap-1 text-[11px] font-semibold text-secondary hover:text-brand-red"
                                            >
                                                <span className="material-symbols-outlined text-[14px]" aria-hidden="true">
                                                    picture_as_pdf
                                                </span>
                                                Fiche technique (PDF)
                                            </Link>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}

                    {/* Pagination */}
                    {products.last_page > 1 && (
                        <nav aria-label="Pagination" className="mt-lg flex items-center justify-center gap-2">
                            {products.prev_page_url ? (
                                <Link
                                    href={products.prev_page_url}
                                    preserveScroll
                                    className="flex min-h-[44px] items-center rounded-lg border border-outline-variant bg-white px-3 text-body-sm text-secondary hover:border-tertiary"
                                >
                                    Précédent
                                </Link>
                            ) : (
                                <span className="flex min-h-[44px] items-center rounded-lg border border-outline-variant bg-surface-2 px-3 text-body-sm text-secondary/40">
                                    Précédent
                                </span>
                            )}
                            <span className="text-body-sm text-secondary">
                                Page {products.current_page} / {products.last_page}
                            </span>
                            {products.next_page_url ? (
                                <Link
                                    href={products.next_page_url}
                                    preserveScroll
                                    className="flex min-h-[44px] items-center rounded-lg border border-outline-variant bg-white px-3 text-body-sm text-secondary hover:border-tertiary"
                                >
                                    Suivant
                                </Link>
                            ) : (
                                <span className="flex min-h-[44px] items-center rounded-lg border border-outline-variant bg-surface-2 px-3 text-body-sm text-secondary/40">
                                    Suivant
                                </span>
                            )}
                        </nav>
                    )}
                </main>
            </div>
        </PublicLayout>
    );
}
