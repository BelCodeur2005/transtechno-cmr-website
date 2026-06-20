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
        icon: 'gavel',
        title: '1. Objet et champ d’application',
        body: (
            <p>
                Les présentes Conditions Générales de Vente (CGV) régissent les ventes d'équipements médicaux,
                scientifiques et de réactifs réalisées par <strong className="text-secondary">Transtechno Cameroun</strong>{' '}
                auprès de ses clients professionnels (hôpitaux, cliniques, laboratoires, industries). Toute commande
                implique l'acceptation sans réserve des présentes CGV.
            </p>
        ),
    },
    {
        icon: 'request_quote',
        title: '2. Devis et commandes',
        body: (
            <p>
                Toute prestation fait l'objet d'un <strong className="text-secondary">devis (cotation) préalable</strong>,
                établi à partir de votre demande. Le devis est valable pour la durée qui y est indiquée. La commande est
                ferme à réception du bon de commande signé ou de l'accord écrit du client.
            </p>
        ),
    },
    {
        icon: 'payments',
        title: '3. Prix et taxes',
        body: (
            <>
                <p>
                    Les prix sont exprimés en <strong className="text-secondary">Francs CFA (FCFA)</strong>, hors taxes. La{' '}
                    <strong className="text-secondary">TVA au taux de 19,25 %</strong> s'applique sauf exonération légale.
                </p>
                <p className="mt-sm">
                    Les établissements éligibles peuvent bénéficier de l'<strong className="text-secondary">exonération
                    de TVA Minsanté</strong>, sur présentation des justificatifs requis. Les frais de transport, de
                    dédouanement et d'installation, lorsqu'ils s'appliquent, sont précisés dans le devis.
                </p>
            </>
        ),
    },
    {
        icon: 'account_balance',
        title: '4. Modalités de paiement',
        body: (
            <p>
                Les modalités et échéances de paiement sont définies dans le devis ou le contrat. Sauf accord particulier,
                un acompte peut être exigé à la commande, notamment pour les équipements faisant l'objet d'une importation
                spécifique.
            </p>
        ),
    },
    {
        icon: 'local_shipping',
        title: '5. Livraison et délais',
        body: (
            <p>
                Une partie de nos équipements étant <strong className="text-secondary">importée sur commande</strong>, les
                délais de livraison sont communiqués à titre indicatif dans le devis et peuvent varier selon la
                disponibilité fournisseur et les formalités douanières. Transtechno Cameroun informe le client de toute
                évolution significative du délai.
            </p>
        ),
    },
    {
        icon: 'build',
        title: '6. Installation, garantie et SAV',
        body: (
            <p>
                L'installation, la mise en service et la formation sont assurées selon les conditions précisées au devis.
                Les équipements bénéficient de la <strong className="text-secondary">garantie constructeur</strong>, et nos
                équipes assurent la maintenance et le service après-vente avec des pièces d'origine certifiées (HUGER,
                BIOBASE, GIMA, etc.).
            </p>
        ),
    },
    {
        icon: 'inventory_2',
        title: '7. Réserve de propriété',
        body: (
            <p>
                Les marchandises demeurent la propriété de Transtechno Cameroun jusqu'au paiement intégral du prix,
                conformément à la législation en vigueur.
            </p>
        ),
    },
    {
        icon: 'verified_user',
        title: '8. Conformité réglementaire',
        body: (
            <p>
                Transtechno Cameroun est agréée par le Ministère de la Santé Publique (Minsanté) sous l'arrêté{' '}
                <strong className="text-secondary">N° 0683</strong>. Les produits commercialisés respectent les normes de
                l'Organisation Mondiale de la Santé (OMS) et la réglementation applicable aux dispositifs médicaux et aux
                réactifs (IVD / Recherche).
            </p>
        ),
    },
    {
        icon: 'balance',
        title: '9. Litiges et droit applicable',
        body: (
            <p>
                Les présentes CGV sont régies par le <strong className="text-secondary">droit camerounais</strong>. En cas
                de litige, les parties s'efforceront de trouver une solution amiable avant toute action ; à défaut, les
                tribunaux compétents de Douala seront seuls saisis.
            </p>
        ),
    },
];

export default function ConditionsVente() {
    return (
        <PublicLayout>
            <Head title="Conditions Générales de Vente" />

            <main className="mx-auto max-w-4xl px-margin pb-xl">
                <section className="mb-lg border-b border-outline-variant py-lg">
                    <h1 className="mb-xs text-headline-xl text-navy-blue">Conditions Générales de Vente</h1>
                    <p className="max-w-2xl text-body-lg text-secondary/70">
                        Cadre contractuel des ventes d'équipements médicaux, scientifiques et de réactifs de Transtechno
                        Cameroun à ses clients professionnels.
                    </p>
                </section>

                <div className="space-y-xl">
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
                            Une question sur nos conditions de vente ou un projet d'équipement ?{' '}
                            <Link href="/contact" className="font-semibold text-primary hover:underline">
                                Contactez notre équipe commerciale
                            </Link>
                            .
                        </p>
                    </div>
                </div>
            </main>
        </PublicLayout>
    );
}
