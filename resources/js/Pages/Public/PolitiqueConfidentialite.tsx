import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import { ReactNode } from 'react';

interface Section {
    icon: string;
    title: string;
    body: ReactNode;
}

const SECTIONS: Section[] = [
    {
        icon: 'database',
        title: '1. Données que nous collectons',
        body: (
            <>
                <p>
                    Transtechno Cameroun collecte uniquement les données que vous nous transmettez volontairement via nos
                    formulaires de <strong className="text-secondary">contact</strong> et de{' '}
                    <strong className="text-secondary">demande de cotation</strong> : nom, établissement, ville, téléphone,
                    adresse email et le contenu de votre message ou de votre besoin.
                </p>
                <p className="mt-sm">
                    Aucune donnée bancaire n'est collectée sur ce site : les transactions financières sont traitées hors
                    ligne, dans le cadre de devis signés.
                </p>
            </>
        ),
    },
    {
        icon: 'target',
        title: '2. Finalité du traitement',
        body: (
            <ul className="list-inside list-disc space-y-xs text-secondary/70">
                <li>Répondre à vos demandes d'information et établir des propositions commerciales (devis).</li>
                <li>Assurer le suivi de la relation commerciale, de la livraison et du SAV.</li>
                <li>Vous transmettre, sur demande, des informations techniques sur nos équipements et réactifs.</li>
            </ul>
        ),
    },
    {
        icon: 'cookie',
        title: '3. Cookies',
        body: (
            <p>
                Ce site utilise uniquement des cookies <strong className="text-secondary">techniques</strong> nécessaires à
                son fonctionnement (session, sécurité, préférences). Nous n'utilisons{' '}
                <strong className="text-secondary">aucun cookie publicitaire ni de traçage tiers</strong> à des fins
                marketing. Vous pouvez configurer votre navigateur pour refuser les cookies, au risque d'altérer certaines
                fonctionnalités.
            </p>
        ),
    },
    {
        icon: 'shield_lock',
        title: '4. Partage et sécurité',
        body: (
            <p>
                Vos données ne sont <strong className="text-secondary">ni vendues, ni cédées, ni échangées</strong> avec des
                tiers à des fins commerciales. Elles sont conservées sur une infrastructure d'hébergement sécurisée et ne
                sont accessibles qu'aux collaborateurs habilités de Transtechno Cameroun.
            </p>
        ),
    },
    {
        icon: 'schedule',
        title: '5. Durée de conservation',
        body: (
            <p>
                Les données liées à une demande sont conservées le temps nécessaire au traitement de votre dossier puis
                pour la durée légale applicable à la relation commerciale. Au-delà, elles sont supprimées ou archivées de
                manière sécurisée.
            </p>
        ),
    },
    {
        icon: 'manage_accounts',
        title: '6. Vos droits',
        body: (
            <p>
                Conformément à la réglementation en vigueur, vous disposez d'un droit d'accès, de rectification et de
                suppression de vos données personnelles. Pour exercer ces droits, contactez-nous à l'adresse{' '}
                <a href="mailto:office@transtechno-cmr.com" className="font-semibold text-tertiary hover:underline">
                    office@transtechno-cmr.com
                </a>
                .
            </p>
        ),
    },
];

export default function PolitiqueConfidentialite() {
    return (
        <PublicLayout>
            <Head title="Politique de Confidentialité" />

            <main className="mx-auto max-w-4xl px-sm sm:px-margin pb-lg lg:pb-xl">
                <section className="mb-lg border-b border-outline-variant py-lg">
                    <h1 className="mb-xs text-2xl font-bold text-navy-blue sm:text-4xl lg:text-headline-xl">Politique de Confidentialité</h1>
                    <p className="max-w-2xl text-body-md text-secondary/70 lg:text-body-lg">
                        La protection de vos données et le respect de votre vie privée sont au cœur de nos engagements
                        envers nos partenaires de santé.
                    </p>
                </section>

                <div className="space-y-lg lg:space-y-xl">
                    {SECTIONS.map((s) => (
                        <article key={s.title} className="space-y-sm">
                            <h2 className="flex items-center gap-xs text-headline-md text-secondary">
                                <span className="material-symbols-outlined text-tertiary" aria-hidden="true">
                                    {s.icon}
                                </span>
                                {s.title}
                            </h2>
                            <div className="text-body-md leading-relaxed text-secondary/70">{s.body}</div>
                        </article>
                    ))}

                    <div className="flex items-start gap-sm rounded-lg border-l-4 border-secondary bg-surface-2 p-md">
                        <span className="material-symbols-outlined text-secondary" aria-hidden="true">
                            info
                        </span>
                        <p className="text-body-sm text-secondary/70">
                            Une question sur le traitement de vos données ?{' '}
                            <Link href="/contact" className="font-semibold text-primary hover:underline">
                                Contactez-nous
                            </Link>{' '}
                            — nous vous répondrons dans les meilleurs délais.
                        </p>
                    </div>
                </div>
            </main>
        </PublicLayout>
    );
}
