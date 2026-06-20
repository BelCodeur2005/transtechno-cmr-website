import { openQuoteDrawer, StockStatus, useQuoteCart } from '@/hooks/useQuoteCart';
import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link, router } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

interface Featured {
    id: number;
    name: string;
    slug: string;
    reference: string | null;
    short_description: string | null;
    category: string | null;
    stock_status: StockStatus;
    image: string | null;
}

interface Partner {
    name: string;
    logo: string | null;
}

interface Pole {
    name: string;
    slug: string;
    icon: string | null;
    description: string | null;
}

interface AccueilPageProps {
    featured: Featured[];
    partners: Partner[];
    poles: Pole[];
}

/** Icône Material par pôle (les pôles seedés n'ont pas encore d'icône). */
const POLE_ICONS: Record<string, string> = {
    'monitoring-et-soins-critiques': 'monitor_heart',
    'hematologie-et-laboratoire': 'biotech',
    'imagerie-medicale': 'radiology',
    'hemodialyse-et-traitement-des-eaux': 'water_drop',
    'reactifs-et-biologie-moleculaire': 'science',
};

const POLE_FALLBACK_DESC = "Solutions et équipements spécialisés certifiés OMS.";

/** Lien d'un pôle : les réactifs ont leur propre catalogue. */
function poleHref(slug: string): string {
    if (slug === 'reactifs-et-biologie-moleculaire') {
        return '/reactifs';
    }
    return `/catalogue?poles[]=${encodeURIComponent(slug)}`;
}

const TRUST = ['Certifié OMS', 'Agréé Minsanté', 'Maintenance SAV'];

