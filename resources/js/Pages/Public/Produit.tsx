import WhatsAppButton from '@/Components/Public/WhatsAppButton';
import { openQuoteDrawer, StockStatus, useQuoteCart } from '@/hooks/useQuoteCart';
import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import DOMPurify from 'dompurify';
import { useState } from 'react';

interface Spec {
    label: string;
    value: string;
}

interface Reagent {
    storage_temp: string;
    conditionnement: string | null;
    volume_quantity: string | null;
    is_ivd: boolean;
    lot_number_example: string | null;
    expiry_note: string | null;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    url: string;
    reference: string | null;
    short_description: string | null;
    long_description: string | null;
    brand: string | null;
    category: { name: string; slug: string } | null;
    poles: string[];
    product_type: 'equipement' | 'reactif' | 'consommable';
    stock_status: StockStatus;
    price_fcfa: number | null;
    images: string[];
    pdf_url: string | null;
    specs: Spec[];
    reagent: Reagent | null;
}

interface Related {
    name: string;
    slug: string;
    short_description: string | null;
    image: string | null;
}

interface ProduitPageProps {
    product: Product;
    related: Related[];
}

const STOCK_INFO: Record<StockStatus, { text: string; cls: string; dot: string }> = {
    en_stock: { text: 'En stock à Douala', cls: 'text-green-700', dot: 'bg-green-600' },
    en_transit: { text: 'Disponible sous ~15 jours (importation)', cls: 'text-orange-600', dot: 'bg-orange-500' },
    rupture: { text: 'Sur commande', cls: 'text-primary', dot: 'bg-primary-container' },
};

const REASSURANCE = [
    { icon: 'engineering', title: 'Installation sur site', text: 'Mise en service par nos ingénieurs experts certifiés.' },
    { icon: 'school', title: 'Formation des utilisateurs', text: 'Sessions de formation technique pour vos techniciens.' },
    { icon: 'handyman', title: 'Maintenance & SAV à Douala', text: 'Intervention rapide locale et stock de pièces détachées.' },
    { icon: 'verified_user', title: 'Garantie Constructeur', text: 'Couverture complète et support technique illimité.' },
];

function formatFcfa(value: number): string {
    return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
}

