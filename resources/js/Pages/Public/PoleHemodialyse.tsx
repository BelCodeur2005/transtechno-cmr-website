import PublicLayout from '@/Layouts/PublicLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useEffect, useState } from 'react';

interface Props {
    projectTypes: string[];
}

const EXPERTISES = [
    {
        icon: 'medical_services',
        title: "Générateurs d'Hémodialyse",
        text: 'Fourniture, installation et étalonnage de générateurs de dialyse de dernière génération issus de nos partenaires mondiaux.',
    },
    {
        icon: 'water_drop',
        title: "Traitement de l'eau (Osmose)",
        text: "Conception et déploiement de centrales de traitement d'eau par osmose inverse, garantissant une pureté absolue pour la sécurité des patients.",
    },
    {
        icon: 'science',
        title: 'Production de Dialysats',
        text: 'Production locale et approvisionnement continu en concentrés pour hémodialyse respectant les normes OMS et Minsanté.',
    },
];

const COMMITMENTS = [
    'SAV technique 24/7 sur toute l\'étendue du territoire camerounais.',
    'Stock local permanent de pièces de rechange et consommables.',
    'Accompagnement stratégique pour le montage de vos appels d\'offres.',
];

const STATS = ['Maintenance Préventive', 'Stock Local', 'Ingénieurs Certifiés', 'Zéro Panne'];