export default function Accueil({ featured, partners, poles }: AccueilPageProps) {
    const cart = useQuoteCart();
    const [query, setQuery] = useState('');

    function submitSearch(e: FormEvent) {
        e.preventDefault();
        const q = query.trim();
        router.get('/catalogue', q ? { q } : {}, { preserveScroll: false });
    }

    function addToQuote(p: Featured) {
        cart.addItem({
            id: p.id,
            name: p.name,
            slug: p.slug,
            reference: p.reference,
            brand: null,
            image: p.image,
            stockStatus: p.stock_status,
        });
        openQuoteDrawer();
    }

    const bigCards = featured.slice(0, 2);
    const smallCards = featured.slice(2, 4);

    return (
        <PublicLayout>
            <Head title="Transtechno Cameroun — Équipements médicaux & scientifiques">
                <meta name="description" content="Importateur et distributeur d'équipements médicaux, scientifiques et de laboratoire au Cameroun. Agréé Minsanté, certifié OMS. Monitoring, imagerie, bloc opératoire, néonatologie, laboratoire." head-key="description" />
                <link rel="canonical" href="https://transtechno-cmr.com" head-key="canonical" />
                <meta property="og:url" content="https://transtechno-cmr.com" head-key="og:url" />
                <meta property="og:title" content="Transtechno Cameroun — Équipements médicaux & scientifiques" head-key="og:title" />
                <meta property="og:description" content="Importateur et distributeur d'équipements médicaux, scientifiques et de laboratoire au Cameroun. Agréé Minsanté, certifié OMS." head-key="og:description" />
                <meta property="og:type" content="website" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                <meta name="twitter:card" content="summary" head-key="twitter:card" />
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify({
                    '@context': 'https://schema.org',
                    '@type': 'Organization',
                    name: 'Transtechno Cameroun',
                    url: 'https://transtechno-cmr.com',
                    logo: 'https://transtechno-cmr.com/images/logo.png',
                    description: 'Importateur et distributeur d\'équipements médicaux, scientifiques et de laboratoire au Cameroun. Agréé Minsanté N° 0683, certifié OMS.',
                    foundingDate: '2016',
                    address: {
                        '@type': 'PostalAddress',
                        streetAddress: 'Village carrefour, entrée bille, Immeuble Pharmacie Sainte-Marie, 2ème niveau',
                        addressLocality: 'Douala',
                        addressCountry: 'CM',
                    },
                    contactPoint: [
                        { '@type': 'ContactPoint', telephone: '+237-699-67-40-36', contactType: 'customer service', availableLanguage: ['French'] },
                        { '@type': 'ContactPoint', telephone: '+237-674-64-53-54', contactType: 'sales', availableLanguage: ['French'] },
                    ],
                    email: 'office@transtechno-cmr.com',
                    sameAs: [
                        'https://www.facebook.com/TRANSTECHNO-CAMEROUN',
                    ],
                }) }} />
            </Head>

            {/* ── Hero ──────────────────────────────────────────── */}
            <section className="relative overflow-hidden bg-secondary py-xl text-white lg:py-24">
                <div className="absolute inset-0 z-0" aria-hidden="true">
                    <img
                        src="/images/hero-bg.jpg"
                        alt=""
                        className="h-full w-full scale-105 object-cover opacity-30 mix-blend-overlay"
                    />
                    <div className="absolute inset-0 bg-gradient-to-r from-secondary via-secondary/80 to-transparent" />
                </div>
                <div className="relative z-10 mx-auto max-w-7xl px-margin">
                    <div className="max-w-3xl">
                        <span className="mb-md inline-block rounded-full bg-brand-red px-3 py-1 text-[10px] font-bold uppercase tracking-widest shadow-lg">
                            Depuis 2016 au Cameroun
                        </span>
                        <h1 className="mb-md text-headline-xl leading-tight">Science technology and medical instrument</h1>
                        <p className="mb-lg max-w-2xl text-body-lg leading-relaxed text-white/90">
                            Leader dans l'importation et la commercialisation des équipements scientifiques de pointe. Nous
                            fournissons des solutions certifiées OMS pour les secteurs hospitaliers et de recherche.
                        </p>

                        <form onSubmit={submitSearch} role="search" className="flex max-w-2xl flex-col gap-md sm:flex-row">
                            <div className="relative flex flex-grow items-center overflow-hidden rounded-lg border border-white/20 bg-white shadow-xl">
                                <span className="material-symbols-outlined ml-md text-secondary" aria-hidden="true">
                                    search
                                </span>
                                <input
                                    type="search"
                                    name="q"
                                    value={query}
                                    onChange={(e) => setQuery(e.target.value)}
                                    placeholder="Rechercher un équipement…"
                                    aria-label="Rechercher un équipement"
                                    className="w-full border-none bg-transparent px-sm py-4 text-secondary placeholder:text-secondary/60 focus:ring-0"
                                />
                            </div>
                            <button
                                type="submit"
                                className="flex items-center justify-center gap-2 rounded-lg bg-brand-red px-lg py-4 font-bold text-white shadow-lg transition-colors hover:bg-primary"
                            >
                                Chercher
                                <span className="material-symbols-outlined" aria-hidden="true">
                                    search
                                </span>
                            </button>
                        </form>

                        <div className="mt-lg flex flex-wrap items-center gap-lg">
                            {TRUST.map((t) => (
                                <div key={t} className="flex items-center gap-xs">
                                    <span className="material-symbols-outlined text-brand-red" aria-hidden="true">
                                        check_circle
                                    </span>
                                    <span className="text-xs font-medium">{t}</span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Spécialités ───────────────────────────────────── */}
            <section aria-label="Domaines d'expertise" className="mx-auto max-w-7xl px-margin py-xl">
                <div className="mb-xl text-center">
                    <span className="mb-xs block text-label-caps uppercase tracking-widest text-brand-red">Domaines d'expertise</span>
                    <h2 className="text-headline-lg text-secondary">Solutions Médicales Globales</h2>
                    <div className="mx-auto mt-sm h-1 w-20 bg-brand-red" />
                </div>
                <div className="grid grid-cols-2 gap-md md:grid-cols-5">
                    {poles.map((pole) => (
                        <Link
                            key={pole.slug}
                            href={poleHref(pole.slug)}
                            className="group relative flex h-48 flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-transparent bg-navy-blue p-4 text-center text-white transition-all duration-300 hover:border-brand-red"
                        >
                            <span className="material-symbols-outlined mb-2 text-[40px] text-white/90 transition-transform duration-500 group-hover:scale-110" aria-hidden="true">
                                {pole.icon || POLE_ICONS[pole.slug] || 'medical_services'}
                            </span>
                            <span className="mb-1 block text-lg font-bold uppercase tracking-wider">{pole.name}</span>
                            <p className="line-clamp-2 text-[12px] text-white/80">{pole.description || POLE_FALLBACK_DESC}</p>
                        </Link>
                    ))}
                </div>
                <div className="mt-lg text-center">
                    <Link href="/categories" className="inline-flex items-center gap-2 font-bold text-brand-red transition-all hover:underline">
                        Voir toutes les catégories
                        <span className="material-symbols-outlined" aria-hidden="true">
                            arrow_forward
                        </span>
                    </Link>
                </div>
            </section>

            {/* ── À propos ──────────────────────────────────────── */}
            <section className="border-y border-outline-variant/30 bg-white py-xl">
                <div className="mx-auto grid max-w-7xl grid-cols-1 items-center gap-xl px-margin lg:grid-cols-2">
                    <div className="relative">
                        <div className="relative aspect-video overflow-hidden rounded-xl bg-secondary shadow-2xl">
                            <img
                                src="/images/about.jpg"
                                alt="Environnement de laboratoire et équipements scientifiques Transtechno"
                                loading="lazy"
                                className="h-full w-full object-cover opacity-90 mix-blend-luminosity"
                            />
                            <div className="absolute inset-0 bg-secondary/40 mix-blend-multiply" aria-hidden="true" />
                        </div>
                        <div className="absolute -bottom-6 -right-6 hidden rounded-lg bg-brand-red p-6 text-white shadow-xl md:block">
                            <div className="text-headline-md font-bold leading-tight">ISO &amp; OMS</div>
                            <div className="text-[10px] font-bold uppercase tracking-wider opacity-80">Normes de Qualité</div>
                        </div>
                    </div>
                    <div className="lg:pl-md">
                        <span className="mb-sm block text-label-caps font-bold uppercase tracking-widest text-brand-red">À propos de nous</span>
                        <h2 className="mb-md text-headline-lg text-secondary">Engagement &amp; Excellence Scientifique</h2>
                        <div className="space-y-md text-body-md leading-relaxed text-secondary/80">
                            <p className="border-l-4 border-secondary/20 pl-md italic">
                                TRANSTECHNO CAMEROUN s'est spécialisée dans l'importation et la commercialisation des
                                équipements scientifiques dans les domaines médicaux, physiques et géophysiques.
                            </p>
                            <p>
                                Depuis sa création, nous fournissons à nos clients des produits et services répondant aux
                                normes et exigences qualité établies par l'OMS. Notre équipe technique assure le suivi,
                                l'installation et la maintenance de chaque équipement.
                            </p>
                            <p>
                                Conscient des difficultés dans la prise en charge de l'insuffisance rénale, nous nous sommes
                                investis dans l'acquisition de technologies avancées pour la production de Dialysats.
                            </p>
                        </div>
                        <div className="mt-xl grid grid-cols-2 gap-md">
                            <div className="rounded-lg border border-outline-variant/30 bg-surface-2 p-md transition-colors hover:border-brand-red">
                                <div className="text-headline-md text-secondary">2016</div>
                                <div className="text-label-caps font-bold text-secondary/60">Année de création</div>
                            </div>
                            <div className="rounded-lg border border-outline-variant/30 bg-surface-2 p-md transition-colors hover:border-brand-red">
                                <div className="text-headline-md text-secondary">OMS</div>
                                <div className="text-label-caps font-bold text-secondary/60">Certifications</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Équipements phares ────────────────────────────── */}
            {featured.length > 0 && (
                <section className="bg-surface-1 py-xl">
                    <div className="mx-auto max-w-7xl px-margin">
                        <div className="mb-xl flex flex-col items-end justify-between gap-md md:flex-row">
                            <div>
                                <span className="mb-xs block text-label-caps font-bold uppercase tracking-widest text-brand-red">Nos équipements</span>
                                <h2 className="text-headline-lg text-secondary">Équipements Phares</h2>
                            </div>
                            <Link href="/catalogue" className="flex items-center gap-xs text-body-md font-bold text-brand-red transition-transform hover:translate-x-1">
                                Voir tout le catalogue
                                <span className="material-symbols-outlined" aria-hidden="true">
                                    arrow_forward
                                </span>
                            </Link>
                        </div>

                        <div className="grid grid-cols-1 gap-lg md:grid-cols-2 lg:grid-cols-3">
                            {/* 2 grandes cartes */}
                            {bigCards.map((p) => (
                                <div
                                    key={p.slug}
                                    className="group flex flex-col overflow-hidden rounded-xl border border-outline-variant bg-white shadow-sm transition-all duration-300 hover:shadow-xl"
                                >
                                    <Link href={`/produits/${p.slug}`} className="relative block h-64 shrink-0 bg-surface-2 p-md">
                                        {p.image ? (
                                            <img
                                                src={p.image}
                                                alt={p.name}
                                                loading="lazy"
                                                className="h-full w-full object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-110"
                                            />
                                        ) : (
                                            <span className="flex h-full w-full items-center justify-center text-secondary/30">
                                                <span className="material-symbols-outlined text-6xl" aria-hidden="true">
                                                    medical_services
                                                </span>
                                            </span>
                                        )}
                                        {p.category && (
                                            <span className="absolute left-4 top-4 rounded bg-secondary px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-white">
                                                {p.category}
                                            </span>
                                        )}
                                    </Link>
                                    <div className="flex flex-1 flex-col p-lg">
                                        <Link href={`/produits/${p.slug}`}>
                                            <h3 className="mb-sm text-headline-md font-bold text-secondary transition-colors group-hover:text-brand-red">
                                                {p.name}
                                            </h3>
                                        </Link>
                                        {p.short_description && (
                                            <p className="mb-lg line-clamp-2 text-body-sm text-secondary/70">{p.short_description}</p>
                                        )}
                                        <button
                                            type="button"
                                            onClick={() => addToQuote(p)}
                                            className="mt-auto w-full rounded-lg bg-brand-red py-3 font-bold text-white shadow-md transition-all hover:bg-primary active:scale-95"
                                        >
                                            Demander un devis
                                        </button>
                                    </div>
                                </div>
                            ))}

                            {/* Colonne composite : 2 mini-cartes */}
                            {smallCards.length > 0 && (
                                <div className="grid grid-rows-2 gap-md">
                                    {smallCards.map((p) => (
                                        <Link
                                            key={p.slug}
                                            href={`/produits/${p.slug}`}
                                            className="group flex items-center gap-md rounded-xl border border-outline-variant bg-white p-md transition-colors hover:border-brand-red"
                                        >
                                            <div className="flex h-24 w-24 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-surface-2">
                                                {p.image ? (
                                                    <img src={p.image} alt={p.name} loading="lazy" className="h-full w-full object-contain p-1 mix-blend-multiply" />
                                                ) : (
                                                    <span className="material-symbols-outlined text-3xl text-secondary transition-colors group-hover:text-brand-red" aria-hidden="true">
                                                        biotech
                                                    </span>
                                                )}
                                            </div>
                                            <div>
                                                <h4 className="mb-1 font-bold text-secondary">{p.name}</h4>
                                                {p.short_description && (
                                                    <p className="line-clamp-2 text-[12px] text-secondary/70">{p.short_description}</p>
                                                )}
                                                <span className="mt-2 inline-block text-[12px] font-bold text-brand-red">Détails →</span>
                                            </div>
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </section>
            )}

            {/* ── Partenaires ───────────────────────────────────── */}
            {partners.length > 0 && (
                <section className="overflow-hidden bg-secondary py-xl">
                    <div className="mx-auto max-w-7xl px-margin">
                        <div className="mb-xl text-center">
                            <h2 className="mb-sm text-headline-lg uppercase tracking-wide text-white">Nos partenaires</h2>
                            <p className="text-body-md text-white/70">Collaborations stratégiques avec les leaders mondiaux</p>
                            <div className="mx-auto mt-md h-1 w-12 bg-brand-red" />
                        </div>
                        <div className="marquee-group relative overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_5%,black_95%,transparent)]">
                            <ul className="flex w-max animate-marquee items-center gap-md">
                                {[...partners, ...partners].map((partner, i) => (
                                    <li
                                        key={`${partner.name}-${i}`}
                                        aria-hidden={i >= partners.length}
                                        className="flex h-28 w-52 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5 px-6 py-4 grayscale transition-all duration-300 hover:border-brand-red/50 hover:bg-white/10 hover:grayscale-0"
                                    >
                                        {partner.logo ? (
                                            <img src={partner.logo} alt={partner.name} className="max-h-16 w-full object-contain" />
                                        ) : (
                                            <span className="text-2xl font-black tracking-tighter text-white">{partner.name}</span>
                                        )}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </section>
            )}
        </PublicLayout>
    );
}