export default function Produit({ product, related }: ProduitPageProps) {
    const cart = useQuoteCart();
    const [activeImage, setActiveImage] = useState(0);
    const [tab, setTab] = useState<'description' | 'specs'>('description');

    const hasImages = product.images.length > 0;
    const isReagent = product.product_type === 'reactif';
    const inCart = cart.has(product.id);
    const stock = STOCK_INFO[product.stock_status];

    function addToQuote() {
        cart.addItem({
            id: product.id,
            name: product.name,
            slug: product.slug,
            reference: product.reference,
            brand: product.brand,
            image: hasImages ? product.images[0] : null,
            stockStatus: product.stock_status,
        });
        openQuoteDrawer();
    }

    return (
        <PublicLayout>
            <Head title={`${product.name} — Transtechno Cameroun`}>
                {product.short_description && (
                    <meta name="description" content={product.short_description} head-key="description" />
                )}
                <link rel="canonical" href={product.url} head-key="canonical" />
                <meta property="og:url" content={product.url} head-key="og:url" />
                <meta property="og:title" content={`${product.name} — Transtechno Cameroun`} head-key="og:title" />
                {product.short_description && (
                    <meta property="og:description" content={product.short_description} head-key="og:description" />
                )}
                <meta property="og:type" content="product" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                {product.images[0] && <meta property="og:image" content={product.images[0]} head-key="og:image" />}
                <meta name="twitter:card" content={product.images[0] ? 'summary_large_image' : 'summary'} head-key="twitter:card" />
            </Head>

            <div className="mx-auto max-w-7xl px-4 py-lg md:px-margin">
                {/* Fil d'Ariane */}
                <nav aria-label="Fil d'Ariane" className="mb-md flex flex-wrap items-center gap-y-1 text-body-sm text-secondary/70">
                    <Link href="/" className="hover:text-tertiary">
                        Accueil
                    </Link>
                    <span className="px-1.5">/</span>
                    <Link href="/categories" className="hover:text-tertiary">
                        Catégories
                    </Link>
                    {product.category && (
                        <>
                            <span className="px-1.5">/</span>
                            <Link href={`/catalogue?categorie=${product.category.slug}`} className="hover:text-tertiary">
                                {product.category.name}
                            </Link>
                        </>
                    )}
                    <span className="px-1.5">/</span>
                    <span className="text-secondary">{product.name}</span>
                </nav>

                {/* Hero : galerie + infos */}
                <section className="mb-lg lg:mb-xl grid grid-cols-1 gap-md lg:grid-cols-2 lg:gap-lg">
                    {/* Galerie */}
                    <div className="space-y-sm">
                        <div className="flex aspect-square items-center justify-center overflow-hidden rounded-lg border border-outline-variant bg-white">
                            {hasImages ? (
                                <img
                                    src={product.images[activeImage]}
                                    alt={product.name}
                                    className="h-full w-full object-contain p-4"
                                />
                            ) : (
                                <span className="material-symbols-outlined text-[96px] text-secondary/20" aria-hidden="true">
                                    {isReagent ? 'biotech' : 'medical_services'}
                                </span>
                            )}
                        </div>
                        {product.images.length > 1 && (
                            <div className="grid grid-cols-4 gap-xs">
                                {product.images.map((img, i) => (
                                    <button
                                        key={img}
                                        type="button"
                                        onClick={() => setActiveImage(i)}
                                        aria-label={`Image ${i + 1}`}
                                        className={`flex aspect-square items-center justify-center overflow-hidden rounded-lg border bg-white ${
                                            i === activeImage ? 'border-2 border-primary' : 'border-outline-variant hover:border-secondary'
                                        }`}
                                    >
                                        <img src={img} alt="" className="h-full w-full object-cover" />
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Infos */}
                    <div className="flex flex-col">
                        {product.brand && (
                            <span className="mb-xs text-label-caps uppercase text-secondary/60">{product.brand}</span>
                        )}
                        <h1 className="text-headline-lg text-navy-blue">{product.name}</h1>
                        {product.short_description && (
                            <h2 className="mt-xs text-headline-md text-secondary">{product.short_description}</h2>
                        )}

                        {/* Badges certifications */}
                        <div className="mt-md flex flex-wrap gap-xs">
                            <span className="flex items-center gap-xs rounded-full bg-surface-4 px-sm py-1 text-label-caps text-secondary">
                                <span className="material-symbols-outlined text-[16px]" aria-hidden="true">
                                    verified
                                </span>
                                Certifié OMS
                            </span>
                            <span className="flex items-center gap-xs rounded-full bg-surface-4 px-sm py-1 text-label-caps text-secondary">
                                <span className="material-symbols-outlined text-[16px]" aria-hidden="true">
                                    approval
                                </span>
                                Agréé Minsanté (N° 0683)
                            </span>
                            {isReagent && product.reagent?.is_ivd && (
                                <span className="flex items-center gap-xs rounded-full bg-tertiary/10 px-sm py-1 text-label-caps text-tertiary">
                                    <span className="material-symbols-outlined text-[16px]" aria-hidden="true">
                                        science
                                    </span>
                                    Certifié IVD
                                </span>
                            )}
                        </div>

                        {/* Boîte prix + CTA */}
                        <div className="mt-md rounded-xl border border-outline-variant bg-white p-md">
                            {/* Disponibilité */}
                            <p className={`mb-sm flex items-center gap-xs text-body-sm font-semibold ${stock.cls}`}>
                                <span className={`h-2.5 w-2.5 rounded-full ${stock.dot}`} aria-hidden="true" />
                                {stock.text}
                            </p>

                            <p className="mb-md text-headline-md font-bold text-primary">
                                {product.price_fcfa ? formatFcfa(product.price_fcfa) : 'Prix sur devis personnalisé'}
                            </p>

                            <div className="flex flex-col gap-sm">
                                <button
                                    type="button"
                                    onClick={addToQuote}
                                    className="flex w-full items-center justify-center gap-sm rounded-lg bg-primary-container py-3 text-body-lg font-bold text-white transition-colors hover:bg-primary"
                                >
                                    <span className="material-symbols-outlined" aria-hidden="true">
                                        request_quote
                                    </span>
                                    Demander une cotation
                                </button>

                                {inCart && (
                                    <button
                                        type="button"
                                        onClick={openQuoteDrawer}
                                        className="flex items-center justify-center gap-xs text-body-sm font-semibold text-green-700 hover:underline"
                                    >
                                        <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                                            check_circle
                                        </span>
                                        Déjà dans votre sélection — voir
                                    </button>
                                )}

                                {product.pdf_url ? (
                                    <a
                                        href={product.pdf_url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="flex w-full items-center justify-center gap-sm rounded-lg border border-secondary py-3 text-body-lg font-bold text-secondary transition-colors hover:bg-surface-2"
                                    >
                                        <span className="material-symbols-outlined" aria-hidden="true">
                                            download
                                        </span>
                                        Télécharger la fiche technique (PDF)
                                    </a>
                                ) : (
                                    <button
                                        type="button"
                                        disabled
                                        title="Fiche technique bientôt disponible"
                                        className="flex w-full cursor-not-allowed items-center justify-center gap-sm rounded-lg border border-outline-variant py-3 text-body-lg font-bold text-secondary/40"
                                    >
                                        <span className="material-symbols-outlined" aria-hidden="true">
                                            picture_as_pdf
                                        </span>
                                        Fiche technique (PDF)
                                    </button>
                                )}

                                <WhatsAppButton
                                    variant="outline"
                                    className="w-full py-3 text-body-lg"
                                    label="Demander sur WhatsApp"
                                    message={`Bonjour Transtechno, je suis intéressé(e) par "${product.name}"${product.reference ? ` (réf. ${product.reference})` : ''}. Pouvez-vous me faire une cotation ?`}
                                />
                            </div>
                        </div>

                        {/* Points clés (3 premières specs) */}
                        {product.specs.length > 0 && (
                            <ul className="mt-md space-y-sm text-body-md text-secondary">
                                {product.specs.slice(0, 3).map((spec) => (
                                    <li key={spec.label} className="flex gap-sm">
                                        <span className="material-symbols-outlined text-primary" aria-hidden="true">
                                            check_circle
                                        </span>
                                        <span>
                                            <strong>{spec.label} :</strong> {spec.value}
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </section>

                {/* Onglets Description / Caractéristiques */}
                <section className="mb-lg lg:mb-xl">
                    <div className="mb-md flex gap-md sm:gap-lg border-b border-outline-variant">
                        <button
                            type="button"
                            onClick={() => setTab('description')}
                            className={`pb-sm text-body-md font-semibold uppercase tracking-wider transition-colors ${
                                tab === 'description'
                                    ? 'border-b-2 border-primary text-primary'
                                    : 'text-secondary/70 hover:text-primary'
                            }`}
                        >
                            Description
                        </button>
                        {(product.specs.length > 0 || isReagent) && (
                            <button
                                type="button"
                                onClick={() => setTab('specs')}
                                className={`pb-sm text-body-md font-semibold uppercase tracking-wider transition-colors ${
                                    tab === 'specs'
                                        ? 'border-b-2 border-primary text-primary'
                                        : 'text-secondary/70 hover:text-primary'
                                }`}
                            >
                                Caractéristiques techniques
                            </button>
                        )}
                    </div>

                    {tab === 'description' ? (
                        <div className="max-w-4xl">
                            {product.long_description ? (
                                <div
                                    className="prose max-w-none text-body-lg text-secondary"
                                    dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(product.long_description) }}
                                />
                            ) : (
                                <p className="text-body-lg text-secondary">
                                    {product.short_description ?? 'Description détaillée à venir.'}
                                </p>
                            )}
                            {product.poles.length > 0 && (
                                <div className="mt-md flex flex-wrap gap-xs">
                                    {product.poles.map((pole) => (
                                        <span key={pole} className="rounded-full bg-surface-3 px-sm py-1 text-body-sm text-secondary">
                                            {pole}
                                        </span>
                                    ))}
                                </div>
                            )}
                        </div>
                    ) : (
                        <div className="overflow-x-auto rounded-xl border border-outline-variant">
                            <table className="w-full min-w-[480px] border-collapse text-left">
                                <thead className="bg-secondary uppercase text-white">
                                    <tr>
                                        <th className="p-md text-label-caps">Spécification</th>
                                        <th className="p-md text-label-caps">Détails</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-outline-variant text-body-sm">
                                    {/* Détails réactif (ABM) en priorité */}
                                    {isReagent && product.reagent && (
                                        <>
                                            <tr>
                                                <td className="bg-white p-md font-bold text-secondary">Température de stockage</td>
                                                <td className="p-md">{product.reagent.storage_temp}</td>
                                            </tr>
                                            {product.reagent.conditionnement && (
                                                <tr>
                                                    <td className="bg-white p-md font-bold text-secondary">Conditionnement</td>
                                                    <td className="p-md">{product.reagent.conditionnement}</td>
                                                </tr>
                                            )}
                                            {product.reagent.volume_quantity && (
                                                <tr>
                                                    <td className="bg-white p-md font-bold text-secondary">Volume / Quantité</td>
                                                    <td className="p-md">{product.reagent.volume_quantity}</td>
                                                </tr>
                                            )}
                                            <tr>
                                                <td className="bg-white p-md font-bold text-secondary">Usage</td>
                                                <td className="p-md">{product.reagent.is_ivd ? 'Diagnostic in vitro (IVD)' : 'Recherche uniquement'}</td>
                                            </tr>
                                            {product.reagent.expiry_note && (
                                                <tr>
                                                    <td className="bg-white p-md font-bold text-secondary">Conservation</td>
                                                    <td className="p-md">{product.reagent.expiry_note}</td>
                                                </tr>
                                            )}
                                        </>
                                    )}
                                    {product.reference && (
                                        <tr>
                                            <td className="bg-white p-md font-bold text-secondary">Référence</td>
                                            <td className="p-md">{product.reference}</td>
                                        </tr>
                                    )}
                                    {product.specs.map((spec) => (
                                        <tr key={spec.label}>
                                            <td className="bg-white p-md font-bold text-secondary">{spec.label}</td>
                                            <td className="p-md">{spec.value}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </section>

                {/* Blocs réassurance */}
                <section className="mb-lg lg:mb-xl grid grid-cols-1 gap-md sm:grid-cols-2 lg:grid-cols-4">
                    {REASSURANCE.map((r) => (
                        <div
                            key={r.title}
                            className="space-y-xs rounded-xl border border-outline-variant p-md text-center transition-shadow hover:shadow-sm"
                        >
                            <span className="material-symbols-outlined text-headline-lg text-primary" aria-hidden="true">
                                {r.icon}
                            </span>
                            <h3 className="text-label-caps uppercase text-secondary">{r.title}</h3>
                            <p className="text-body-sm text-secondary/70">{r.text}</p>
                        </div>
                    ))}
                </section>

                {/* Produits similaires */}
                {related.length > 0 && (
                    <section className="mb-lg lg:mb-xl">
                        <h2 className="mb-md lg:mb-lg border-l-4 border-primary pl-sm text-headline-md text-secondary">Produits similaires</h2>
                        <div className="grid grid-cols-1 gap-md sm:grid-cols-2 md:grid-cols-3">
                            {related.map((item) => (
                                <Link
                                    key={item.slug}
                                    href={`/produits/${item.slug}`}
                                    className="overflow-hidden rounded-xl border border-outline-variant bg-white transition-shadow hover:shadow-md"
                                >
                                    <div className="flex h-48 items-center justify-center bg-surface-2">
                                        {item.image ? (
                                            <img src={item.image} alt={item.name} loading="lazy" className="h-full w-full object-contain p-3" />
                                        ) : (
                                            <span className="material-symbols-outlined text-5xl text-secondary/30" aria-hidden="true">
                                                medical_services
                                            </span>
                                        )}
                                    </div>
                                    <div className="p-md">
                                        <h3 className="mb-xs text-body-md font-bold text-secondary">{item.name}</h3>
                                        {item.short_description && (
                                            <p className="mb-md line-clamp-2 text-body-sm text-secondary/70">{item.short_description}</p>
                                        )}
                                        <span className="flex items-center gap-xs text-body-sm font-bold text-primary">
                                            Voir plus
                                            <span className="material-symbols-outlined text-[18px]" aria-hidden="true">
                                                arrow_forward
                                            </span>
                                        </span>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    </section>
                )}
            </div>
        </PublicLayout>
    );
}
