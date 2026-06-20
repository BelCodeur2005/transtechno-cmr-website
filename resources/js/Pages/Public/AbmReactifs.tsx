import PublicLayout from '@/Layouts/PublicLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useEffect, useState } from 'react';

/** Domaine de la gamme réactifs (catégorie ABM administrable côté back-office). */
interface Domain {
    name: string;
    slug: string;
    icon: string;
    description: string | null;
}

interface Props {
    requestTypes: string[];
    domains: Domain[];
}

/** Différenciateurs propres aux consommables / réactifs (vs équipements). */
const DIFFERENTIATORS = [
    {
        icon: 'ac_unit',
        title: 'Chaîne du froid maîtrisée',
        text: 'Transport et stockage sous température contrôlée — +2/+8 °C, −20 °C et −80 °C selon le réactif.',
        accent: 'text-tertiary',
        ring: 'ring-tertiary/20',
        bg: 'bg-tertiary/10',
    },
    {
        icon: 'verified_user',
        title: 'IVD ou Recherche',
        text: "Statut réglementaire clairement identifié pour chaque référence : diagnostic in vitro certifié ou usage recherche.",
        accent: 'text-green-700',
        ring: 'ring-green-600/20',
        bg: 'bg-green-600/10',
    },
    {
        icon: 'inventory_2',
        title: 'Traçabilité lot & péremption',
        text: 'Numéro de lot et date de péremption communiqués à chaque livraison pour une conformité totale.',
        accent: 'text-amber-700',
        ring: 'ring-amber-600/20',
        bg: 'bg-amber-600/10',
    },
    {
        icon: 'event_repeat',
        title: 'Réapprovisionnement récurrent',
        text: 'Contrats d\'approvisionnement continu pour ne jamais interrompre vos protocoles et vos analyses.',
        accent: 'text-primary',
        ring: 'ring-primary/20',
        bg: 'bg-primary/10',
    },
];