export default function PoleHemodialyse({ projectTypes }: Props) {
    const flash = (usePage().props as { flash?: { success?: string } }).flash;
    const [showSuccess, setShowSuccess] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        structure: '',
        function: '',
        phone: '',
        email: '',
        project_type: '',
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
        post('/pole-hemodialyse', { preserveScroll: true });
    };

    const fieldClass =
        'w-full rounded-lg border border-outline-variant bg-surface-2 px-3 py-2.5 text-body-md text-secondary placeholder-secondary/40 transition-colors focus:border-primary focus:ring-1 focus:ring-primary';
    const labelClass = 'mb-1 block text-label-caps font-semibold uppercase text-secondary/70';

    return (
        <PublicLayout>
            <Head title="Pôle Hémodialyse & Traitement des Eaux — Transtechno Cameroun">
                <meta name="description" content="Générateurs d'hémodialyse, traitement de l'eau par osmose inverse et production de dialysats certifiés. SAV 24/7 sur tout le Cameroun. Transtechno Cameroun, agréé Minsanté." head-key="description" />
                <meta property="og:title" content="Pôle Hémodialyse & Traitement des Eaux — Transtechno Cameroun" head-key="og:title" />
                <meta property="og:description" content="Générateurs d'hémodialyse, osmose inverse et dialysats certifiés OMS. SAV 24/7 sur tout le Cameroun." head-key="og:description" />
                <meta property="og:type" content="website" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                <meta name="twitter:card" content="summary" head-key="twitter:card" />
            </Head>

            {/* ── Hero ─────────────────────────────────────────── */}
            <section className="relative flex min-h-[360px] items-center overflow-hidden py-lg lg:min-h-[480px] lg:py-xl">
                <div className="absolute inset-0 z-0">
                    <img src="/images/hemo-hero.jpg" alt="" className="h-full w-full object-cover" />
                    <div className="absolute inset-0 bg-gradient-to-r from-navy-blue via-navy-blue/90 to-tertiary/40" />
                </div>
                <div className="relative z-10 mx-auto w-full max-w-7xl px-sm sm:px-margin">
                    <p className="text-label-caps font-semibold uppercase text-tertiary-fixed-dim">Pôle d'Excellence</p>
                    <h1 className="mt-1 max-w-3xl text-2xl font-bold text-white sm:text-4xl lg:text-headline-xl">
                        Solutions intégrales pour l'Hémodialyse et le Traitement des Eaux
                    </h1>
                    <p className="mt-sm max-w-2xl text-body-md text-white/80 lg:text-body-lg">
                        Accompagnement de bout en bout pour la création et la maintenance de centres de dialyse au Cameroun :
                        de l'installation des générateurs à la production locale de dialysats certifiés.
                    </p>
                    <a href="#projet" className="mt-md inline-flex items-center gap-2 rounded-lg bg-primary-container px-5 py-3 text-body-md font-bold text-white transition-colors hover:bg-primary">
                        <span className="material-symbols-outlined text-[20px]" aria-hidden="true">engineering</span>
                        Parler à un ingénieur spécialisé
                    </a>
                </div>
            </section>

            {/* ── 3 expertises ─────────────────────────────────── */}
            <section className="mx-auto max-w-7xl px-sm sm:px-margin py-lg lg:py-xl">
                <div className="mb-md lg:mb-lg text-center">
                    <h2 className="text-headline-md lg:text-headline-lg text-navy-blue">Une maîtrise complète de la chaîne de dialyse</h2>
                </div>
                <div className="grid grid-cols-1 gap-md md:grid-cols-3">
                    {EXPERTISES.map((e) => (
                        <div key={e.title} className="rounded-xl border border-outline-variant bg-white p-md lg:p-lg text-center transition-shadow hover:shadow-lg">
                            <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-tertiary/10 text-tertiary">
                                <span className="material-symbols-outlined text-[28px]" aria-hidden="true">{e.icon}</span>
                            </div>
                            <h3 className="mt-sm text-headline-md text-secondary">{e.title}</h3>
                            <p className="mt-xs text-body-md text-secondary/70">{e.text}</p>
                        </div>
                    ))}
                </div>
            </section>

            {/* ── Pourquoi + engagements ───────────────────────── */}
            <section className="bg-navy-blue py-lg lg:py-xl text-white">
                <div className="mx-auto grid max-w-7xl grid-cols-1 gap-md lg:gap-lg px-sm sm:px-margin lg:grid-cols-2">
                    <div>
                        <h2 className="text-headline-md lg:text-headline-lg">Pourquoi choisir Transtechno pour votre centre ?</h2>
                        <p className="mt-sm text-body-md text-white/80">
                            L'hémodialyse est une spécialité critique qui ne tolère aucune panne — un arrêt technique peut
                            mettre des vies en danger. C'est pourquoi nous avons structuré notre offre autour d'une
                            réactivité absolue.
                        </p>
                        <ul className="mt-md space-y-2">
                            {COMMITMENTS.map((c) => (
                                <li key={c} className="flex items-start gap-2 text-body-md text-white/90">
                                    <span className="material-symbols-outlined mt-0.5 text-[20px] text-green-400" aria-hidden="true">check_circle</span>
                                    {c}
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div className="grid grid-cols-2 gap-sm self-center">
                        {STATS.map((s) => (
                            <div key={s} className="rounded-xl bg-white/5 p-md text-center ring-1 ring-white/10">
                                <span className="material-symbols-outlined text-[28px] text-tertiary-fixed-dim" aria-hidden="true">verified</span>
                                <p className="mt-1 text-body-md font-bold">{s}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── Formulaire projet ────────────────────────────── */}
            <section id="projet" className="mx-auto max-w-7xl px-sm sm:px-margin py-lg lg:py-xl">
                <div className="grid grid-cols-1 gap-md lg:gap-lg lg:grid-cols-12">
                    {/* Aside */}
                    <div className="lg:col-span-4">
                        <p className="text-label-caps font-semibold uppercase text-primary">Notre engagement technique</p>
                        <h2 className="mt-1 text-headline-md lg:text-headline-lg text-navy-blue">Présentez-nous votre projet</h2>
                        <p className="mt-sm text-body-md text-secondary/80">
                            Directeur d'hôpital, promoteur de clinique ou investisseur ? Nos experts vous accompagnent dans le
                            dimensionnement technique et financier de votre futur centre de dialyse.
                        </p>
                        <div className="mt-md rounded-xl border border-outline-variant bg-surface p-md">
                            <p className="flex items-center gap-2 text-body-sm font-bold text-secondary">
                                <span className="material-symbols-outlined text-primary" aria-hidden="true">emergency</span>
                                Hotline spécialisée
                            </p>
                            <a href="tel:+237674645354" className="mt-1 block text-headline-md font-bold text-primary hover:underline">+237 674 64 53 54</a>
                            <p className="text-body-sm text-secondary/60">Disponible 24h/24 pour les urgences</p>
                        </div>
                    </div>

                    {/* Formulaire */}
                    <div className="lg:col-span-8">
                        {showSuccess && (
                            <div className="mb-md flex items-start gap-2 rounded-lg border border-green-200 bg-green-50 p-md text-body-md text-green-800" role="status">
                                <span className="material-symbols-outlined text-green-600" aria-hidden="true">task_alt</span>
                                Votre demande de projet a bien été transmise. Notre département d'ingénierie vous recontactera rapidement.
                            </div>
                        )}
                        <form onSubmit={submit} className="rounded-xl border border-outline-variant bg-white p-md lg:p-lg">
                            <div className="grid grid-cols-1 gap-sm sm:grid-cols-2">
                                <div>
                                    <label htmlFor="name" className={labelClass}>Nom complet *</label>
                                    <input id="name" type="text" value={data.name} onChange={(e) => setData('name', e.target.value)} className={fieldClass} required />
                                    {errors.name && <p className="mt-1 text-body-sm text-primary">{errors.name}</p>}
                                </div>
                                <div>
                                    <label htmlFor="structure" className={labelClass}>Établissement / Clinique *</label>
                                    <input id="structure" type="text" value={data.structure} onChange={(e) => setData('structure', e.target.value)} className={fieldClass} required />
                                    {errors.structure && <p className="mt-1 text-body-sm text-primary">{errors.structure}</p>}
                                </div>
                                <div>
                                    <label htmlFor="function" className={labelClass}>Fonction</label>
                                    <input id="function" type="text" value={data.function} onChange={(e) => setData('function', e.target.value)} className={fieldClass} placeholder="Ex : Directeur médical" />
                                </div>
                                <div>
                                    <label htmlFor="phone" className={labelClass}>Téléphone *</label>
                                    <input id="phone" type="tel" value={data.phone} onChange={(e) => setData('phone', e.target.value)} className={fieldClass} required />
                                    {errors.phone && <p className="mt-1 text-body-sm text-primary">{errors.phone}</p>}
                                </div>
                                <div className="sm:col-span-2">
                                    <label htmlFor="email" className={labelClass}>Email *</label>
                                    <input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className={fieldClass} required />
                                    {errors.email && <p className="mt-1 text-body-sm text-primary">{errors.email}</p>}
                                </div>
                                <div className="sm:col-span-2">
                                    <label htmlFor="project_type" className={labelClass}>Nature du projet *</label>
                                    <select id="project_type" value={data.project_type} onChange={(e) => setData('project_type', e.target.value)} className={fieldClass} required>
                                        <option value="" disabled>Sélectionnez une option</option>
                                        {projectTypes.map((t) => (
                                            <option key={t} value={t}>{t}</option>
                                        ))}
                                    </select>
                                    {errors.project_type && <p className="mt-1 text-body-sm text-primary">{errors.project_type}</p>}
                                </div>
                                <div className="sm:col-span-2">
                                    <label htmlFor="message" className={labelClass}>Détails du projet</label>
                                    <textarea id="message" rows={4} value={data.message} onChange={(e) => setData('message', e.target.value)} className={fieldClass} placeholder="Capacité visée, localisation, échéances…" />
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
                                        Envoyer ma demande de projet
                                    </>
                                )}
                            </button>
                            <p className="mt-sm flex items-start gap-2 text-body-sm text-secondary/60">
                                <span className="material-symbols-outlined text-[18px] text-tertiary" aria-hidden="true">lock</span>
                                Vos données sont sécurisées et traitées par notre département d'ingénierie biomédicale.
                            </p>
                        </form>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
