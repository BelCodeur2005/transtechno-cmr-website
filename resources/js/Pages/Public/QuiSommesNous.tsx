import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

interface Partner {
    name: string;
    logo: string | null;
}

interface QuiSommesNousProps {
    partners: Partner[];
}

const ENGAGEMENTS = [
    {
        icon: 'verified_user',
        title: 'Qualité Certifiée',
        text: "Conformité totale sous l'agrément Minsanté N° 0683. Nous garantissons la traçabilité complète de chaque équipement médical.",
        cardHover: 'hover:border-primary',
        wrap: 'bg-primary/10 group-hover:bg-primary',
        iconCls: 'text-primary group-hover:text-white',
    },
    {
        icon: 'engineering',
        title: 'Expertise Technique',
        text: 'Installation, maintenance préventive et formation continue de votre personnel pour une exploitation optimale du matériel.',
        cardHover: 'hover:border-secondary',
        wrap: 'bg-secondary/10 group-hover:bg-secondary',
        iconCls: 'text-secondary group-hover:text-white',
    },
    {
        icon: 'biotech',
        title: 'Innovation Sociale',
        text: 'Solutions de Dialysats innovantes pour répondre aux défis majeurs de santé publique en Afrique Centrale.',
        cardHover: 'hover:border-tertiary',
        wrap: 'bg-tertiary/10 group-hover:bg-tertiary',
        iconCls: 'text-tertiary group-hover:text-white',
    },
];

const VALUES = [
    {
        letter: 'T',
        title: "Travail d'équipe",
        text: 'Nous servons mieux nos clients — médecins, cliniques — et nos partenaires en travaillant comme une véritable équipe.',
        cls: 'bg-primary/10 text-primary',
    },
    {
        letter: 'R',
        title: 'Respect',
        text: 'Nous traitons nos employés et chaque personne avec respect et dignité, de manière juste et équitable.',
        cls: 'bg-secondary/10 text-secondary',
    },
    {
        letter: 'I',
        title: 'Innovation & Intégrité',
        text: "Innovation continue dans notre domaine, et conduite de nos activités avec intégrité et les plus hauts standards d'éthique.",
        cls: 'bg-green-600/10 text-green-700',
    },
    {
        letter: 'P',
        title: 'Professionnalisme',
        text: 'Une organisation professionnelle, flexible et apprenante, au service de la santé.',
        cls: 'bg-tertiary/10 text-tertiary',
    },
];

const STATS = [
    { value: '2016', label: 'Année de création' },
    { value: '8+', label: 'Partenaires mondiaux' },
    { value: '100%', label: 'Conformité OMS' },
];

