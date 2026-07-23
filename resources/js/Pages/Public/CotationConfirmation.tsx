import WhatsAppButton from '@/Components/Public/WhatsAppButton';
import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

interface Props {
    reference: string;
    isUrgent: boolean;
}

export default function CotationConfirmation({ reference, isUrgent }: Props) {
    return (
        <PublicLayout>
            <Head title="Demande envoyée" />

            <main className="mx-auto flex w-full max-w-2xl flex-col items-center px-sm sm:px-margin py-lg lg:py-xl text-center">
                <div className="flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
                    <span className="material-symbols-outlined text-[44px] text-green-600" aria-hidden="true">
                        task_alt
                    </span>
                </div>

                <h1 className="mt-md text-2xl font-bold text-navy-blue sm:text-4xl lg:text-headline-xl">Demande bien reçue !</h1>
                <p className="mt-sm max-w-lg text-body-md text-secondary/70 lg:text-body-lg">
                    Merci. Votre demande de cotation a été transmise à notre équipe commerciale. Vous recevrez une offre
                    technico-commerciale détaillée sous 24h à 48h.
                </p>

                <div className="mt-md rounded-xl border border-outline-variant bg-white px-md sm:px-lg py-md">
                    <p className="text-label-caps uppercase text-secondary/60">Votre référence de suivi</p>
                    <p className="mt-1 text-headline-md lg:text-headline-lg font-bold tracking-wide text-primary">#{reference}</p>
                </div>

                {isUrgent && (
                    <div className="mt-md flex items-center gap-2 rounded-lg border border-primary/20 bg-primary/5 px-md py-3 text-body-sm font-semibold text-primary">
                        <span className="material-symbols-outlined text-[20px]" aria-hidden="true">bolt</span>
                        Demande marquée comme prioritaire — un conseiller vous contactera en priorité.
                    </div>
                )}

                <div className="mt-md lg:mt-lg flex w-full flex-col items-center gap-sm">
                    <WhatsAppButton
                        message={`Bonjour Transtechno, je souhaite suivre ma demande de cotation #${reference}.`}
                        label={`Suivre ma demande sur WhatsApp`}
                    />
                    <div className="flex flex-wrap justify-center gap-sm">
                        <Link
                            href="/catalogue"
                            className="rounded-lg bg-secondary px-5 py-2.5 text-body-md font-bold text-white transition-colors hover:bg-secondary/90"
                        >
                            Retour au catalogue
                        </Link>
                        <Link
                            href="/"
                            className="rounded-lg border border-outline-variant px-5 py-2.5 text-body-md font-bold text-secondary transition-colors hover:bg-surface-2"
                        >
                            Accueil
                        </Link>
                    </div>
                </div>

                <p className="mt-md lg:mt-lg flex items-center gap-2 text-body-sm text-secondary/60">
                    <span className="material-symbols-outlined text-[18px] text-tertiary" aria-hidden="true">support_agent</span>
                    Une question ? Appelez le <a href="tel:+237674645354" className="font-semibold text-primary hover:underline">+237 674 64 53 54</a>
                </p>
            </main>
        </PublicLayout>
    );
}