export default function AbmReactifs({ requestTypes, domains }: Props) {
    const flash = (usePage().props as { flash?: { success?: string } }).flash;
    const [showSuccess, setShowSuccess] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        structure: '',
        function: '',
        phone: '',
        email: '',
        request_type: '',
        message: '',
    });

    useEffect(() => {
        if (flash?.success) {
            setShowSuccess(true);
            reset();
            const t = setTimeout(() => setShowSuccess(false), 8000);
            return () => clearTimeout(t);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [flash?.success]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/reactifs/abm', { preserveScroll: true });
    };

    const fieldClass =
        'w-full rounded-lg border border-outline-variant bg-surface-2 px-3 py-2.5 text-body-md text-secondary placeholder-secondary/40 transition-colors focus:border-primary focus:ring-1 focus:ring-primary';
    const labelClass = 'mb-1 block text-label-caps font-semibold uppercase text-secondary/70';

    return (
        <PublicLayout>
            <Head title="Réactifs & Biologie Moléculaire — Partenaire ABM | Transtechno Cameroun">
                <meta name="description" content="Réactifs de biologie moléculaire, PCR, microbiologie et culture cellulaire via notre partenaire ABM (Applied Biological Materials). Chaîne du froid maîtrisée, traçabilité lot & péremption. Douala, Cameroun." head-key="description" />
                <meta property="og:title" content="Réactifs & Biologie Moléculaire — Partenaire ABM" head-key="og:title" />
                <meta property="og:description" content="Réactifs PCR, microbiologie et culture cellulaire via ABM. Chaîne du froid contrôlée, certifiés IVD ou recherche. Transtechno Cameroun." head-key="og:description" />
                <meta property="og:type" content="website" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                <meta name="twitter:card" content="summary" head-key="twitter:card" />
            </Head>

            {/* ── Hero (dégradé navy + double hélice ADN) ───────────── */}
            <section className="relative flex min-h-[520px] items-center overflow-hidden bg-navy-blue py-xl">
                {/* trame moléculaire */}
                <div
                    className="absolute inset-0 z-0 opacity-[0.12]"
                    style={{
                        backgroundImage:
                            'radial-gradient(circle, #67c4f0 1px, transparent 1.5px)',
                        backgroundSize: '26px 26px',
                    }}
                    aria-hidden="true"
                />
                <div className="absolute inset-0 z-0 bg-gradient-to-r from-navy-blue via-navy-blue/95 to-tertiary/30" aria-hidden="true" />

                {/* double hélice ADN décorative */}
                <svg
                    className="absolute right-0 top-1/2 z-0 hidden h-[520px] w-auto -translate-y-1/2 text-tertiary/30 lg:block"
                    viewBox="0 0 200 520"
                    fill="none"
                    aria-hidden="true"
                >
                    <path d="M50 0 C150 60, 150 120, 50 180 S-50 300, 50 360 S150 480, 50 520" stroke="currentColor" strokeWidth="3" />
                    <path d="M150 0 C50 60, 50 120, 150 180 S250 300, 150 360 S50 480, 150 520" stroke="currentColor" strokeWidth="3" />
                    {Array.from({ length: 13 }).map((_, i) => {
                        const y = i * 40 + 20;
                        return <line key={i} x1="50" y1={y} x2="150" y2={y} stroke="currentColor" strokeWidth="2" strokeDasharray="3 5" />;
                    })}
                </svg>

                <div className="relative z-10 mx-auto w-full max-w-7xl px-margin">
                    <span className="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-label-caps font-semibold uppercase tracking-wide text-white backdrop-blur">
                        <span className="material-symbols-outlined text-[18px]" aria-hidden="true">handshake</span>
                        Partenaire officiel ABM
                    </span>
                    <h1 className="mt-sm max-w-3xl text-headline-xl text-white">
                        Réactifs de biologie moléculaire &amp; microbiologie
                    </h1>
                    <p className="mt-sm max-w-2xl text-body-lg text-white/80">
                        Transtechno distribue au Cameroun la gamme <strong className="font-semibold text-white">Applied Biological Materials (ABM)</strong> —
                        des consommables de laboratoire de haute qualité, livrés sous chaîne du froid et tracés au lot près.
                    </p>
                    <div className="mt-md flex flex-wrap gap-sm">
                        <a href="#demande" className="inline-flex items-center gap-2 rounded-lg bg-primary-container px-5 py-3 text-body-md font-bold text-white transition-colors hover:bg-primary">
                            <span className="material-symbols-outlined text-[20px]" aria-hidden="true">request_quote</span>
                            Demander une cotation réactifs
                        </a>
                        <a href="#gamme" className="inline-flex items-center gap-2 rounded-lg border border-white/30 px-5 py-3 text-body-md font-semibold text-white transition-colors hover:bg-white/10">
                            <span className="material-symbols-outlined text-[20px]" aria-hidden="true">view_list</span>
                            Découvrir la gamme
                        </a>
                    </div>
                </div>
            </section>

            {/* ── Bandeau partenariat ABM ──────────────────────────── */}
            <section className="border-b border-outline-variant bg-white">
                <div className="mx-auto grid max-w-7xl grid-cols-1 gap-lg px-margin py-xl lg:grid-cols-12">
                    <div className="lg:col-span-7">
                        <p className="text-label-caps font-semibold uppercase text-primary">Le partenariat</p>
                        <h2 className="mt-1 text-headline-lg text-navy-blue">Applied Biological Materials</h2>
                        <p className="mt-sm text-body-md text-secondary/80">
                            ABM est un fabricant et fournisseur reconnu de réactifs pour les sciences de la vie : enzymes,
                            kits de biologie moléculaire, lignées cellulaires et solutions de génomique. En s'appuyant sur ce
                            partenariat, Transtechno met à la disposition des laboratoires, hôpitaux et centres de recherche
                            camerounais une gamme de consommables fiables, avec un accompagnement technique local.
                        </p>
                        <p className="mt-sm text-body-md text-secondary/80">
                            Contrairement aux équipements, les réactifs sont des <strong className="font-semibold text-secondary">consommables
                            à réapprovisionnement récurrent</strong> : nous gérons pour vous la disponibilité continue, la
                            conservation et la conformité réglementaire de chaque référence.
                        </p>
                    </div>
                    <div className="lg:col-span-5">
                        <div className="grid grid-cols-2 gap-sm">
                            {[
                                { k: 'Référence', v: 'Réactifs certifiés', i: 'workspace_premium' },
                                { k: 'Logistique', v: 'Chaîne du froid', i: 'ac_unit' },
                                { k: 'Conformité', v: 'IVD / Recherche', i: 'verified' },
                                { k: 'Support', v: 'Spécialiste dédié', i: 'support_agent' },
                            ].map((b) => (
                                <div key={b.k} className="rounded-xl border border-outline-variant bg-surface p-md">
                                    <span className="material-symbols-outlined text-[26px] text-tertiary" aria-hidden="true">{b.i}</span>
                                    <p className="mt-1 text-label-caps uppercase text-secondary/50">{b.k}</p>
                                    <p className="text-body-md font-bold text-navy-blue">{b.v}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Gamme : domaines (pilotés par les catégories réactifs en BDD) ── */}
            <section id="gamme" className="mx-auto max-w-7xl px-margin py-xl">
                <div className="mb-lg max-w-2xl">
                    <p className="text-label-caps font-semibold uppercase text-primary">La gamme</p>
                    <h2 className="mt-1 text-headline-lg text-navy-blue">Nos domaines d'expertise</h2>
                    <p className="mt-sm text-body-md text-secondary/70">
                        De la PCR à l'édition génique, une couverture complète des besoins en réactifs de vos protocoles.
                    </p>
                </div>
                <div className="grid grid-cols-1 gap-md sm:grid-cols-2 lg:grid-cols-3">
                    {domains.map((d) => (
                        <a key={d.slug} href={`/reactifs?categorie=${d.slug}`} className="group flex flex-col rounded-xl border border-outline-variant bg-white p-lg transition-shadow hover:shadow-lg">
                            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-tertiary/10 text-tertiary transition-colors group-hover:bg-tertiary group-hover:text-white">
                                <span className="material-symbols-outlined text-[26px]" aria-hidden="true">{d.icon}</span>
                            </div>
                            <h3 className="mt-sm text-headline-md text-secondary">{d.name}</h3>
                            {d.description && <p className="mt-xs flex-1 text-body-md text-secondary/70">{d.description}</p>}
                            <span className="mt-sm inline-flex items-center gap-1 text-body-sm font-semibold text-tertiary">
                                Voir les références
                                <span className="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-0.5" aria-hidden="true">arrow_forward</span>
                            </span>
                        </a>
                    ))}
                    {/* carte CTA catalogue */}
                    <a href="/reactifs" className="flex flex-col justify-center rounded-xl border border-dashed border-tertiary/40 bg-tertiary/5 p-lg text-tertiary transition-colors hover:bg-tertiary/10">
                        <span className="material-symbols-outlined text-[30px]" aria-hidden="true">arrow_circle_right</span>
                        <h3 className="mt-sm text-headline-md">Parcourir le catalogue réactifs</h3>
                        <p className="mt-xs text-body-md text-tertiary/80">Toutes les références disponibles, filtrables par domaine.</p>
                    </a>
                </div>
            </section>

            {/* ── Différenciateurs réactifs ────────────────────────── */}
            <section className="bg-surface py-xl">
                <div className="mx-auto max-w-7xl px-margin">
                    <div className="mb-lg max-w-2xl">
                        <p className="text-label-caps font-semibold uppercase text-primary">Notre exigence</p>
                        <h2 className="mt-1 text-headline-lg text-navy-blue">Un réactif n'est pas un équipement</h2>
                        <p className="mt-sm text-body-md text-secondary/70">
                            La valeur d'un consommable se joue dans sa conservation et sa conformité. Voilà ce que nous garantissons.
                        </p>
                    </div>
                    <div className="grid grid-cols-1 gap-md sm:grid-cols-2 lg:grid-cols-4">
                        {DIFFERENTIATORS.map((d) => (
                            <div key={d.title} className="rounded-xl border border-outline-variant bg-white p-lg">
                                <div className={`flex h-12 w-12 items-center justify-center rounded-full ${d.bg} ${d.accent} ring-1 ${d.ring}`}>
                                    <span className="material-symbols-outlined text-[26px]" aria-hidden="true">{d.icon}</span>
                                </div>
                                <h3 className="mt-sm text-headline-md text-secondary">{d.title}</h3>
                                <p className="mt-xs text-body-md text-secondary/70">{d.text}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── Formulaire demande spécialisée ───────────────────── */}
            <section id="demande" className="mx-auto max-w-7xl px-margin py-xl">
                <div className="grid grid-cols-1 gap-lg lg:grid-cols-12">
                    {/* Aside */}
                    <div className="lg:col-span-4">
                        <p className="text-label-caps font-semibold uppercase text-primary">Contact spécialisé</p>
                        <h2 className="mt-1 text-headline-lg text-navy-blue">Parlez à notre spécialiste réactifs</h2>
                        <p className="mt-sm text-body-md text-secondary/80">
                            Indiquez-nous vos références ABM, vos volumes et vos contraintes de conservation : nous vous
                            répondons avec la disponibilité, la chaîne du froid requise et la tarification.
                        </p>
                        <ul className="mt-md space-y-2">
                            {['Cotation au lot ou par contrat récurrent', 'Conseil sur la conservation et le transport', 'Statut IVD / Recherche précisé par référence'].map((c) => (
                                <li key={c} className="flex items-start gap-2 text-body-md text-secondary/80">
                                    <span className="material-symbols-outlined mt-0.5 text-[20px] text-tertiary" aria-hidden="true">check_circle</span>
                                    {c}
                                </li>
                            ))}
                        </ul>
                        <div className="mt-md rounded-xl border border-outline-variant bg-surface p-md">
                            <p className="flex items-center gap-2 text-body-sm font-bold text-secondary">
                                <span className="material-symbols-outlined text-primary" aria-hidden="true">call</span>
                                Ligne directe
                            </p>
                            <a href="tel:+237699674036" className="mt-1 block text-headline-md font-bold text-primary hover:underline">+237 699 67 40 36</a>
                            <p className="text-body-sm text-secondary/60">Lun–Ven 8h–17h · Sam 9h–13h</p>
                        </div>
                    </div>

                    {/* Formulaire */}
                    <div className="lg:col-span-8">
                        {showSuccess && (
                            <div className="mb-md flex items-start gap-2 rounded-lg border border-green-200 bg-green-50 p-md text-body-md text-green-800" role="status">
                                <span className="material-symbols-outlined text-green-600" aria-hidden="true">task_alt</span>
                                Votre demande a bien été transmise. Notre spécialiste réactifs vous recontactera rapidement.
                            </div>
                        )}
                        <form onSubmit={submit} className="rounded-xl border border-outline-variant bg-white p-lg">
                            <div className="grid grid-cols-1 gap-sm sm:grid-cols-2">
                                <div>
                                    <label htmlFor="name" className={labelClass}>Nom complet *</label>
                                    <input id="name" type="text" value={data.name} onChange={(e) => setData('name', e.target.value)} className={fieldClass} required />
                                    {errors.name && <p className="mt-1 text-body-sm text-primary">{errors.name}</p>}
                                </div>
                                <div>
                                    <label htmlFor="structure" className={labelClass}>Laboratoire / Institution *</label>
                                    <input id="structure" type="text" value={data.structure} onChange={(e) => setData('structure', e.target.value)} className={fieldClass} required />
                                    {errors.structure && <p className="mt-1 text-body-sm text-primary">{errors.structure}</p>}
                                </div>
                                <div>
                                    <label htmlFor="function" className={labelClass}>Fonction</label>
                                    <input id="function" type="text" value={data.function} onChange={(e) => setData('function', e.target.value)} className={fieldClass} placeholder="Ex : Responsable de laboratoire" />
                                </div>
                                <div>
                                    <label htmlFor="phone" className={labelClass}>Téléphone *</label>
                                    <input id="phone" type="tel" value={data.phone} onChange={(e) => setData('phone', e.target.value)} className={fieldClass} required />
                                    {errors.phone && <p className="mt-1 text-body-sm text-primary">{errors.phone}</p>}
                                </div>
                                <div className="sm:col-span-2">
                                    <label htmlFor="email" className={labelClass}>Email</label>
                                    <input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className={fieldClass} />
                                    {errors.email && <p className="mt-1 text-body-sm text-primary">{errors.email}</p>}
                                </div>
                                <div className="sm:col-span-2">
                                    <label htmlFor="request_type" className={labelClass}>Nature de la demande *</label>
                                    <select id="request_type" value={data.request_type} onChange={(e) => setData('request_type', e.target.value)} className={fieldClass} required>
                                        <option value="" disabled>Sélectionnez un domaine</option>
                                        {requestTypes.map((t) => (
                                            <option key={t} value={t}>{t}</option>
                                        ))}
                                    </select>
                                    {errors.request_type && <p className="mt-1 text-body-sm text-primary">{errors.request_type}</p>}
                                </div>
                                <div className="sm:col-span-2">
                                    <label htmlFor="message" className={labelClass}>Références &amp; détails</label>
                                    <textarea id="message" rows={4} value={data.message} onChange={(e) => setData('message', e.target.value)} className={fieldClass} placeholder="Références ABM, volumes, conditionnement, contraintes de conservation…" />
                                </div>
                            </div>

                            <button type="submit" disabled={processing} className="mt-md flex w-full items-center justify-center gap-2 rounded-lg bg-primary-container py-3.5 text-body-md font-bold text-white transition-colors hover:bg-primary disabled:cursor-not-allowed disabled:opacity-60">
                                {processing ? (
                                    <>
                                        <span className="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true" />
                                        Envoi en cours…
                                    </>
                                ) : (
                                    <>
                                        <span className="material-symbols-outlined text-lg" aria-hidden="true">send</span>
                                        Envoyer ma demande
                                    </>
                                )}
                            </button>
                            <p className="mt-sm flex items-start gap-2 text-body-sm text-secondary/60">
                                <span className="material-symbols-outlined text-[18px] text-tertiary" aria-hidden="true">lock</span>
                                Vos données sont sécurisées et traitées par notre spécialiste réactifs &amp; biologie moléculaire.
                            </p>
                        </form>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
