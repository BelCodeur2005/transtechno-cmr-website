import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useEffect, useRef, useState } from 'react';

interface Props {
    interventionTypes: string[];
}

const PRIORITIES = [
    {
        value: 'Normal',
        icon: 'check_circle',
        label: 'Normal',
        desc: 'Fonctionnement dégradé, intervention planifiable sous 48–72 h.',
        cls: 'border-outline-variant',
        selected: 'border-tertiary bg-tertiary/5',
        iconCls: 'text-tertiary',
    },
    {
        value: 'Critique',
        icon: 'warning',
        label: 'Critique',
        desc: "Équipement à l'arrêt, activité médicale impactée — intervention sous 24 h.",
        cls: 'border-outline-variant',
        selected: 'border-amber-500 bg-amber-50',
        iconCls: 'text-amber-600',
    },
    {
        value: 'Urgent',
        icon: 'emergency',
        label: 'Urgent',
        desc: "Urgence vitale ou bloc opératoire bloqué — intervention immédiate requise.",
        cls: 'border-outline-variant',
        selected: 'border-primary bg-primary/5',
        iconCls: 'text-primary',
    },
];

const STEPS = [
    { icon: 'confirmation_number', title: 'Ticket ouvert', desc: 'Référence générée instantanément.' },
    { icon: 'support_agent', title: 'Pré-diagnostic', desc: 'Notre technicien vous appelle sous 2 h.' },
    { icon: 'local_shipping', title: 'Intervention', desc: 'Déploiement sur site planifié.' },
    { icon: 'task_alt', title: 'Rapport', desc: 'Compte-rendu écrit remis à la clôture.' },
];

