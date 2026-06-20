import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import { ReactNode } from 'react';

const PILLARS = [
    {
        icon: 'settings',
        title: 'Installation & Formation',
        text: 'Mise en service de vos automates et formation de votre personnel médical sur site.',
    },
    {
        icon: 'health_and_safety',
        title: 'Maintenance Préventive',
        text: 'Visites planifiées pour garantir la longévité et la conformité de vos équipements.',
    },
    {
        icon: 'build',
        title: 'Réparation & SAV',
        text: 'Diagnostic rapide et intervention sur site en cas de panne critique.',
    },
    {
        icon: 'inventory_2',
        title: 'Pièces de Rechange',
        text: "Stock permanent de pièces d'origine certifiées par les constructeurs (HUGER, BIOBASE…).",
    },
];

const STEPS = [
    { icon: 'support_agent', title: 'Ouverture du Ticket' },
    { icon: 'monitor', title: 'Pré-diagnostic à distance' },
    { icon: 'local_shipping', title: "Déploiement de l'équipe technique" },
    { icon: 'task_alt', title: 'Remise en service et Rapport' },
];

function SectionHeading({ children }: { children: ReactNode }) {
    return (
        <div className="mb-lg text-center">
            <h2 className="text-headline-lg text-navy-blue">{children}</h2>
            <span className="mx-auto mt-2 block h-1 w-16 rounded-full bg-primary-container" aria-hidden="true" />
        </div>
    );
}

