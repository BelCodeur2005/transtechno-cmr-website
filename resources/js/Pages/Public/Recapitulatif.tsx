import { StockStatus, useQuoteCart } from '@/hooks/useQuoteCart';
import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';

interface Props {
    sectors: string[];
}

interface CustomItem {
    key: string;
    custom_label: string;
    brand_name: string;
    quantity: number;
}

const STOCK: Record<StockStatus, { text: string; cls: string; dot: string }> = {
    en_stock: { text: 'En stock à Douala', cls: 'text-green-600', dot: 'bg-green-600' },
    en_transit: { text: 'Disponible (Importation 15 jours)', cls: 'text-orange-600', dot: 'bg-orange-600' },
    rupture: { text: 'Sur commande', cls: 'text-primary', dot: 'bg-primary-container' },
};

const SERVICES = [
    { field: 'services_installation', icon: 'local_shipping', label: 'Installation et mise en service' },
    { field: 'services_formation', icon: 'school', label: 'Formation technique' },
    { field: 'services_maintenance', icon: 'build', label: 'Contrat de maintenance préventive' },
] as const;

export default function Recapitulatif({ sectors }: Props) {
    const cart = useQuoteCart();
    const [customItems, setCustomItems] = useState<CustomItem[]>([]);
    const [draft, setDraft] = useState({ custom_label: '', brand_name: '', quantity: 1 });

    const { data, setData, processing, errors } = useForm({
        responsible_name: '',
        structure_name: '',
        city: '',
        phone: '',
        email: '',
        sector: '',
        specifications: '',
        is_urgent: false,
        services_installation: false,
        services_formation: false,
        services_maintenance: false,
    });

    const totalArticles = cart.count + customItems.reduce((s, i) => s + i.quantity, 0);
    const isEmpty = cart.items.length === 0 && customItems.length === 0;

    const addCustomItem = () => {
        if (!draft.custom_label.trim()) {
            return;
        }
        setCustomItems((prev) => [
            ...prev,
            { key: crypto.randomUUID(), custom_label: draft.custom_label.trim(), brand_name: draft.brand_name.trim(), quantity: Math.max(1, draft.quantity) },
        ]);
        setDraft({ custom_label: '', brand_name: '', quantity: 1 });
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        const items = [
            ...cart.items.map((i) => ({ product_id: i.id, custom_label: null, brand_name: i.brand, quantity: i.quantity })),
            ...customItems.map((i) => ({ product_id: null, custom_label: i.custom_label, brand_name: i.brand_name || null, quantity: i.quantity })),
        ];

        router.post('/cotation', { ...data, items } as never, {
            preserveScroll: true,
            onSuccess: () => cart.clear(),
        });
    };

    const fieldClass =
        'w-full rounded-lg border border-outline-variant bg-surface-2 px-3 py-2.5 text-body-md text-secondary placeholder-secondary/40 transition-colors focus:border-primary focus:ring-1 focus:ring-primary';
    const labelClass = 'mb-1 block text-label-caps font-semibold uppercase text-secondary/70';

    return (
        <PublicLayout>
            <Head title="Récapitulatif de votre demande de cotation" />

            <main className="mx-auto w-full max-w-7xl px-sm sm:px-margin py-lg">
                <Link
                    href="/catalogue"
                    className="mb-md inline-flex items-center gap-1 text-body-sm font-semibold text-tertiary hover:underline"
                >
                    <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                        arrow_back
                    </span>
                    Continuer à explorer le catalogue
                </Link>

                <header className="mb-md lg:mb-lg">
                    <h1 className="text-2xl font-bold text-navy-blue sm:text-4xl lg:text-headline-xl">Récapitulatif de votre demande de cotation</h1>
                    <p className="mt-xs text-body-lg text-secondary/70">
                        {totalArticles} article{totalArticles > 1 ? 's' : ''} sélectionné{totalArticles > 1 ? 's' : ''}
                    </p>
                </header>

                {isEmpty ? (
                    <div className="rounded-xl border border-outline-variant bg-white p-md lg:p-xl text-center">
                        <span className="material-symbols-outlined text-[56px] text-secondary/25" aria-hidden="true">
                            request_quote
                        </span>
                        <h2 className="mt-sm text-headline-md text-secondary">Votre sélection est vide</h2>
                        <p className="mx-auto mt-xs max-w-md text-body-md text-secondary/70">
                            Ajoutez des produits depuis le catalogue, ou si vous ne trouvez pas une référence précise,
                            demandez-nous de l'importer pour vous.
                        </p>
                        <div className="mt-md flex flex-wrap justify-center gap-sm">
                            <Link
                                href="/catalogue"
                                className="rounded-lg bg-secondary px-5 py-2.5 text-body-md font-bold text-white transition-colors hover:bg-secondary/90"
                            >
                                Parcourir le catalogue
                            </Link>
                            <Link
                                href="/demande-cotation"
                                className="rounded-lg border border-primary px-5 py-2.5 text-body-md font-bold text-primary transition-colors hover:bg-primary/5"
                            >
                                Demander un import
                            </Link>
                        </div>
                    </div>
                ) : (
                    <form onSubmit={submit} className="grid grid-cols-1 gap-md lg:gap-lg lg:grid-cols-12">
                        {/* ── Colonne gauche : produits ───────────────── */}
                        <div className="flex flex-col gap-md lg:col-span-8">
                            <section className="rounded-xl border border-outline-variant bg-white">
                                <h2 className="border-b border-outline-variant px-md py-sm text-headline-md text-secondary">
                                    Ma sélection d'équipements
                                </h2>
                                <ul className="divide-y divide-outline-variant">
                                    {cart.items.map((item) => {
                                        const stock = STOCK[item.stockStatus] ?? STOCK.en_stock;
                                        return (
                                            <li key={item.id} className="flex gap-sm p-md">
                                                <div className="flex h-20 w-20 shrink-0 items-center justify-center rounded bg-surface-3">
                                                    {item.image ? (
                                                        <img src={item.image} alt="" className="h-full w-full object-contain p-1" />
                                                    ) : (
                                                        <span className="material-symbols-outlined text-3xl text-secondary/40" aria-hidden="true">
                                                            medical_services
                                                        </span>
                                                    )}
                                                </div>
                                                <div className="flex flex-1 flex-col">
                                                    <div className="flex items-start justify-between gap-2">
                                                        <div>
                                                            <h3 className="text-body-md font-bold leading-tight text-secondary">{item.name}</h3>
                                                            {item.brand && (
                                                                <p className="text-body-sm text-secondary/60">{item.brand}</p>
                                                            )}
                                                            <p className={`mt-1 flex items-center gap-1 text-[11px] font-bold ${stock.cls}`}>
                                                                <span className={`h-1.5 w-1.5 rounded-full ${stock.dot}`} aria-hidden="true" />
                                                                {stock.text}
                                                            </p>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            onClick={() => cart.removeItem(item.id)}
                                                            aria-label={`Retirer ${item.name}`}
                                                            className="shrink-0 rounded p-1 text-secondary/50 transition-colors hover:bg-primary/5 hover:text-primary"
                                                        >
                                                            <span className="material-symbols-outlined text-[20px]" aria-hidden="true">
                                                                delete
                                                            </span>
                                                        </button>
                                                    </div>
                                                    <div className="mt-auto flex items-center rounded border border-outline-variant self-start">
                                                        <button
                                                            type="button"
                                                            onClick={() => cart.updateQuantity(item.id, item.quantity - 1)}
                                                            aria-label="Diminuer la quantité"
                                                            className="flex h-8 w-8 items-center justify-center text-secondary transition-colors hover:bg-surface-2"
                                                        >
                                                            <span className="material-symbols-outlined text-[18px]" aria-hidden="true">remove</span>
                                                        </button>
                                                        <span className="w-8 text-center text-body-sm font-semibold tabular-nums">{item.quantity}</span>
                                                        <button
                                                            type="button"
                                                            onClick={() => cart.updateQuantity(item.id, item.quantity + 1)}
                                                            aria-label="Augmenter la quantité"
                                                            className="flex h-8 w-8 items-center justify-center text-secondary transition-colors hover:bg-surface-2"
                                                        >
                                                            <span className="material-symbols-outlined text-[18px]" aria-hidden="true">add</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                        );
                                    })}

                                    {customItems.map((item) => (
                                        <li key={item.key} className="flex gap-sm p-md">
                                            <div className="flex h-20 w-20 shrink-0 items-center justify-center rounded bg-surface-3">
                                                <span className="material-symbols-outlined text-3xl text-secondary/40" aria-hidden="true">
                                                    inventory_2
                                                </span>
                                            </div>
                                            <div className="flex flex-1 flex-col">
                                                <div className="flex items-start justify-between gap-2">
                                                    <div>
                                                        <h3 className="text-body-md font-bold leading-tight text-secondary">{item.custom_label}</h3>
                                                        {item.brand_name && <p className="text-body-sm text-secondary/60">{item.brand_name}</p>}
                                                        <p className="mt-1 text-[11px] font-bold text-tertiary">Article hors-catalogue · Qté {item.quantity}</p>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        onClick={() => setCustomItems((prev) => prev.filter((c) => c.key !== item.key))}
                                                        aria-label={`Retirer ${item.custom_label}`}
                                                        className="shrink-0 rounded p-1 text-secondary/50 transition-colors hover:bg-primary/5 hover:text-primary"
                                                    >
                                                        <span className="material-symbols-outlined text-[20px]" aria-hidden="true">delete</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </section>

                            {/* Spécifications */}
                            <section className="rounded-xl border border-outline-variant bg-white p-md">
                                <label htmlFor="specifications" className={labelClass}>
                                    Précisions sur votre projet ou spécifications techniques particulières
                                </label>
                                <textarea
                                    id="specifications"
                                    rows={4}
                                    value={data.specifications}
                                    onChange={(e) => setData('specifications', e.target.value)}
                                    placeholder="Tension, accessoires, contraintes d'installation, délais souhaités…"
                                    className={fieldClass}
                                />
                            </section>

                            {/* Ajout hors-catalogue */}
                            <section className="rounded-xl border border-dashed border-outline-variant bg-surface p-md">
                                <h3 className="mb-sm flex items-center gap-1 text-body-md font-bold text-secondary">
                                    <span className="material-symbols-outlined text-tertiary" aria-hidden="true">add</span>
                                    Ajouter un article hors-catalogue
                                </h3>
                                <div className="grid grid-cols-1 gap-sm sm:grid-cols-12">
                                    <input
                                        type="text"
                                        value={draft.custom_label}
                                        onChange={(e) => setDraft((d) => ({ ...d, custom_label: e.target.value }))}
                                        placeholder="Désignation de l'équipement"
                                        aria-label="Désignation de l'équipement hors-catalogue"
                                        className={`${fieldClass} sm:col-span-6`}
                                    />
                                    <input
                                        type="text"
                                        value={draft.brand_name}
                                        onChange={(e) => setDraft((d) => ({ ...d, brand_name: e.target.value }))}
                                        placeholder="Marque (optionnel)"
                                        aria-label="Marque"
                                        className={`${fieldClass} sm:col-span-3`}
                                    />
                                    <input
                                        type="number"
                                        min={1}
                                        value={draft.quantity}
                                        onChange={(e) => setDraft((d) => ({ ...d, quantity: Number(e.target.value) }))}
                                        aria-label="Quantité"
                                        className={`${fieldClass} sm:col-span-1`}
                                    />
                                    <button
                                        type="button"
                                        onClick={addCustomItem}
                                        className="rounded-lg bg-secondary px-3 py-2.5 text-body-sm font-bold text-white transition-colors hover:bg-secondary/90 sm:col-span-2"
                                    >
                                        Ajouter
                                    </button>
                                </div>
                            </section>
                        </div>

                        {/* ── Colonne droite : établissement + services ── */}
                        <div className="flex flex-col gap-md lg:col-span-4">
                            <div className="lg:sticky lg:top-[150px] flex flex-col gap-md">
                                <section className="rounded-xl border border-outline-variant bg-white p-md">
                                    <h2 className="mb-sm text-headline-md text-secondary">Informations établissement</h2>
                                    <div className="space-y-sm">
                                        <div>
                                            <label htmlFor="responsible_name" className={labelClass}>Nom du responsable *</label>
                                            <input id="responsible_name" type="text" value={data.responsible_name} onChange={(e) => setData('responsible_name', e.target.value)} className={fieldClass} required />
                                            {errors.responsible_name && <p className="mt-1 text-body-sm text-primary">{errors.responsible_name}</p>}
                                        </div>
                                        <div>
                                            <label htmlFor="structure_name" className={labelClass}>Structure *</label>
                                            <input id="structure_name" type="text" value={data.structure_name} onChange={(e) => setData('structure_name', e.target.value)} className={fieldClass} required />
                                            {errors.structure_name && <p className="mt-1 text-body-sm text-primary">{errors.structure_name}</p>}
                                        </div>
                                        <div className="grid grid-cols-2 gap-sm">
                                            <div>
                                                <label htmlFor="city" className={labelClass}>Ville *</label>
                                                <input id="city" type="text" value={data.city} onChange={(e) => setData('city', e.target.value)} className={fieldClass} required />
                                                {errors.city && <p className="mt-1 text-body-sm text-primary">{errors.city}</p>}
                                            </div>
                                            <div>
                                                <label htmlFor="phone" className={labelClass}>Téléphone</label>
                                                <input id="phone" type="tel" value={data.phone} onChange={(e) => setData('phone', e.target.value)} className={fieldClass} />
                                            </div>
                                        </div>
                                        <div>
                                            <label htmlFor="email" className={labelClass}>Email</label>
                                            <input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className={fieldClass} />
                                            {errors.email && <p className="mt-1 text-body-sm text-primary">{errors.email}</p>}
                                        </div>
                                        <div>
                                            <label htmlFor="sector" className={labelClass}>Secteur *</label>
                                            <select id="sector" value={data.sector} onChange={(e) => setData('sector', e.target.value)} className={fieldClass} required>
                                                <option value="" disabled>Sélectionner…</option>
                                                {sectors.map((s) => (
                                                    <option key={s} value={s}>{s}</option>
                                                ))}
                                            </select>
                                            {errors.sector && <p className="mt-1 text-body-sm text-primary">{errors.sector}</p>}
                                        </div>
                                    </div>
                                </section>

                                <section className="rounded-xl border border-outline-variant bg-white p-md">
                                    <h2 className="mb-sm text-headline-md text-secondary">Configuration des services</h2>
                                    <div className="space-y-2">
                                        {SERVICES.map((s) => (
                                            <label key={s.field} className="flex cursor-pointer items-center gap-2 rounded-lg border border-outline-variant p-2.5 transition-colors hover:bg-surface-2 has-[:checked]:border-tertiary has-[:checked]:bg-tertiary/5">
                                                <input
                                                    type="checkbox"
                                                    checked={data[s.field]}
                                                    onChange={(e) => setData(s.field, e.target.checked)}
                                                    className="h-4 w-4 rounded border-outline-variant text-tertiary focus:ring-tertiary"
                                                />
                                                <span className="material-symbols-outlined text-[20px] text-tertiary" aria-hidden="true">{s.icon}</span>
                                                <span className="text-body-sm font-medium text-secondary">{s.label}</span>
                                            </label>
                                        ))}
                                        <label className="flex cursor-pointer items-center gap-2 rounded-lg border border-outline-variant p-2.5 transition-colors hover:bg-surface-2 has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                            <input
                                                type="checkbox"
                                                checked={data.is_urgent}
                                                onChange={(e) => setData('is_urgent', e.target.checked)}
                                                className="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary"
                                            />
                                            <span className="material-symbols-outlined text-[20px] text-primary" aria-hidden="true">warning</span>
                                            <span className="text-body-sm font-bold text-primary">Besoin prioritaire (Urgent)</span>
                                        </label>
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
                                            Soumettre ma demande de cotation
                                            <span className="material-symbols-outlined text-lg" aria-hidden="true">send</span>
                                        </>
                                    )}
                                </button>

                                <p className="flex items-start gap-2 text-body-sm text-secondary/60">
                                    <span className="material-symbols-outlined text-[18px] text-tertiary" aria-hidden="true">verified_user</span>
                                    Transtechno Cameroun s'engage à fournir des équipements conformes aux normes OMS. Agrément Minsanté N° 0683.
                                </p>
                            </div>
                        </div>
                    </form>
                )}
            </main>
        </PublicLayout>
    );
}
