import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link, router } from '@inertiajs/react';

interface Card {
    title: string;
    slug: string;
    excerpt: string | null;
    image: string | null;
    category: string | null;
    category_slug: string | null;
    published_at: string | null;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
}

interface Props {
    featured: Card | null;
    articles: Paginated<Card>;
    categories: { name: string; slug: string; count: number }[];
    currentCategory: string | null;
}

/** Convertit un label de pagination Laravel (avec entités HTML) en texte simple sûr. */
function cleanLabel(label: string): string {
    return label
        .replace(/&laquo;|&lsaquo;/g, '‹')
        .replace(/&raquo;|&rsaquo;/g, '›')
        .replace(/<[^>]*>/g, '')
        .replace(/Previous/i, 'Précédent')
        .replace(/Next/i, 'Suivant')
        .trim();
}

function CoverFallback() {
    return (
        <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-navy-blue to-secondary">
            <span className="material-symbols-outlined text-[40px] text-white/40" aria-hidden="true">
                article
            </span>
        </div>
    );
}

export default function Actualites({ featured, articles, categories, currentCategory }: Props) {
    const goCategory = (slug: string | null) => {
        router.get('/actualites', slug ? { categorie: slug } : {}, { preserveScroll: false, preserveState: false });
    };

    const pillBase =
        'rounded-full px-4 py-1.5 text-body-sm font-semibold transition-colors';

    return (
        <PublicLayout>
            <Head title="Actualités & Innovations Médicales — Transtechno Cameroun">
                <meta name="description" content="Actualités médicales, partenariats, innovations et événements dans le secteur de la santé au Cameroun. Suivez les dernières nouvelles de Transtechno Cameroun." head-key="description" />
                <meta property="og:title" content="Actualités & Innovations Médicales — Transtechno Cameroun" head-key="og:title" />
                <meta property="og:description" content="Actualités médicales, partenariats, innovations et événements dans le secteur de la santé au Cameroun." head-key="og:description" />
                <meta property="og:type" content="website" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                <meta name="twitter:card" content="summary" head-key="twitter:card" />
            </Head>

            {/* ── Hero ─────────────────────────────────────────── */}
            <section className="bg-navy-blue py-xl text-white">
                <div className="mx-auto max-w-7xl px-margin">
                    <h1 className="max-w-3xl text-headline-xl">Actualités &amp; Innovations Médicales</h1>
                    <p className="mt-sm max-w-2xl text-body-lg text-white/80">
                        Suivez l'évolution du plateau technique au Cameroun : nos dernières installations, événements et
                        conseils d'experts pour optimiser vos équipements médicaux.
                    </p>
                </div>
            </section>

            <main className="mx-auto max-w-7xl px-margin py-lg">
                {/* ── Filtres catégories ──────────────────────── */}
                <div className="mb-lg flex flex-wrap gap-2">
                    <button
                        type="button"
                        onClick={() => goCategory(null)}
                        className={`${pillBase} ${!currentCategory ? 'bg-primary-container text-white' : 'bg-surface-2 text-secondary hover:bg-surface-3'}`}
                    >
                        Toutes les news
                    </button>
                    {categories.map((c) => (
                        <button
                            key={c.slug}
                            type="button"
                            onClick={() => goCategory(c.slug)}
                            className={`${pillBase} ${currentCategory === c.slug ? 'bg-primary-container text-white' : 'bg-surface-2 text-secondary hover:bg-surface-3'}`}
                        >
                            {c.name}
                            <span className="ml-1 opacity-60">({c.count})</span>
                        </button>
                    ))}
                </div>

                {/* ── À la une ────────────────────────────────── */}
                {featured && (
                    <Link
                        href={`/news/${featured.slug}`}
                        className="group mb-lg block overflow-hidden rounded-xl border border-outline-variant bg-white md:flex"
                    >
                        <div className="h-56 w-full overflow-hidden md:h-auto md:w-2/5">
                            {featured.image ? (
                                <img
                                    src={featured.image}
                                    alt={featured.title}
                                    loading="lazy"
                                    className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                            ) : (
                                <CoverFallback />
                            )}
                        </div>
                        <div className="flex flex-col justify-center p-lg md:w-3/5">
                            <div className="mb-xs flex items-center gap-2 text-body-sm text-secondary/70">
                                <span className="material-symbols-outlined text-[18px] text-tertiary" aria-hidden="true">
                                    calendar_today
                                </span>
                                {featured.published_at}
                                {featured.category && (
                                    <>
                                        <span aria-hidden="true">•</span>
                                        <span className="font-semibold text-primary">{featured.category}</span>
                                    </>
                                )}
                            </div>
                            <h2 className="text-headline-lg text-navy-blue transition-colors group-hover:text-primary">
                                {featured.title}
                            </h2>
                            {featured.excerpt && (
                                <p className="mt-sm text-body-md text-secondary/70 line-clamp-3">{featured.excerpt}</p>
                            )}
                            <span className="mt-md inline-flex items-center gap-1 font-bold text-primary">
                                Lire l'article complet
                                <span className="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_forward</span>
                            </span>
                        </div>
                    </Link>
                )}

                {/* ── Grille ──────────────────────────────────── */}
                {articles.data.length === 0 ? (
                    <div className="rounded-xl border border-dashed border-outline-variant bg-white p-xl text-center">
                        <span className="material-symbols-outlined text-[48px] text-secondary/30" aria-hidden="true">
                            news
                        </span>
                        <p className="mt-sm text-body-lg font-bold text-navy-blue">Aucun article dans cette catégorie</p>
                        <button onClick={() => goCategory(null)} className="mt-sm font-semibold text-primary hover:underline">
                            Voir toutes les actualités
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 gap-md sm:grid-cols-2 lg:grid-cols-3">
                        {articles.data.map((a) => (
                            <Link
                                key={a.slug}
                                href={`/news/${a.slug}`}
                                className="group flex flex-col overflow-hidden rounded-xl border border-outline-variant bg-white transition-shadow hover:shadow-lg"
                            >
                                <div className="h-44 w-full overflow-hidden">
                                    {a.image ? (
                                        <img
                                            src={a.image}
                                            alt={a.title}
                                            loading="lazy"
                                            className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        />
                                    ) : (
                                        <CoverFallback />
                                    )}
                                </div>
                                <div className="flex flex-1 flex-col p-md">
                                    {a.category && (
                                        <span className="mb-xs inline-block w-fit rounded bg-primary/10 px-2 py-0.5 text-label-caps font-semibold uppercase text-primary">
                                            {a.category}
                                        </span>
                                    )}
                                    <h3 className="text-headline-md leading-snug text-navy-blue transition-colors group-hover:text-primary">
                                        {a.title}
                                    </h3>
                                    {a.excerpt && (
                                        <p className="mt-xs text-body-sm text-secondary/70 line-clamp-2">{a.excerpt}</p>
                                    )}
                                    <div className="mt-auto flex items-center justify-between pt-sm text-body-sm">
                                        <span className="text-secondary/60">{a.published_at}</span>
                                        <span className="inline-flex items-center gap-1 font-bold text-primary">
                                            Lire
                                            <span className="material-symbols-outlined text-[16px]" aria-hidden="true">arrow_forward</span>
                                        </span>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>
                )}

                {/* ── Pagination ──────────────────────────────── */}
                {articles.links.length > 3 && (
                    <div className="mt-lg flex flex-wrap justify-center gap-1">
                        {articles.links.map((link, i) => (
                            <button
                                key={i}
                                type="button"
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url, {}, { preserveScroll: false })}
                                aria-label={cleanLabel(link.label)}
                                className={`min-w-9 rounded px-3 py-1.5 text-body-sm ${
                                    link.active
                                        ? 'bg-primary-container font-bold text-white'
                                        : link.url
                                          ? 'border border-outline-variant text-secondary hover:bg-surface-2'
                                          : 'cursor-not-allowed text-secondary/30'
                                }`}
                            >
                                {cleanLabel(link.label)}
                            </button>
                        ))}
                    </div>
                )}
            </main>
        </PublicLayout>
    );
}