export default function ServicesSav() {
    return (
        <PublicLayout>
            <Head title="Services & SAV — Transtechno Cameroun">
                <meta name="description" content="Installation, maintenance préventive, réparation et pièces de rechange pour équipements médicaux au Cameroun. Hotline SAV : +237 674 64 53 54. Agréé Minsanté." head-key="description" />
                <meta property="og:title" content="Services & SAV — Transtechno Cameroun" head-key="og:title" />
                <meta property="og:description" content="Installation, maintenance préventive, réparation et pièces de rechange pour équipements médicaux au Cameroun." head-key="og:description" />
                <meta property="og:type" content="website" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                <meta name="twitter:card" content="summary" head-key="twitter:card" />
            </Head>

            {/* ── Hero ─────────────────────────────────────────── */}
            <section className="relative flex min-h-[460px] items-center overflow-hidden py-xl">
                <div className="absolute inset-0 z-0">
                    <img src="/images/services-hero.jpg" alt="" className="h-full w-full object-cover" />
                    <div className="absolute inset-0 bg-gradient-to-r from-navy-blue via-navy-blue/90 to-navy-blue/60" />
                </div>
                <div className="relative z-10 mx-auto w-full max-w-7xl px-margin">
                    <span className="mb-sm inline-flex items-center gap-2 rounded-full bg-primary-container/20 px-3 py-1 text-label-caps font-semibold uppercase text-white ring-1 ring-white/20">
                        <span className="h-2 w-2 animate-pulse rounded-full bg-green-400" aria-hidden="true" />
                        Support technique 24/7
                    </span>
                    <h1 className="max-w-3xl text-headline-xl text-white">Expertise Technique &amp; Service Après-Vente</h1>
                    <p className="mt-sm max-w-2xl text-body-lg text-white/80">
                        Parce qu'une panne peut paralyser un bloc opératoire ou un laboratoire, notre équipe d'ingénieurs
                        certifiés intervient sur toute l'étendue du territoire camerounais.
                    </p>
                    <div className="mt-md flex flex-wrap gap-sm">
                        <Link
                            href="/sav/demande"
                            className="inline-flex items-center gap-2 rounded-lg bg-primary-container px-5 py-3 text-body-md font-bold text-white transition-colors hover:bg-primary"
                        >
                            <span className="material-symbols-outlined text-[20px]" aria-hidden="true">confirmation_number</span>
                            Ouvrir un ticket SAV
                        </Link>
                        <a href="tel:+237674645354" className="inline-flex items-center gap-2 rounded-lg border border-white/40 px-5 py-3 text-body-md font-bold text-white transition-colors hover:bg-white/10">
                            <span className="material-symbols-outlined text-[20px]" aria-hidden="true">call</span>
                            Contacter un technicien
                        </a>
                    </div>
                </div>
            </section>

            {/* ── 4 piliers ────────────────────────────────────── */}
            <section className="mx-auto max-w-7xl px-margin py-xl">
                <SectionHeading>Un accompagnement sur mesure</SectionHeading>
                <div className="grid grid-cols-1 gap-md sm:grid-cols-2 lg:grid-cols-4">
                    {PILLARS.map((p) => (
                        <div key={p.title} className="rounded-xl border border-outline-variant bg-white p-md transition-shadow hover:shadow-lg">
                            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-tertiary/10 text-tertiary">
                                <span className="material-symbols-outlined" aria-hidden="true">{p.icon}</span>
                            </div>
                            <h3 className="mt-sm text-headline-md text-secondary">{p.title}</h3>
                            <p className="mt-xs text-body-sm text-secondary/70">{p.text}</p>
                        </div>
                    ))}
                </div>
            </section>

            {/* ── Pôle Hémodialyse (bandeau navy) ──────────────── */}
            <section className="mx-auto max-w-7xl px-margin pb-xl">
                <div className="flex flex-col items-start gap-md rounded-2xl bg-navy-blue p-lg text-white lg:flex-row lg:items-center lg:justify-between lg:p-xl">
                    <div className="max-w-3xl">
                        <p className="flex items-center gap-2 text-label-caps font-semibold uppercase text-tertiary-fixed-dim">
                            <span className="material-symbols-outlined text-[20px]" aria-hidden="true">water_drop</span>
                            Expertise spécialisée
                        </p>
                        <h2 className="mt-1 text-headline-lg">Pôle d'Excellence : Hémodialyse &amp; Traitement des Eaux</h2>
                        <p className="mt-sm text-body-md text-white/80">
                            Transtechno s'investit activement dans la prise en charge de l'insuffisance rénale au Cameroun :
                            installation complète de centres de dialyse, maintenance des générateurs, et production locale de
                            dialysats certifiés.
                        </p>
                    </div>
                    <Link href="/pole-hemodialyse" className="inline-flex shrink-0 items-center gap-2 rounded-lg bg-white px-5 py-3 text-body-md font-bold text-navy-blue transition-colors hover:bg-white/90">
                        Découvrir ce pôle
                        <span className="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_forward</span>
                    </Link>
                </div>
            </section>

            {/* ── Processus (timeline) ─────────────────────────── */}
            <section className="bg-surface py-xl">
                <div className="mx-auto max-w-7xl px-margin">
                    <SectionHeading>Comment déclencher une intervention ?</SectionHeading>
                    <ol className="relative grid grid-cols-1 gap-lg sm:grid-cols-2 lg:grid-cols-4">
                        <span className="absolute left-0 right-0 top-8 hidden h-0.5 bg-outline-variant lg:block" aria-hidden="true" />
                        {STEPS.map((s, i) => (
                            <li key={s.title} className="relative flex flex-col items-center text-center">
                                <span className="relative z-10 flex h-16 w-16 items-center justify-center rounded-full border-2 border-primary-container bg-white text-primary-container shadow-sm">
                                    <span className="material-symbols-outlined text-[28px]" aria-hidden="true">{s.icon}</span>
                                </span>
                                <span className="mt-sm text-label-caps font-bold uppercase text-tertiary">Étape {i + 1}</span>
                                <h3 className="mt-1 max-w-[14rem] text-body-md font-bold text-secondary">{s.title}</h3>
                            </li>
                        ))}
                    </ol>
                </div>
            </section>

            {/* ── CTA urgence (2 boutons) ──────────────────────── */}
            <section className="bg-navy-blue py-xl">
                <div className="mx-auto max-w-3xl px-margin text-center">
                    <h2 className="text-headline-lg text-white">Votre équipement nécessite une attention urgente ?</h2>
                    <p className="mt-sm text-body-md text-white/70">
                        Nos techniciens sont formés directement chez nos partenaires internationaux.
                    </p>
                    <div className="mt-md flex flex-wrap justify-center gap-sm">
                        <Link
                            href="/sav/demande"
                            className="inline-flex items-center gap-2 rounded-lg bg-primary-container px-6 py-3 text-body-md font-bold text-white transition-colors hover:bg-primary"
                        >
                            <span className="material-symbols-outlined text-[20px]" aria-hidden="true">support_agent</span>
                            Formulaire d'assistance technique
                        </Link>
                        <a href="tel:+237674645354" className="inline-flex items-center gap-2 rounded-lg border border-white/40 px-6 py-3 text-body-md font-bold text-white transition-colors hover:bg-white/10">
                            <span className="material-symbols-outlined text-[20px]" aria-hidden="true">call</span>
                            Hotline SAV : +237 674 64 53 54
                        </a>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
