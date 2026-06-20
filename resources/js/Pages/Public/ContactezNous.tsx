import PublicLayout from '@/Layouts/PublicLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useEffect, useState } from 'react';

const STRUCTURE_TYPES = ['Hôpital public', 'Clinique privée', 'Laboratoire', 'Industrie'];

const SUBJECTS = [
    'Demande de cotation / Devis',
    'Intervention technique SAV',
    'Information produit',
    'Partenariat',
];

const SAV_PILLARS = [
    {
        icon: 'precision_manufacturing',
        title: 'Installation & Mise en service',
        text: 'Déploiement technique rigoureux respectant les standards internationaux des constructeurs.',
    },
    {
        icon: 'school',
        title: 'Formation des utilisateurs',
        text: 'Transfert de compétences opérationnelles pour une exploitation optimale de vos équipements.',
    },
    {
        icon: 'verified',
        title: 'Maintenance préventive',
        text: 'Contrats de maintenance personnalisés pour garantir la pérennité et la précision de vos dispositifs.',
    },
];

export default function ContactezNous() {
    const page = usePage().props as { flash?: { success?: string }; company: import('@/types').CompanySettings };
    const flash = page.flash;
    const company = page.company;
    const [showSuccess, setShowSuccess] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        phone: '',
        structure: '',
        structure_type: '',
        subject: SUBJECTS[0],
        message: '',
    });

    useEffect(() => {
        if (flash?.success) {
            setShowSuccess(true);
            reset();
            const t = setTimeout(() => setShowSuccess(false), 6000);
            return () => clearTimeout(t);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [flash?.success]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/contact', { preserveScroll: true });
    };

    const fieldClass =
        'w-full rounded-lg border border-outline-variant bg-surface-2 px-3 py-2.5 text-body-md text-secondary placeholder-secondary/40 transition-colors focus:border-primary focus:ring-1 focus:ring-primary';
    const labelClass = 'text-label-caps font-semibold uppercase text-secondary/70';

    return (
        <PublicLayout>
            <Head title="Contactez-nous — Transtechno Cameroun">
                <meta name="description" content="Contactez Transtechno Cameroun pour un devis, une intervention SAV ou une information produit. Tél : +237 674 64 53 54. Email : office@transtechno-cmr.com. Douala, Cameroun." head-key="description" />
                <meta property="og:title" content="Contactez-nous — Transtechno Cameroun" head-key="og:title" />
                <meta property="og:description" content="Contactez Transtechno Cameroun pour un devis ou une intervention SAV. +237 674 64 53 54 · Douala, Cameroun." head-key="og:description" />
                <meta property="og:type" content="website" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                <meta name="twitter:card" content="summary" head-key="twitter:card" />
            </Head>

            {/* ── Hero ──────────────────────────────────────────── */}
            <section className="relative flex min-h-[440px] items-center overflow-hidden py-xl lg:min-h-[500px]">
                <div className="absolute inset-0 z-0">
                    <img src="/images/contact-hero.jpg" alt="" className="h-full w-full object-cover" />
                    <div className="absolute inset-0 bg-gradient-to-r from-dark-navy/95 via-dark-navy/80 to-dark-navy/50" />
                </div>
                <div className="relative z-10 mx-auto w-full max-w-7xl px-margin">
                    <div className="max-w-3xl">
                        <h1 className="mb-sm text-headline-xl text-white">L'expertise biomédicale à votre porte</h1>
                        <p className="text-body-lg text-white/90">
                            Contactez le partenaire certifié OMS et Minsanté au Cameroun.
                        </p>
                        <div className="mt-lg">
                            <a
                                href="#contact-form"
                                className="inline-flex h-12 items-center rounded-lg bg-primary-container px-lg font-bold text-white transition hover:brightness-110"
                            >
                                Nous écrire
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Cartes d'information ───────────────────────────── */}
            <section className="mx-auto max-w-7xl px-margin py-lg">
                <div className="grid grid-cols-1 gap-gutter md:grid-cols-4">
                    {/* Téléphone */}
                    <div className="flex flex-col items-center border-t-4 border-primary-container bg-white p-md text-center shadow-lg transition-transform hover:-translate-y-1">
                        <span className="material-symbols-outlined mb-sm text-4xl text-primary-container" aria-hidden="true">
                            call
                        </span>
                        <h3 className="mb-xs text-headline-md text-secondary">Téléphone / WhatsApp</h3>
                        <p className="text-body-md text-secondary/70">
                            <a href={`tel:${company.phone_1.replace(/\s/g, '')}`} className="hover:text-primary">{company.phone_1}</a>
                            <br />
                            <a href={`https://wa.me/${company.whatsapp}`} target="_blank" rel="noopener noreferrer" className="hover:text-primary">{company.phone_2} (WhatsApp)</a>
                        </p>
                    </div>

                    {/* Email */}
                    <div className="flex flex-col items-center border-t-4 border-primary-container bg-white p-md text-center shadow-lg transition-transform hover:-translate-y-1">
                        <span className="material-symbols-outlined mb-sm text-4xl text-primary-container" aria-hidden="true">
                            mail
                        </span>
                        <h3 className="mb-xs text-headline-md text-secondary">Email</h3>
                        <p className="break-all text-body-md text-secondary/70">
                            <a href="mailto:office@transtechno-cmr.com" className="hover:text-primary">
                                office@transtechno-cmr.com
                            </a>
                        </p>
                    </div>

                    {/* Adresse (mise en avant) */}
                    <div className="relative flex flex-col items-center gap-md overflow-hidden border-2 border-primary-container bg-white p-md text-center shadow-xl transition-transform hover:-translate-y-1 md:col-span-2 md:flex-row md:text-left">
                        <span
                            className="material-symbols-outlined pointer-events-none absolute right-2 top-2 text-9xl text-primary-container opacity-5"
                            aria-hidden="true"
                        >
                            location_on
                        </span>
                        <div className="rounded-full bg-primary-container/10 p-md">
                            <span className="material-symbols-outlined text-5xl text-primary-container" aria-hidden="true">
                                location_on
                            </span>
                        </div>
                        <div>
                            <h3 className="mb-xs text-headline-md text-secondary">Siège Social — Douala</h3>
                            <p className="text-body-md text-secondary/70">
                                Village carrefour entrée bille, 2ème niveau de l'immeuble de la pharmacie Sainte Marie,
                                Douala.
                            </p>
                            <div className="mt-xs space-y-0.5 text-body-sm text-secondary/70">
                                <p><span className="font-semibold text-secondary">Lun – Ven :</span> 08h00 – 17h00</p>
                                <p><span className="font-semibold text-secondary">Samedi :</span> 09h00 – 13h00</p>
                                <p><span className="font-semibold text-secondary">Dimanche :</span> <span className="text-primary font-medium">Fermé</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Notre Expertise SAV ───────────────────────────── */}
            <section className="bg-surface-2 py-xl">
                <div className="mx-auto max-w-7xl px-margin">
                    <div className="mb-lg text-center">
                        <h2 className="mb-xs text-headline-lg text-secondary">Notre Expertise SAV</h2>
                        <div className="mx-auto h-1 w-24 bg-primary" />
                    </div>
                    <div className="grid grid-cols-1 gap-lg md:grid-cols-3">
                        {SAV_PILLARS.map((p) => (
                            <div
                                key={p.title}
                                className="border border-outline-variant bg-white p-lg text-center shadow-sm transition-shadow hover:shadow-md"
                            >
                                <div className="mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary-container/5">
                                    <span className="material-symbols-outlined text-3xl text-primary" aria-hidden="true">
                                        {p.icon}
                                    </span>
                                </div>
                                <h3 className="mb-sm text-headline-md text-secondary">{p.title}</h3>
                                <p className="text-body-md text-secondary/70">{p.text}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── Formulaire ────────────────────────────────────── */}
            <section id="contact-form" className="scroll-mt-[180px] bg-surface py-xl">
                <div className="mx-auto max-w-6xl px-margin">
                    <div className="grid overflow-hidden rounded-xl border border-outline-variant bg-white shadow-2xl lg:grid-cols-5">
                        {/* Panneau gauche — réassurance */}
                        <aside className="relative overflow-hidden bg-dark-navy p-lg text-white lg:col-span-2 lg:p-xl">
                            <div className="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-primary-container/20 blur-2xl" aria-hidden="true" />
                            <span className="text-label-caps font-semibold uppercase tracking-widest text-[#ffb4ab]">
                                Contact direct
                            </span>
                            <h2 className="mt-xs text-headline-lg">Parlons de votre projet</h2>
                            <p className="mt-sm text-body-md text-white/80">
                                Une question technique, un besoin d'équipement ou une demande de cotation ? Notre équipe
                                vous répond sous 24h ouvrées.
                            </p>
                            <ul className="mt-lg space-y-md">
                                {[
                                    { icon: 'bolt', t: 'Réponse rapide', d: 'Prise en charge sous 24h ouvrées.' },
                                    { icon: 'request_quote', t: 'Devis gratuit', d: 'Cotation détaillée sans engagement.' },
                                    { icon: 'verified_user', t: 'Conformité Minsanté', d: 'Distribution agréée N° 0683.' },
                                ].map((b) => (
                                    <li key={b.t} className="flex items-start gap-sm">
                                        <span
                                            className="material-symbols-outlined mt-0.5 rounded-full bg-white/10 p-1.5 text-[20px] text-[#ffb4ab]"
                                            aria-hidden="true"
                                        >
                                            {b.icon}
                                        </span>
                                        <div>
                                            <p className="font-semibold">{b.t}</p>
                                            <p className="text-body-sm text-white/70">{b.d}</p>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                            <div className="mt-lg border-t border-white/10 pt-md">
                                <p className="text-label-caps uppercase tracking-wider text-white/50">Hotline SAV</p>
                                <a
                                    href="tel:+237674645354"
                                    className="mt-1 flex items-center gap-xs text-headline-md font-bold text-white transition-colors hover:text-[#ffb4ab]"
                                >
                                    <span className="material-symbols-outlined" aria-hidden="true">call</span>
                                    +237 674 64 53 54
                                </a>
                            </div>
                        </aside>

                        {/* Colonne droite — formulaire */}
                        <div className="p-md lg:col-span-3 lg:p-xl">
                            <h3 className="mb-md text-headline-md text-secondary">Formulaire de Contact Professionnel</h3>

                        {showSuccess && (
                            <div
                                role="status"
                                aria-live="polite"
                                className="mb-md flex items-start gap-sm rounded-lg border border-green-600/30 bg-green-50 p-sm text-body-sm text-green-800"
                            >
                                <span className="material-symbols-outlined text-green-700" aria-hidden="true">
                                    check_circle
                                </span>
                                <span>{flash?.success}</span>
                            </div>
                        )}

                        <form onSubmit={submit} className="space-y-md" noValidate>
                            <div className="grid grid-cols-1 gap-md md:grid-cols-2">
                                <div className="space-y-1">
                                    <label htmlFor="name" className={labelClass}>
                                        Nom complet <span className="text-primary">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        type="text"
                                        autoComplete="name"
                                        placeholder="Ex : Jean Dupont"
                                        className={fieldClass}
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        aria-invalid={!!errors.name}
                                        required
                                    />
                                    {errors.name && <p className="text-body-sm text-primary">{errors.name}</p>}
                                </div>

                                <div className="space-y-1">
                                    <label htmlFor="phone" className={labelClass}>
                                        Téléphone
                                    </label>
                                    <input
                                        id="phone"
                                        type="tel"
                                        autoComplete="tel"
                                        placeholder="+237 ..."
                                        className={fieldClass}
                                        value={data.phone}
                                        onChange={(e) => setData('phone', e.target.value)}
                                        aria-invalid={!!errors.phone}
                                    />
                                    {errors.phone && <p className="text-body-sm text-primary">{errors.phone}</p>}
                                </div>
                            </div>

                            <div className="space-y-1">
                                <label htmlFor="email" className={labelClass}>
                                    Email <span className="text-primary">*</span>
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    autoComplete="email"
                                    placeholder="vous@etablissement.cm"
                                    className={fieldClass}
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    aria-invalid={!!errors.email}
                                    required
                                />
                                {errors.email && <p className="text-body-sm text-primary">{errors.email}</p>}
                            </div>

                            <div className="grid grid-cols-1 gap-md md:grid-cols-2">
                                <div className="space-y-1">
                                    <label htmlFor="structure" className={labelClass}>
                                        Établissement
                                    </label>
                                    <input
                                        id="structure"
                                        type="text"
                                        autoComplete="organization"
                                        placeholder="Nom de votre structure"
                                        className={fieldClass}
                                        value={data.structure}
                                        onChange={(e) => setData('structure', e.target.value)}
                                    />
                                </div>

                                <div className="space-y-1">
                                    <label htmlFor="structure_type" className={labelClass}>
                                        Type d'établissement
                                    </label>
                                    <select
                                        id="structure_type"
                                        className={fieldClass}
                                        value={data.structure_type}
                                        onChange={(e) => setData('structure_type', e.target.value)}
                                    >
                                        <option value="">Choisir...</option>
                                        {STRUCTURE_TYPES.map((t) => (
                                            <option key={t} value={t}>
                                                {t}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div className="space-y-1">
                                <label htmlFor="subject" className={labelClass}>
                                    Objet de votre demande
                                </label>
                                <select
                                    id="subject"
                                    className={fieldClass}
                                    value={data.subject}
                                    onChange={(e) => setData('subject', e.target.value)}
                                >
                                    {SUBJECTS.map((s) => (
                                        <option key={s} value={s}>
                                            {s}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="space-y-1">
                                <label htmlFor="message" className={labelClass}>
                                    Message <span className="text-primary">*</span>
                                </label>
                                <textarea
                                    id="message"
                                    rows={4}
                                    placeholder="Détaillez votre besoin ici..."
                                    className={fieldClass}
                                    value={data.message}
                                    onChange={(e) => setData('message', e.target.value)}
                                    aria-invalid={!!errors.message}
                                    required
                                />
                                {errors.message && <p className="text-body-sm text-primary">{errors.message}</p>}
                            </div>

                            <div className="flex items-center gap-sm rounded-lg border-l-4 border-primary bg-surface-2 p-sm">
                                <span className="material-symbols-outlined text-primary" aria-hidden="true">
                                    verified_user
                                </span>
                                <p className="text-body-sm italic text-secondary/70">
                                    Conformité Minsanté N° 0683 — Distribution &amp; Maintenance Agréée.
                                </p>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="flex w-full items-center justify-center gap-sm rounded-lg bg-primary-container py-md text-headline-md uppercase tracking-wider text-white shadow-lg transition-all hover:brightness-110 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {processing && (
                                    <span className="h-5 w-5 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true" />
                                )}
                                {processing ? 'Envoi en cours…' : 'Envoyer ma demande'}
                            </button>
                        </form>
                        </div>
                    </div>
                </div>
            </section>

            {/* ── Carte ─────────────────────────────────────────── */}
            <section className="relative h-[450px] w-full">
                <iframe
                    title="Carte — Transtechno Cameroun, Douala"
                    src="https://www.google.com/maps?q=Transtechno+Cameroun,+Douala&output=embed"
                    className="h-full w-full border-0"
                    loading="lazy"
                    referrerPolicy="no-referrer-when-downgrade"
                    allowFullScreen
                />
                <div className="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <div className="pointer-events-auto flex items-center gap-md border-2 border-primary bg-white p-md shadow-2xl">
                        <div className="rounded bg-primary p-2 text-white">
                            <span className="material-symbols-outlined text-3xl" aria-hidden="true">
                                medical_services
                            </span>
                        </div>
                        <div>
                            <p className="text-headline-md font-bold text-secondary">Transtechno Siège</p>
                            <p className="text-body-sm text-secondary/70">Entrée Bille, Douala — Cameroun</p>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