export default function QuiSommesNous({ partners }: QuiSommesNousProps) {
    return (
        <PublicLayout>
            <Head title="Qui sommes-nous ? — Transtechno Cameroun">
                <meta name="description" content="Fondée en 2016 à Douala, Transtechno Cameroun importe et commercialise des équipements médicaux, scientifiques et de laboratoire certifiés OMS. Agréé Minsanté arrêté N° 0683." head-key="description" />
                <meta property="og:title" content="Qui sommes-nous ? — Transtechno Cameroun" head-key="og:title" />
                <meta property="og:description" content="Fondée en 2016, Transtechno Cameroun est agréée Minsanté et certifiée OMS. Leader en équipements médicaux et scientifiques au Cameroun." head-key="og:description" />
                <meta property="og:type" content="website" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                <meta name="twitter:card" content="summary" head-key="twitter:card" />
            </Head>

            {/* ── Hero ──────────────────────────────────────────── */}
            <section className="relative flex min-h-[360px] items-center overflow-hidden bg-secondary py-lg lg:min-h-[560px] lg:py-xl">
                <div className="absolute inset-0 z-0">
                    <img
                        src="/images/qsn-hero.jpg"
                        alt=""
                        className="h-full w-full object-cover brightness-[.4]"
                    />
                    {/* Voile bleu (bleutage) + dégradé pour lisibilité du texte */}
                    <div className="absolute inset-0 bg-secondary/60 mix-blend-multiply" />
                    <div className="absolute inset-0 bg-gradient-to-r from-navy-blue/80 via-secondary/40 to-transparent" />
                </div>
                <div className="relative z-10 mx-auto w-full max-w-7xl px-sm sm:px-margin">
                    <div className="max-w-3xl">
                        <h1 className="mb-md text-2xl font-bold text-white sm:text-4xl lg:text-headline-xl">
                            L'Excellence Technologique au Service de la Santé au Cameroun
                        </h1>
                        <p className="border-l-4 border-primary pl-sm text-body-md text-white/90 lg:text-body-lg">
                            Depuis 2016, nous redéfinissons les standards du plateau technique hospitalier à travers
                            l'innovation et la rigueur scientifique.
                        </p>
                    </div>
                </div>
            </section>

            {/* ── Histoire & Vision ─────────────────────────────── */}
            <section className="bg-surface py-lg lg:py-xl">
                <div className="mx-auto grid max-w-7xl items-center gap-md lg:gap-lg px-sm sm:px-margin md:grid-cols-2">
                    <div className="space-y-sm">
                        <span className="text-label-caps font-bold uppercase tracking-widest text-primary">Notre Héritage</span>
                        <h2 className="text-headline-md lg:text-headline-lg text-secondary">Expertise et Conformité Internationale</h2>
                        <p className="text-body-md leading-relaxed text-secondary/80 lg:text-body-lg">
                            Transtechno Cameroun s'est imposé comme le partenaire de référence pour l'importation et la
                            distribution d'équipements scientifiques de pointe. Notre mission est d'équiper les centres de
                            santé camerounais avec des technologies de diagnostic et de soin conformes aux normes de
                            l'Organisation Mondiale de la Santé (OMS).
                        </p>
                        <p className="text-body-md text-secondary/70">
                            Nous croyons que chaque patient mérite un accès à un diagnostic précis. C'est pourquoi nous
                            sélectionnons rigoureusement nos partenaires mondiaux pour garantir une fiabilité sans faille et
                            une durabilité exceptionnelle de chaque dispositif médical installé.
                        </p>
                    </div>
                    <div className="relative">
                        <div className="absolute -inset-4 rounded-xl bg-secondary/5" aria-hidden="true" />
                        <div className="relative z-10 h-[260px] w-full overflow-hidden rounded-lg shadow-xl md:h-[400px]">
                            <img
                                src="/images/qsn-heritage.jpg"
                                alt="Salle d'hospitalisation équipée par Transtechno"
                                loading="lazy"
                                className="h-full w-full object-cover"
                            />
                            {/* Voile bleu (bleutage renforcé) — cohérent avec le hero */}
                            <div className="absolute inset-0 bg-secondary/70 mix-blend-multiply" />
                            <div className="absolute inset-0 bg-navy-blue/25" />
                            <div className="absolute inset-0 bg-gradient-to-t from-navy-blue/60 to-transparent" />
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Nos Valeurs (TRIP) ────────────────────────────── */}
            <section className="bg-white py-lg lg:py-xl">
                <div className="mx-auto max-w-7xl px-sm sm:px-margin">
                    <div className="mb-md lg:mb-lg text-center">
                        <span className="text-label-caps font-bold uppercase tracking-widest text-primary">Nos valeurs</span>
                        <h2 className="text-headline-md lg:text-headline-lg text-secondary">L'esprit TRIP</h2>
                        <p className="mx-auto mt-xs max-w-2xl text-body-md text-secondary/70">
                            Transtechno s'appuie passionnément sur ses valeurs fondamentales : Travail d'équipe, Respect,
                            Innovation, Intégrité et Professionnalisme.
                        </p>
                        <div className="mx-auto mt-sm h-1 w-20 bg-brand-red" />
                    </div>
                    <div className="grid grid-cols-1 gap-md sm:grid-cols-2 lg:grid-cols-4">
                        {VALUES.map((v) => (
                            <div
                                key={v.letter}
                                className="rounded-xl border border-outline-variant bg-surface-1 p-md lg:p-lg text-center transition-shadow hover:shadow-md"
                            >
                                <div className={`mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full ${v.cls}`}>
                                    <span className="text-headline-lg font-bold">{v.letter}</span>
                                </div>
                                <h3 className="mb-xs text-headline-md text-secondary">{v.title}</h3>
                                <p className="text-body-sm text-secondary/70">{v.text}</p>
                            </div>
                        ))}
                    </div>
                    <p className="mx-auto mt-md lg:mt-lg max-w-3xl text-center text-body-md italic text-secondary/70">
                        Transtechno est un fournisseur de solutions de santé à grande échelle, centré en continu sur les
                        besoins des patients et les besoins cliniques partout dans le pays — pour améliorer les conditions
                        médicales et réduire les coûts de santé.
                    </p>
                </div>
            </section>

            {/* ── Engagements ───────────────────────────────────── */}
            <section className="bg-surface-2 py-lg lg:py-xl">
                <div className="mx-auto max-w-7xl px-sm sm:px-margin">
                    <div className="mb-md lg:mb-lg text-center">
                        <h2 className="text-headline-md lg:text-headline-lg text-secondary">Nos Engagements Fondamentaux</h2>
                        <p className="mx-auto mt-xs max-w-xl text-body-md text-secondary/70">
                            L'alliance de la certification légale et de l'innovation sociale pour un Cameroun en meilleure
                            santé.
                        </p>
                    </div>
                    <div className="grid grid-cols-1 gap-md md:grid-cols-3">
                        {ENGAGEMENTS.map((e) => (
                            <div
                                key={e.title}
                                className={`group rounded-lg border border-outline-variant bg-white p-md lg:p-lg transition-all ${e.cardHover}`}
                            >
                                <div className={`mb-md flex h-12 w-12 items-center justify-center rounded-full transition-colors ${e.wrap}`}>
                                    <span className={`material-symbols-outlined ${e.iconCls}`} aria-hidden="true">
                                        {e.icon}
                                    </span>
                                </div>
                                <h3 className="mb-xs text-headline-md text-secondary">{e.title}</h3>
                                <p className="text-body-sm text-secondary/70">{e.text}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── Chiffres clés ─────────────────────────────────── */}
            <section className="bg-secondary py-lg text-white">
                <div className="mx-auto max-w-7xl px-sm sm:px-margin">
                    <div className="grid grid-cols-1 gap-md lg:gap-lg text-center md:grid-cols-3">
                        {STATS.map((s) => (
                            <div key={s.label}>
                                <div className="text-4xl font-bold text-[#ffb4ab] lg:text-headline-xl">{s.value}</div>
                                <div className="text-label-caps uppercase opacity-80">{s.label}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── Partenaires (carrousel) ───────────────────────── */}
            {partners.length > 0 && (
                <section className="bg-white py-lg lg:py-xl">
                    <div className="mx-auto max-w-7xl px-sm sm:px-margin">
                        <h2 className="mb-md lg:mb-lg text-center text-headline-md text-secondary">Ils nous font confiance</h2>
                    </div>
                    {/* Défilement infini — pause au survol. masque dégradé sur les bords */}
                    <div className="marquee-group relative overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_8%,black_92%,transparent)]">
                        <ul className="animate-marquee flex w-max shrink-0 items-center gap-md pr-md">
                            {[...partners, ...partners].map((partner, i) => (
                                <li
                                    key={`${partner.name}-${i}`}
                                    aria-hidden={i >= partners.length}
                                    className="flex h-28 w-52 shrink-0 items-center justify-center rounded-xl border border-outline-variant bg-white px-6 py-4 grayscale transition-all duration-300 hover:border-brand-red/40 hover:grayscale-0"
                                >
                                    {partner.logo ? (
                                        <img src={partner.logo} alt={partner.name} className="max-h-16 w-full object-contain" />
                                    ) : (
                                        <span className="text-center text-lg font-black tracking-tighter text-secondary">{partner.name}</span>
                                    )}
                                </li>
                            ))}
                        </ul>
                    </div>
                </section>
            )}

            {/* ── Localisation ──────────────────────────────────── */}
            <section className="bg-surface-1 py-lg lg:py-xl">
                <div className="mx-auto grid max-w-7xl items-center gap-md lg:gap-lg px-sm sm:px-margin md:grid-cols-2">
                    <div className="space-y-md">
                        <h2 className="text-headline-md lg:text-headline-lg text-secondary">Retrouvez-nous à Douala</h2>
                        <div className="flex items-start gap-sm">
                            <span className="material-symbols-outlined text-primary" aria-hidden="true">
                                location_on
                            </span>
                            <div>
                                <p className="text-body-md font-bold text-secondary">Siège Social</p>
                                <p className="text-secondary/70">
                                    Village Carrefour Entrée Bille,
                                    <br />
                                    Immeuble Pharmacie Sainte Marie, 2ème niveau — Douala
                                </p>
                            </div>
                        </div>
                        <div className="flex items-start gap-sm">
                            <span className="material-symbols-outlined text-primary" aria-hidden="true">
                                call
                            </span>
                            <div>
                                <p className="text-body-md font-bold text-secondary">Téléphone</p>
                                <p className="text-secondary/70">+237 674 64 53 54 / 699 67 40 36</p>
                            </div>
                        </div>
                        <div className="flex items-start gap-sm">
                            <span className="material-symbols-outlined text-primary" aria-hidden="true">
                                mail
                            </span>
                            <div>
                                <p className="text-body-md font-bold text-secondary">Email</p>
                                <a href="mailto:office@transtechno-cmr.com" className="text-tertiary hover:underline">
                                    office@transtechno-cmr.com
                                </a>
                            </div>
                        </div>
                        <Link
                            href="/contact"
                            className="mt-sm inline-flex h-11 items-center gap-2 rounded-lg bg-primary-container px-4 font-semibold text-white hover:bg-primary"
                        >
                            <span className="material-symbols-outlined text-[20px]" aria-hidden="true">
                                send
                            </span>
                            Nous contacter
                        </Link>
                    </div>
                    <div className="h-[260px] overflow-hidden rounded-lg border border-outline-variant shadow-lg md:h-[400px]">
                        <iframe
                            title="Carte — Transtechno Cameroun, Douala"
                            src="https://www.google.com/maps?q=Transtechno+Cameroun,+Douala&output=embed"
                            className="h-full w-full"
                            loading="lazy"
                            referrerPolicy="no-referrer-when-downgrade"
                        />
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
