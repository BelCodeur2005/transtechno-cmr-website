import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

interface Card {
    title: string;
    slug: string;
    image: string | null;
    category: string | null;
    published_at: string | null;
}

interface Props {
    article: {
        title: string;
        slug: string;
        excerpt: string | null;
        meta_title: string | null;
        meta_description: string | null;
        content: string | null;
        image: string | null;
        category: { name: string; slug: string } | null;
        author: string | null;
        published_at: string | null;
        published_at_iso: string | null;
        updated_at_iso: string;
        url: string;
        tags: { name: string; slug: string }[];
    };
    related: Card[];
}

export default function Article({ article, related }: Props) {
    const metaTitle = article.meta_title || article.title;
    const metaDescription = article.meta_description || article.excerpt || '';

    const jsonLd = {
        '@context': 'https://schema.org',
        '@type': 'Article',
        headline: metaTitle,
        description: metaDescription || undefined,
        image: article.image || undefined,
        datePublished: article.published_at_iso || undefined,
        dateModified: article.updated_at_iso,
        author: article.author
            ? { '@type': 'Person', name: article.author }
            : { '@type': 'Organization', name: 'Transtechno Cameroun' },
        publisher: {
            '@type': 'Organization',
            name: 'Transtechno Cameroun',
            logo: { '@type': 'ImageObject', url: '/images/logo.png' },
        },
        mainEntityOfPage: { '@type': 'WebPage', '@id': article.url },
        keywords: article.tags.map((t) => t.name).join(', ') || undefined,
        articleSection: article.category?.name || undefined,
        inLanguage: 'fr-CM',
    };

    return (
        <PublicLayout>
            <Head title={metaTitle}>
                {metaDescription && <meta name="description" content={metaDescription} head-key="description" />}
                <link rel="canonical" href={article.url} head-key="canonical" />
                <meta property="og:type" content="article" head-key="og:type" />
                <meta property="og:url" content={article.url} head-key="og:url" />
                <meta property="og:title" content={metaTitle} head-key="og:title" />
                {metaDescription && <meta property="og:description" content={metaDescription} head-key="og:description" />}
                {article.image && <meta property="og:image" content={article.image} head-key="og:image" />}
                <meta name="twitter:card" content={article.image ? 'summary_large_image' : 'summary'} head-key="twitter:card" />
                <meta name="twitter:title" content={metaTitle} head-key="twitter:title" />
                {metaDescription && <meta name="twitter:description" content={metaDescription} head-key="twitter:description" />}
                {article.image && <meta name="twitter:image" content={article.image} head-key="twitter:image" />}
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />
            </Head>

            {/* ── En-tête ──────────────────────────────────────── */}
            <header className="relative overflow-hidden bg-navy-blue py-lg lg:py-xl text-white">
                {article.image && (
                    <>
                        <img src={article.image} alt="" className="absolute inset-0 h-full w-full object-cover opacity-25" />
                        <div className="absolute inset-0 bg-navy-blue/70" />
                    </>
                )}
                <div className="relative mx-auto max-w-4xl px-sm sm:px-margin">
                    <Link href="/actualites" className="mb-sm inline-flex items-center gap-1 text-body-sm font-semibold text-white/80 hover:text-white">
                        <span className="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
                        Toutes les actualités
                    </Link>
                    {article.category && (
                        <span className="mb-sm inline-block rounded bg-primary-container px-2.5 py-1 text-label-caps font-semibold uppercase text-white">
                            {article.category.name}
                        </span>
                    )}
                    <h1 className="text-2xl font-bold sm:text-4xl lg:text-headline-xl">{article.title}</h1>
                    <div className="mt-sm flex flex-wrap items-center gap-sm sm:gap-md text-body-sm text-white/80">
                        {article.published_at && (
                            <span className="inline-flex items-center gap-1">
                                <span className="material-symbols-outlined text-[18px]" aria-hidden="true">calendar_today</span>
                                {article.published_at}
                            </span>
                        )}
                        {article.author && (
                            <span className="inline-flex items-center gap-1">
                                <span className="material-symbols-outlined text-[18px]" aria-hidden="true">person</span>
                                {article.author}
                            </span>
                        )}
                    </div>
                </div>
            </header>

            <main className="mx-auto max-w-4xl px-sm sm:px-margin py-lg">
                {article.image && (
                    <img
                        src={article.image}
                        alt={article.title}
                        className="mb-md lg:mb-lg h-auto w-full rounded-xl border border-outline-variant object-cover"
                    />
                )}

                {article.excerpt && (
                    <p className="mb-md lg:mb-lg border-l-4 border-primary pl-sm sm:pl-md text-body-md lg:text-body-lg font-medium text-secondary">
                        {article.excerpt}
                    </p>
                )}

                {/* Contenu riche rédigé en back-office (Filament RichEditor) */}
                <div
                    className="article-content text-body-md"
                    dangerouslySetInnerHTML={{ __html: article.content ?? '' }}
                />

                {article.tags.length > 0 && (
                    <div className="mt-md lg:mt-lg flex flex-wrap items-center gap-2 border-t border-outline-variant pt-md">
                        <span className="text-body-sm font-semibold text-secondary/60">Mots-clés :</span>
                        {article.tags.map((t) => (
                            <span key={t.slug} className="rounded-full bg-surface-2 px-3 py-1 text-body-sm text-secondary">
                                {t.name}
                            </span>
                        ))}
                    </div>
                )}

                {/* CTA cotation */}
                <div className="mt-md lg:mt-lg flex flex-col items-start gap-sm rounded-xl border border-outline-variant bg-surface p-md lg:p-lg sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 className="text-headline-md text-navy-blue">Un projet d'équipement ?</h3>
                        <p className="text-body-md text-secondary/70">Nos experts vous accompagnent de la sélection à l'installation.</p>
                    </div>
                    <Link href="/contact" className="shrink-0 rounded-lg bg-primary-container px-5 py-2.5 font-bold text-white transition-colors hover:bg-primary">
                        Demander un devis
                    </Link>
                </div>
            </main>

            {/* ── Articles liés ────────────────────────────────── */}
            {related.length > 0 && (
                <section className="border-t border-outline-variant bg-surface py-lg">
                    <div className="mx-auto max-w-7xl px-sm sm:px-margin">
                        <h2 className="mb-md text-headline-md lg:text-headline-lg text-navy-blue">À lire également</h2>
                        <div className="grid grid-cols-1 gap-md sm:grid-cols-2 lg:grid-cols-3">
                            {related.map((a) => (
                                <Link
                                    key={a.slug}
                                    href={`/news/${a.slug}`}
                                    className="group flex flex-col overflow-hidden rounded-xl border border-outline-variant bg-white transition-shadow hover:shadow-lg"
                                >
                                    <div className="h-40 w-full overflow-hidden">
                                        {a.image ? (
                                            <img src={a.image} alt={a.title} loading="lazy" className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                        ) : (
                                            <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-navy-blue to-secondary">
                                                <span className="material-symbols-outlined text-[36px] text-white/40" aria-hidden="true">article</span>
                                            </div>
                                        )}
                                    </div>
                                    <div className="flex flex-1 flex-col p-md">
                                        {a.category && (
                                            <span className="mb-xs text-label-caps font-semibold uppercase text-primary">{a.category}</span>
                                        )}
                                        <h3 className="text-headline-md leading-snug text-navy-blue transition-colors group-hover:text-primary">{a.title}</h3>
                                        <span className="mt-auto pt-sm text-body-sm text-secondary/60">{a.published_at}</span>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    </div>
                </section>
            )}
        </PublicLayout>
    );
}
