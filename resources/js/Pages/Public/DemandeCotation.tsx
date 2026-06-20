import PublicLayout from '@/Layouts/PublicLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useEffect, useRef } from 'react';

interface Props {
    sectors: string[];
}

const SERVICES = [
    { field: 'services_installation', label: 'Installation sur site' },
    { field: 'services_formation', label: 'Formation du personnel utilisateur' },
    { field: 'services_maintenance', label: 'Contrat de maintenance préventive' },
] as const;

export default function DemandeCotation({ sectors }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        responsible_name: '',
        structure_name: '',
        city: '',
        sector: '',
        phone: '',
        email: '',
        specifications: '',
        is_urgent: false,
        services_installation: false,
        services_formation: false,
        services_maintenance: false,
    });

    const errorBannerRef = useRef<HTMLDivElement>(null);

    // Scroll vers la première erreur dès qu'elles apparaissent après un submit
    useEffect(() => {
        const hasErrors = Object.keys(errors).length > 0;
        if (!hasErrors) return;

        // Priorité 1 : scroll vers la bannière d'erreur en haut du formulaire
        if (errorBannerRef.current) {
            errorBannerRef.current.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Priorité 2 : scroll vers le premier champ en erreur
        const firstKey = Object.keys(errors)[0];
        const el = document.getElementById(firstKey) ?? document.querySelector<HTMLElement>(`[name="${firstKey}"]`);
        el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el?.focus();
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [JSON.stringify(errors)]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/demande-cotation', { preserveScroll: true });
    };

    const fieldClass =
        'w-full rounded-lg border border-outline-variant bg-surface-2 px-3 py-2.5 text-body-md text-secondary placeholder-secondary/40 transition-colors focus:border-primary focus:ring-1 focus:ring-primary';
    const labelClass = 'mb-1 block text-label-caps font-semibold uppercase text-secondary/70';

    return (
        <PublicLayout>
            <Head title="Demande de cotation personnalisée" />

            {/* ── Hero ──────────────────────────────────────────── */}
            <section className="border-b border-outline-variant bg-navy-blue py-xl text-white">
                <div className="mx-auto max-w-7xl px-margin">
                    <h1 className="max-w-3xl text-headline-xl">Demande de cotation personnalisée</h1>
                    <p className="mt-sm max-w-2xl text-body-lg text-white/80">
                        Accélérez la mise à jour de votre plateau technique. Remplissez ce formulaire pour recevoir une
                        offre technico-commerciale détaillée sous 24h à 48h.
                    </p>
                    <div className="mt-md flex flex-wrap gap-md">
                        <span className="flex items-center gap-1 text-body-sm font-medium text-white/90">
                            <span className="material-symbols-outlined text-[20px] text-tertiary-fixed-dim" aria-hidden="true">verified</span>
                            Agréé Minsanté : Arrêté N° 0683
                        </span>
                        <span className="flex items-center gap-1 text-body-sm font-medium text-white/90">
                            <span className="material-symbols-outlined text-[20px] text-tertiary-fixed-dim" aria-hidden="true">health_and_safety</span>
                            Standard de qualité OMS
                        </span>
                    </div>
                </div>
            </section>

            <main className="mx-auto grid w-full max-w-7xl grid-cols-1 gap-lg px-margin py-lg lg:grid-cols-12">
                {/* ── Formulaire ──────────────────────────────── */}
                <form onSubmit={submit} className="flex flex-col gap-md lg:col-span-8">

                    {/* ── Bannière d'erreur (visible uniquement après un submit raté) ── */}
                    {Object.keys(errors).length > 0 && (
                        <div
                            ref={errorBannerRef}
                            role="alert"
                            className="flex items-start gap-3 rounded-xl border border-primary/30 bg-primary/5 px-md py-sm"
                        >
                            <span className="material-symbols-outlined mt-0.5 shrink-0 text-[22px] text-primary" aria-hidden="true">error</span>
                            <div>
                                <p className="text-body-md font-bold text-primary">Certains champs nécessitent votre attention</p>
                                <ul className="mt-1 list-inside list-disc text-body-sm text-primary/80">
                                    {Object.values(errors).map((msg, i) => (
                                        <li key={i}>{msg}</li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    )}

                    {/* Identité */}
                    <section className="rounded-xl border border-outline-variant bg-white p-md">
                        <h2 className="mb-sm flex items-center gap-1 text-headline-md text-secondary">
                            <span className="material-symbols-outlined text-tertiary" aria-hidden="true">corporate_fare</span>
                            Identité de l'établissement
                        </h2>
                        <div className="grid grid-cols-1 gap-sm sm:grid-cols-2">
                            <div>
                                <label htmlFor="responsible_name" className={labelClass}>Nom du responsable *</label>
                                <input id="responsible_name" type="text" value={data.responsible_name} onChange={(e) => setData('responsible_name', e.target.value)} placeholder="Ex : Dr. TALLA Jean" className={fieldClass} required />
                                {errors.responsible_name && <p className="mt-1 text-body-sm text-primary">{errors.responsible_name}</p>}
                            </div>
                            <div>
                                <label htmlFor="structure_name" className={labelClass}>Nom de la clinique / hôpital *</label>
                                <input id="structure_name" type="text" value={data.structure_name} onChange={(e) => setData('structure_name', e.target.value)} placeholder="Ex : Hôpital Général" className={fieldClass} required />
                                {errors.structure_name && <p className="mt-1 text-body-sm text-primary">{errors.structure_name}</p>}
                            </div>
                            <div>
                                <label htmlFor="city" className={labelClass}>Ville *</label>
                                <input id="city" type="text" value={data.city} onChange={(e) => setData('city', e.target.value)} placeholder="Ex : Douala" className={fieldClass} required />
                                {errors.city && <p className="mt-1 text-body-sm text-primary">{errors.city}</p>}
                            </div>
                            <div>
                                <label htmlFor="sector" className={labelClass}>Secteur d'activité *</label>
                                <select id="sector" value={data.sector} onChange={(e) => setData('sector', e.target.value)} className={fieldClass} required>
                                    <option value="" disabled>Sélectionnez un secteur</option>
                                    {sectors.map((s) => (
                                        <option key={s} value={s}>{s}</option>
                                    ))}
                                </select>
                                {errors.sector && <p className="mt-1 text-body-sm text-primary">{errors.sector}</p>}
                            </div>
                            <div>
                                <label htmlFor="phone" className={labelClass}>Téléphone *</label>
                                <input
                                    id="phone" type="tel" value={data.phone}
                                    onChange={(e) => setData('phone', e.target.value)}
                                    placeholder="+237 6XX XX XX XX"
                                    className={`${fieldClass} ${errors.phone ? 'border-primary ring-1 ring-primary/30' : ''}`}
                                />
                                {errors.phone && <p className="mt-1 text-body-sm text-primary">{errors.phone}</p>}
                            </div>
                            <div>
                                <label htmlFor="email" className={labelClass}>Email *</label>
                                <input
                                    id="email" type="email" value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    placeholder="contact@etablissement.cm"
                                    className={`${fieldClass} ${errors.email ? 'border-primary ring-1 ring-primary/30' : ''}`}
                                />
                                {errors.email && <p className="mt-1 text-body-sm text-primary">{errors.email}</p>}
                            </div>
                            <p className="text-body-sm text-secondary/60 sm:col-span-2">
                                <span className="material-symbols-outlined mr-1 align-middle text-[14px] text-tertiary">info</span>
                                Au moins l'un des deux (téléphone ou email) est requis pour vous recontacter.
                            </p>
                        </div>
                    </section>

                    {/* Spécifications */}
                    <section className="rounded-xl border border-outline-variant bg-white p-md">
                        <h2 className="mb-sm flex items-center gap-1 text-headline-md text-secondary">
                            <span className="material-symbols-outlined text-tertiary" aria-hidden="true">inventory</span>
                            Spécifications du besoin
                        </h2>
                        <label htmlFor="specifications" className={labelClass}>Équipements ou consommables souhaités *</label>
                        <textarea
                            id="specifications"
                            rows={6}
                            value={data.specifications}
                            onChange={(e) => setData('specifications', e.target.value)}
                            placeholder="Veuillez décrire en détail les équipements, quantités et références souhaitées…"
                            className={fieldClass}
                            required
                        />
                        {errors.specifications && <p className="mt-1 text-body-sm text-primary">{errors.specifications}</p>}

                        <label className="mt-sm flex cursor-pointer items-center gap-2 rounded-lg border border-outline-variant p-2.5 transition-colors hover:bg-surface-2 has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                            <input type="checkbox" checked={data.is_urgent} onChange={(e) => setData('is_urgent', e.target.checked)} className="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary" />
                            <span className="material-symbols-outlined text-[20px] text-primary" aria-hidden="true">priority_high</span>
                            <span className="text-body-sm font-bold text-primary">Besoin urgent (sous 7 jours)</span>
                        </label>
                    </section>

                    {/* Services */}
                    <section className="rounded-xl border border-outline-variant bg-white p-md">
                        <h2 className="mb-sm flex items-center gap-1 text-headline-md text-secondary">
                            <span className="material-symbols-outlined text-tertiary" aria-hidden="true">build_circle</span>
                            Services associés
                        </h2>
                        <div className="space-y-2">
                            {SERVICES.map((s) => (
                                <label key={s.field} className="flex cursor-pointer items-center gap-2 rounded-lg border border-outline-variant p-2.5 transition-colors hover:bg-surface-2 has-[:checked]:border-tertiary has-[:checked]:bg-tertiary/5">
                                    <input type="checkbox" checked={data[s.field]} onChange={(e) => setData(s.field, e.target.checked)} className="h-4 w-4 rounded border-outline-variant text-tertiary focus:ring-tertiary" />
                                    <span className="text-body-sm font-medium text-secondary">{s.label}</span>
                                </label>
                            ))}
                        </div>
                    </section>

                    <button
                        type="submit"
                        disabled={processing}
                        className="flex w-full items-center justify-center gap-2 rounded-lg bg-primary-container py-3.5 text-body-md font-bold text-white transition-colors hover:bg-primary disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {processing ? (
                            <>
                                <span className="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true" />
                                Envoi en cours…
                            </>
                        ) : (
                            <>
                                <span className="material-symbols-outlined text-lg" aria-hidden="true">send</span>
                                Soumettre la demande
                            </>
                        )}
                    </button>

                    <p className="flex items-start gap-2 text-body-sm text-secondary/60">
                        <span className="material-symbols-outlined text-[18px] text-tertiary" aria-hidden="true">lock</span>
                        Les informations transmises sont protégées et destinées exclusivement à l'établissement de votre offre commerciale.
                    </p>
                </form>

                {/* ── Aside ───────────────────────────────────── */}
                <aside className="flex flex-col gap-md lg:col-span-4">
                    <div className="rounded-xl border border-tertiary/20 bg-tertiary/5 p-md">
                        <h3 className="mb-xs flex items-center gap-1 text-headline-md text-secondary">
                            <span className="material-symbols-outlined text-tertiary" aria-hidden="true">local_shipping</span>
                            Logistique &amp; importation
                        </h3>
                        <p className="text-body-sm text-secondary/70">
                            Vous ne trouvez pas une référence ? Nos partenariats avec HUGER, GIMA et BIOBASE nous permettent
                            de sourcer et d'importer vos équipements spécifiques via notre hub logistique de Douala.
                        </p>
                    </div>

                    <div className="rounded-xl border border-outline-variant bg-white p-md">
                        <h3 className="mb-xs flex items-center gap-1 text-headline-md text-secondary">
                            <span className="material-symbols-outlined text-tertiary" aria-hidden="true">support_agent</span>
                            Assistance commerciale
                        </h3>
                        <a href="tel:+237674645354" className="flex items-center gap-2 text-body-md font-bold text-secondary hover:text-primary">
                            <span className="material-symbols-outlined text-[20px] text-primary" aria-hidden="true">phone_in_talk</span>
                            +237 674 64 53 54
                        </a>
                        <p className="mt-1 text-body-sm text-secondary/60">+237 699 67 40 36</p>
                    </div>
                </aside>
            </main>
        </PublicLayout>
    );
}