export default function SavDemande({ interventionTypes }: Props) {
    const flash = (usePage().props as { flash?: { success?: string } }).flash;
    const [submitted, setSubmitted] = useState(false);
    const [ticketRef, setTicketRef] = useState('');
    const interventionSectionRef = useRef<HTMLElement>(null);

    const { data, setData, post, processing, errors, reset } = useForm<{
        name: string;
        structure: string;
        city: string;
        phone: string;
        email: string;
        intervention_type: string;
        equipments: { brand: string; model: string; serial: string }[];
        problem: string;
        priority: string;
    }>({
        name: '',
        structure: '',
        city: '',
        phone: '',
        email: '',
        intervention_type: '',
        equipments: [{ brand: '', model: '', serial: '' }],
        problem: '',
        priority: 'Normal',
    });

    useEffect(() => {
        if (flash?.success) {
            const match = flash.success.match(/SAV-\d{4}-\d+/);
            if (match) setTicketRef(match[0]);
            setSubmitted(true);
            reset();
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [flash?.success]);

    // Scroll générique vers la première erreur après un submit raté
    useEffect(() => {
        const hasErrors = Object.keys(errors).length > 0;
        if (!hasErrors) return;

        // Cas particulier : intervention_type est dans la sidebar — on cible la section directement
        if (errors.intervention_type && interventionSectionRef.current) {
            interventionSectionRef.current.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Cas général : scroll vers le premier champ en erreur par id ou name
        const firstKey = Object.keys(errors)[0];
        const el = document.getElementById(firstKey) ?? document.querySelector<HTMLElement>(`[name="${firstKey}"]`);
        el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el?.focus();
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [JSON.stringify(errors)]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/sav/demande', { preserveScroll: true });
    };

    const fieldClass =
        'w-full rounded-lg border border-outline-variant bg-white px-3 py-2.5 text-body-md text-secondary placeholder-secondary/40 transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary';
    const labelClass = 'mb-1 block text-label-caps font-semibold uppercase tracking-wide text-secondary/70';

    if (submitted) {
        return (
            <PublicLayout>
                <Head title="Ticket SAV ouvert — Transtechno Cameroun" />
                <section className="flex min-h-[70vh] items-center justify-center px-margin py-xl">
                    <div className="w-full max-w-lg text-center">
                        <div className="mx-auto mb-md flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
                            <span className="material-symbols-outlined text-[40px] text-green-600" aria-hidden="true">check_circle</span>
                        </div>
                        <h1 className="text-headline-lg text-navy-blue">Ticket ouvert avec succès</h1>
                        {ticketRef && (
                            <div className="mt-sm inline-block rounded-lg bg-navy-blue px-md py-sm">
                                <p className="text-label-caps uppercase text-white/70">Votre référence</p>
                                <p className="text-headline-md font-bold text-white">#{ticketRef}</p>
                            </div>
                        )}
                        <p className="mt-md text-body-md text-secondary/70">
                            Notre équipe technique prend en charge votre demande et vous recontacte dans les meilleurs délais.
                            Conservez votre référence pour le suivi.
                        </p>
                        <div className="mt-lg flex flex-wrap justify-center gap-sm">
                            <Link
                                href="/services-sav"
                                className="inline-flex items-center gap-2 rounded-lg border border-outline-variant px-5 py-2.5 text-body-md font-semibold text-secondary hover:bg-surface-2"
                            >
                                <span className="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
                                Retour aux services
                            </Link>
                            <a
                                href="tel:+237674645354"
                                className="inline-flex items-center gap-2 rounded-lg bg-primary-container px-5 py-2.5 text-body-md font-bold text-white transition-colors hover:bg-primary"
                            >
                                <span className="material-symbols-outlined text-[18px]" aria-hidden="true">call</span>
                                Appeler le SAV
                            </a>
                        </div>
                    </div>
                </section>
            </PublicLayout>
        );
    }

    return (
        <PublicLayout>
            <Head title="Ouvrir un ticket SAV — Transtechno Cameroun">
                <meta name="description" content="Déposez votre demande d'assistance technique SAV en ligne. Réparation, maintenance, installation ou pièces de rechange — notre équipe intervient sur tout le territoire camerounais." head-key="description" />
            </Head>

            {/* ── Hero compact ───────────────────────────────────── */}
            <section className="relative overflow-hidden bg-navy-blue py-lg">
                <div className="absolute inset-0 z-0 opacity-[0.06]"
                    style={{ backgroundImage: "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")" }}
                />
                <div className="relative z-10 mx-auto max-w-7xl px-margin">
                    <Link
                        href="/services-sav"
                        className="mb-sm inline-flex items-center gap-1 text-body-sm font-semibold text-white/60 transition-colors hover:text-white"
                    >
                        <span className="material-symbols-outlined text-[16px]" aria-hidden="true">arrow_back</span>
                        Services & SAV
                    </Link>
                    <div className="flex flex-col gap-md lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div className="mb-xs inline-flex items-center gap-2 rounded-full bg-primary-container/20 px-3 py-1 ring-1 ring-white/20">
                                <span className="h-2 w-2 animate-pulse rounded-full bg-green-400" aria-hidden="true" />
                                <span className="text-label-caps font-semibold uppercase text-white">Assistance technique 24/7</span>
                            </div>
                            <h1 className="text-headline-xl text-white">Ouvrir un ticket SAV</h1>
                            <p className="mt-xs max-w-xl text-body-lg text-white/75">
                                Décrivez votre panne ou besoin d'intervention. Un technicien certifié prend en charge votre demande sous 2 h.
                            </p>
                        </div>
                        <a
                            href="tel:+237674645354"
                            className="inline-flex w-fit items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-md py-sm text-white transition-colors hover:bg-white/20"
                        >
                            <span className="material-symbols-outlined text-[28px] text-primary-container" aria-hidden="true">call</span>
                            <div>
                                <p className="text-label-caps uppercase text-white/60">Hotline SAV directe</p>
                                <p className="text-body-lg font-bold">+237 674 64 53 54</p>
                            </div>
                        </a>
                    </div>
                </div>
            </section>

            {/* ── Processus (4 étapes) ────────────────────────────── */}
            <section className="border-b border-outline-variant bg-surface-2">
                <div className="mx-auto max-w-7xl px-margin py-md">
                    <ol className="grid grid-cols-2 gap-sm lg:grid-cols-4">
                        {STEPS.map((s, i) => (
                            <li key={s.title} className="flex items-start gap-2">
                                <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-container/10 text-label-caps font-bold text-primary-container">
                                    {i + 1}
                                </span>
                                <div>
                                    <p className="text-body-sm font-bold text-secondary">{s.title}</p>
                                    <p className="text-body-sm text-secondary/60">{s.desc}</p>
                                </div>
                            </li>
                        ))}
                    </ol>
                </div>
            </section>

            {/* ── Formulaire ──────────────────────────────────────── */}
            <main className="mx-auto max-w-7xl px-margin py-xl">
                <form onSubmit={submit} className="grid grid-cols-1 gap-lg lg:grid-cols-12">

                    {/* ── Colonne principale ────────────────── */}
                    <div className="flex flex-col gap-lg lg:col-span-8">

                        {/* Section 1 : Contact */}
                        <section className="rounded-xl border border-outline-variant bg-white">
                            <header className="flex items-center gap-2 border-b border-outline-variant px-md py-sm">
                                <span className="flex h-8 w-8 items-center justify-center rounded-full bg-navy-blue text-label-caps font-bold text-white">1</span>
                                <h2 className="text-headline-md text-secondary">Informations de contact</h2>
                            </header>
                            <div className="grid grid-cols-1 gap-md p-md sm:grid-cols-2">
                                <div>
                                    <label htmlFor="name" className={labelClass}>Nom complet *</label>
                                    <input
                                        id="name" type="text" value={data.name} required
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="Dr. Jean Mbarga"
                                        className={fieldClass}
                                    />
                                    {errors.name && <p className="mt-1 text-body-sm text-primary">{errors.name}</p>}
                                </div>
                                <div>
                                    <label htmlFor="structure" className={labelClass}>Établissement *</label>
                                    <input
                                        id="structure" type="text" value={data.structure} required
                                        onChange={(e) => setData('structure', e.target.value)}
                                        placeholder="Hôpital, Clinique, Laboratoire…"
                                        className={fieldClass}
                                    />
                                    {errors.structure && <p className="mt-1 text-body-sm text-primary">{errors.structure}</p>}
                                </div>
                                <div>
                                    <label htmlFor="phone" className={labelClass}>Téléphone *</label>
                                    <input
                                        id="phone" type="tel" value={data.phone} required
                                        onChange={(e) => setData('phone', e.target.value)}
                                        placeholder="+237 6__ __ __ __"
                                        className={fieldClass}
                                    />
                                    {errors.phone && <p className="mt-1 text-body-sm text-primary">{errors.phone}</p>}
                                </div>
                                <div>
                                    <label htmlFor="email" className={labelClass}>Email</label>
                                    <input
                                        id="email" type="email" value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="contact@etablissement.cm"
                                        className={fieldClass}
                                    />
                                    {errors.email && <p className="mt-1 text-body-sm text-primary">{errors.email}</p>}
                                </div>
                                <div className="sm:col-span-2">
                                    <label htmlFor="city" className={labelClass}>Ville / Localité</label>
                                    <input
                                        id="city" type="text" value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                        placeholder="Douala, Yaoundé, Bafoussam…"
                                        className={fieldClass}
                                    />
                                </div>
                            </div>
                        </section>

                        {/* Section 2 : Équipements (repeater) */}
                        <section className="rounded-xl border border-outline-variant bg-white">
                            <header className="flex items-center gap-2 border-b border-outline-variant px-md py-sm">
                                <span className="flex h-8 w-8 items-center justify-center rounded-full bg-navy-blue text-label-caps font-bold text-white">2</span>
                                <h2 className="text-headline-md text-secondary">Équipement(s) concerné(s)</h2>
                            </header>
                            <div className="p-md">
                                <div className="flex flex-col gap-md">
                                    {data.equipments.map((eq, index) => (
                                        <div key={index} className={`rounded-lg border bg-surface-2 p-sm ${data.equipments.length > 1 ? 'border-outline-variant' : 'border-transparent'}`}>
                                            {data.equipments.length > 1 && (
                                                <div className="mb-xs flex items-center justify-between">
                                                    <span className="text-label-caps font-semibold uppercase tracking-wide text-secondary/50">Appareil {index + 1}</span>
                                                    <button
                                                        type="button"
                                                        onClick={() => setData('equipments', data.equipments.filter((_, i) => i !== index))}
                                                        className="flex items-center gap-1 text-body-sm text-primary transition-colors hover:text-primary/70"
                                                    >
                                                        <span className="material-symbols-outlined text-[15px]" aria-hidden="true">delete</span>
                                                        Supprimer
                                                    </button>
                                                </div>
                                            )}
                                            <div className="grid grid-cols-1 gap-sm sm:grid-cols-2">
                                                <div>
                                                    <label htmlFor={`eq_brand_${index}`} className={labelClass}>Marque</label>
                                                    <input
                                                        id={`eq_brand_${index}`} type="text" value={eq.brand}
                                                        onChange={(e) => {
                                                            const updated = [...data.equipments];
                                                            updated[index] = { ...updated[index], brand: e.target.value };
                                                            setData('equipments', updated);
                                                        }}
                                                        placeholder="BIOBASE, Mindray, HUGER…"
                                                        className={fieldClass}
                                                    />
                                                </div>
                                                <div>
                                                    <label htmlFor={`eq_model_${index}`} className={labelClass}>Modèle / Désignation</label>
                                                    <input
                                                        id={`eq_model_${index}`} type="text" value={eq.model}
                                                        onChange={(e) => {
                                                            const updated = [...data.equipments];
                                                            updated[index] = { ...updated[index], model: e.target.value };
                                                            setData('equipments', updated);
                                                        }}
                                                        placeholder="Ex. : Moniteur patient MEC-1200"
                                                        className={fieldClass}
                                                    />
                                                </div>
                                                <div className="sm:col-span-2">
                                                    <label htmlFor={`eq_serial_${index}`} className={labelClass}>Numéro de série</label>
                                                    <input
                                                        id={`eq_serial_${index}`} type="text" value={eq.serial}
                                                        onChange={(e) => {
                                                            const updated = [...data.equipments];
                                                            updated[index] = { ...updated[index], serial: e.target.value };
                                                            setData('equipments', updated);
                                                        }}
                                                        placeholder="Ex. : SN-20240312-0047"
                                                        className={fieldClass}
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                {data.equipments.length < 5 && (
                                    <button
                                        type="button"
                                        onClick={() => setData('equipments', [...data.equipments, { brand: '', model: '', serial: '' }])}
                                        className="mt-sm flex items-center gap-2 text-body-sm font-semibold text-tertiary transition-colors hover:text-tertiary/70"
                                    >
                                        <span className="material-symbols-outlined text-[18px]" aria-hidden="true">add_circle</span>
                                        Ajouter un autre équipement
                                    </button>
                                )}

                                <p className="mt-sm text-body-sm text-secondary/50">
                                    Le numéro de série est visible sur l'étiquette à l'arrière ou sous l'appareil. Il facilite l'identification rapide pour le technicien.
                                </p>
                            </div>
                        </section>

                        {/* Section 3 : Panne */}
                        <section className="rounded-xl border border-outline-variant bg-white">
                            <header className="flex items-center gap-2 border-b border-outline-variant px-md py-sm">
                                <span className="flex h-8 w-8 items-center justify-center rounded-full bg-navy-blue text-label-caps font-bold text-white">3</span>
                                <h2 className="text-headline-md text-secondary">Description de la demande</h2>
                            </header>
                            <div className="p-md">
                                <label htmlFor="problem" className={labelClass}>Décrivez la panne ou votre besoin *</label>
                                <textarea
                                    id="problem" rows={7} value={data.problem} required
                                    onChange={(e) => setData('problem', e.target.value)}
                                    placeholder="Décrivez précisément les symptômes, messages d'erreur affichés, depuis combien de temps le problème est apparu, dans quel contexte d'utilisation, et toute autre observation utile au diagnostic…"
                                    className={fieldClass}
                                />
                                {errors.problem && <p className="mt-1 text-body-sm text-primary">{errors.problem}</p>}
                                <p className="mt-2 text-body-sm text-secondary/50">
                                    Plus votre description est précise, plus le pré-diagnostic à distance sera efficace.
                                </p>
                            </div>
                        </section>
                    </div>

                    {/* ── Colonne latérale ──────────────────── */}
                    <div className="lg:col-span-4">
                        <div className="sticky top-[150px] flex flex-col gap-md">

                            {/* Type d'intervention */}
                            <section
                                ref={interventionSectionRef}
                                className={`rounded-xl border bg-white p-md transition-colors ${errors.intervention_type ? 'border-primary ring-1 ring-primary/30' : 'border-outline-variant'}`}
                            >
                                <h3 className="mb-sm text-body-md font-bold text-secondary">Type d'intervention *</h3>
                                <div className="flex flex-col gap-1">
                                    {interventionTypes.map((type) => (
                                        <label
                                            key={type}
                                            className={`flex cursor-pointer items-center gap-2 rounded-lg border p-2.5 text-body-sm transition-colors ${data.intervention_type === type ? 'border-tertiary bg-tertiary/5 font-semibold text-secondary' : 'border-outline-variant text-secondary/70 hover:bg-surface-2'}`}
                                        >
                                            <input
                                                type="radio"
                                                name="intervention_type"
                                                value={type}
                                                checked={data.intervention_type === type}
                                                onChange={() => setData('intervention_type', type)}
                                                className="h-4 w-4 border-outline-variant text-tertiary focus:ring-tertiary"
                                            />
                                            {type}
                                        </label>
                                    ))}
                                </div>
                                {errors.intervention_type && (
                                    <div role="alert" className="mt-sm flex items-center gap-2 rounded-lg bg-primary/10 px-3 py-2.5 text-primary">
                                        <span className="material-symbols-outlined shrink-0 text-[20px]" aria-hidden="true">error</span>
                                        <p className="text-body-sm font-semibold">{errors.intervention_type}</p>
                                    </div>
                                )}
                            </section>

                            {/* Niveau de priorité */}
                            <section className="rounded-xl border border-outline-variant bg-white p-md">
                                <h3 className="mb-sm text-body-md font-bold text-secondary">Niveau de priorité *</h3>
                                <div className="flex flex-col gap-2">
                                    {PRIORITIES.map((p) => (
                                        <label
                                            key={p.value}
                                            className={`flex cursor-pointer items-start gap-2 rounded-lg border p-3 transition-colors ${data.priority === p.value ? p.selected : p.cls + ' hover:bg-surface-2'}`}
                                        >
                                            <input
                                                type="radio"
                                                name="priority"
                                                value={p.value}
                                                checked={data.priority === p.value}
                                                onChange={() => setData('priority', p.value)}
                                                className="mt-0.5 h-4 w-4 shrink-0 border-outline-variant text-tertiary focus:ring-tertiary"
                                            />
                                            <div>
                                                <div className={`flex items-center gap-1 text-body-sm font-bold ${data.priority === p.value ? p.iconCls : 'text-secondary'}`}>
                                                    <span className="material-symbols-outlined text-[16px]" aria-hidden="true">{p.icon}</span>
                                                    {p.label}
                                                </div>
                                                <p className="mt-0.5 text-[11px] text-secondary/60">{p.desc}</p>
                                            </div>
                                        </label>
                                    ))}
                                </div>
                                {errors.priority && <p className="mt-1 text-body-sm text-primary">{errors.priority}</p>}
                            </section>

                            {/* Bouton submit */}
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-container py-4 text-body-md font-bold text-white shadow-sm transition-colors hover:bg-primary disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {processing ? (
                                    <>
                                        <span className="h-5 w-5 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true" />
                                        Envoi en cours…
                                    </>
                                ) : (
                                    <>
                                        <span className="material-symbols-outlined text-[20px]" aria-hidden="true">confirmation_number</span>
                                        Ouvrir le ticket SAV
                                    </>
                                )}
                            </button>

                            <div className="rounded-xl border border-outline-variant bg-surface-2 p-md text-body-sm text-secondary/70">
                                <p className="flex items-start gap-2">
                                    <span className="material-symbols-outlined mt-0.5 text-[16px] text-tertiary" aria-hidden="true">verified_user</span>
                                    Vos informations sont transmises uniquement à notre équipe technique certifiée.
                                </p>
                                <p className="mt-2 flex items-start gap-2">
                                    <span className="material-symbols-outlined mt-0.5 text-[16px] text-tertiary" aria-hidden="true">schedule</span>
                                    Délai de réponse garanti : 2 h en semaine, 4 h le week-end.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </PublicLayout>
    );
}
