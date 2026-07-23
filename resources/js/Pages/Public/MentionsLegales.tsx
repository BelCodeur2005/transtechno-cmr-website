import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { PageProps } from '@/types';

export default function MentionsLegales() {
    const { company } = usePage<PageProps>().props;
    return (
        <PublicLayout>
            <Head title="Mentions Légales" />

            <main className="mx-auto max-w-5xl px-sm sm:px-margin pb-lg lg:pb-xl">
                {/* ── Hero ──────────────────────────────────────── */}
                <section className="mb-lg border-b border-outline-variant py-lg">
                    <h1 className="mb-xs text-2xl font-bold text-navy-blue sm:text-4xl lg:text-headline-xl">Mentions Légales</h1>
                    <p className="max-w-2xl text-body-md text-secondary/70 lg:text-body-lg">
                        Cadre réglementaire et informations institutionnelles relatives à l'exploitation de la plateforme
                        numérique de Transtechno Cameroun.
                    </p>
                </section>

                <div className="grid grid-cols-1 gap-md lg:gap-lg md:grid-cols-12">
                    {/* ── Colonne principale ───────────────────────── */}
                    <div className="space-y-lg lg:space-y-xl md:col-span-8">
                        {/* Informations Entreprise */}
                        <article className="space-y-sm">
                            <h2 className="flex items-center gap-xs text-headline-md lg:text-headline-lg text-secondary">
                                <span className="material-symbols-outlined text-tertiary" aria-hidden="true">
                                    corporate_fare
                                </span>
                                Informations Entreprise
                            </h2>
                            <div className="rounded-lg border border-outline-variant bg-white p-md lg:p-lg">
                                <p className="text-body-md leading-relaxed text-secondary/80">
                                    <strong className="text-secondary">TRANSTECHNO CAMEROUN</strong>
                                    <br />
                                    Société à Responsabilité Limitée (SARL)
                                    <br />
                                    <span className="mt-base block opacity-70">Siège Social :</span>
                                    Village carrefour entrée Bille, Immeuble Pharmacie Sainte Marie, 2ème niveau, Douala,
                                    Cameroun.
                                </p>
                                <div className="mt-md grid grid-cols-1 gap-sm sm:grid-cols-2">
                                    <div className="flex items-center gap-xs">
                                        <span className="material-symbols-outlined text-[20px] text-primary" aria-hidden="true">
                                            call
                                        </span>
                                        <span className="text-body-sm text-secondary/80">
                                            +237 674 64 53 54 / 699 67 40 36
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-xs">
                                        <span className="material-symbols-outlined text-[20px] text-primary" aria-hidden="true">
                                            mail
                                        </span>
                                        <a
                                            href={`mailto:${company.email}`}
                                            className="text-body-sm text-tertiary hover:underline"
                                        >
                                            {company.email}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>

                        {/* Agrément et Conformité */}
                        <article className="space-y-sm">
                            <h2 className="flex items-center gap-xs text-headline-md lg:text-headline-lg text-secondary">
                                <span className="material-symbols-outlined text-tertiary" aria-hidden="true">
                                    verified_user
                                </span>
                                Agrément et Conformité
                            </h2>
                            <div className="rounded-lg border border-primary/20 bg-primary/5 p-md lg:p-lg text-secondary/80">
                                <p className="text-body-md">
                                    Transtechno Cameroun est une institution{' '}
                                    <strong className="text-brand-red">
                                        agréée par le Ministère de la Santé Publique du Cameroun (Minsanté)
                                    </strong>{' '}
                                    sous l'arrêté <span className="font-bold underline">N° 0683</span>.
                                </p>
                                <p className="mt-sm text-body-sm opacity-90">
                                    Toutes nos opérations de distribution, d'installation et de maintenance d'équipements
                                    médicaux et de laboratoire respectent rigoureusement les normes internationales de
                                    l'Organisation Mondiale de la Santé (OMS) et les standards ISO 13485.
                                </p>
                            </div>
                        </article>

                        {/* Propriété Intellectuelle */}
                        <article className="space-y-sm">
                            <h2 className="flex items-center gap-xs text-headline-md lg:text-headline-lg text-secondary">
                                <span className="material-symbols-outlined text-tertiary" aria-hidden="true">
                                    copyright
                                </span>
                                Propriété Intellectuelle
                            </h2>
                            <p className="text-body-md text-secondary/80">
                                L'ensemble de ce site relève de la législation camerounaise et internationale sur le droit
                                d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés, y
                                compris pour les documents téléchargeables et les représentations iconographiques et
                                photographiques.
                            </p>
                            <p className="rounded border-l-4 border-secondary bg-surface-2 p-sm text-body-md text-secondary/80">
                                Les marques et logos cités sur ce site, notamment{' '}
                                <strong className="text-secondary">HUGER, GIMA, BIOBASE</strong>, sont la propriété de leurs
                                détenteurs respectifs et sont utilisés par Transtechno Cameroun dans le cadre de ses
                                partenariats de distribution officielle.
                            </p>
                        </article>

                        {/* Protection des Données */}
                        <article className="space-y-sm">
                            <h2 className="flex items-center gap-xs text-headline-md lg:text-headline-lg text-secondary">
                                <span className="material-symbols-outlined text-tertiary" aria-hidden="true">
                                    security
                                </span>
                                Protection des Données
                            </h2>
                            <p className="text-body-md text-secondary/80">
                                Les informations collectées via nos formulaires de contact ou de demande de devis sont
                                traitées avec la plus stricte confidentialité.
                            </p>
                            <ul className="list-inside list-disc space-y-xs text-body-sm text-secondary/70">
                                <li>Utilisation exclusive pour l'établissement de propositions commerciales (devis).</li>
                                <li>Aucune cession, vente ou échange de vos données personnelles à des tiers.</li>
                                <li>Droit d'accès et de rectification sur simple demande par email.</li>
                            </ul>
                        </article>

                        {/* Responsabilité Technique */}
                        <article className="space-y-sm">
                            <h2 className="flex items-center gap-xs text-headline-md lg:text-headline-lg text-secondary">
                                <span className="material-symbols-outlined text-tertiary" aria-hidden="true">
                                    gavel
                                </span>
                                Responsabilité Technique
                            </h2>
                            <p className="text-body-md text-secondary/80">
                                Transtechno Cameroun s'efforce d'assurer l'exactitude des informations diffusées. Toutefois,
                                les caractéristiques techniques des produits présentés dans notre catalogue en ligne sont
                                fournies à titre indicatif et peuvent varier selon les mises à jour des constructeurs.
                            </p>
                            <p className="border-t border-outline-variant pt-sm text-body-sm italic text-secondary/70">
                                Seules les fiches techniques officielles fournies lors du devis définitif font foi.
                            </p>
                        </article>
                    </div>

                    {/* ── Aside ────────────────────────────────────── */}
                    <aside className="space-y-md md:col-span-4">
                        <div className="rounded-lg border border-outline-variant bg-surface-4 p-md">
                            <h3 className="mb-sm text-headline-md text-secondary">Hébergement</h3>
                            <p className="text-body-sm text-secondary/70">
                                Ce site est hébergé par <strong className="text-secondary">{company.host_name}</strong>
                                <br />
                                {company.host_address}
                                <br />
                                <a href="https://www.planethoster.com" className="text-tertiary hover:underline">
                                    www.planethoster.com
                                </a>
                            </p>
                        </div>

                        <div className="group relative h-64 overflow-hidden rounded-lg">
                            <img
                                src="/images/legal-aside.jpg"
                                alt="Représentation d'une molécule d'ADN — rigueur scientifique"
                                loading="lazy"
                                className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div className="absolute inset-0 flex items-end bg-gradient-to-t from-secondary/80 to-transparent p-sm">
                                <span className="text-label-caps uppercase text-white">Rigueur &amp; Qualité</span>
                            </div>
                        </div>

                        <div className="rounded-lg border border-outline-variant p-md">
                            <h3 className="mb-xs text-headline-md text-secondary">Support Technique</h3>
                            <p className="mb-sm text-body-sm text-secondary/70">
                                Besoin d'une clarification sur nos conditions ?
                            </p>
                            <Link
                                href="/contact"
                                className="inline-flex items-center gap-xs font-bold text-primary hover:underline"
                            >
                                Contactez le service
                                <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                                    arrow_forward
                                </span>
                            </Link>
                        </div>
                    </aside>
                </div>
            </main>
        </PublicLayout>
    );
}
