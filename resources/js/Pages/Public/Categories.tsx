import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

interface CategoryItem {
    id: number;
    name: string;
    slug: string;
    icon: string;
    products_count: number;
}

interface CategoriesPageProps {
    categories: CategoryItem[];
}

export default function Categories({ categories }: CategoriesPageProps) {
    return (
        <PublicLayout>
            <Head title="Catégories de produits — Transtechno Cameroun">
                <meta name="description" content="Explorez nos 33 catégories d'équipements médicaux : bloc opératoire, cardiologie, laboratoire, monitoring, échographie, stérilisation et bien plus. Matériel certifié OMS disponible à Douala." head-key="description" />
                <meta property="og:title" content="Catégories de produits — Transtechno Cameroun" head-key="og:title" />
                <meta property="og:description" content="33 catégories d'équipements médicaux certifiés OMS : bloc opératoire, cardiologie, laboratoire, imagerie, stérilisation… Douala, Cameroun." head-key="og:description" />
                <meta property="og:type" content="website" head-key="og:type" />
                <meta property="og:site_name" content="Transtechno Cameroun" head-key="og:site_name" />
                <meta name="twitter:card" content="summary" head-key="twitter:card" />
            </Head>

            <div className="mx-auto max-w-7xl px-4 py-lg md:px-margin">
                {/* En-tête de page */}
                <header className="mb-md">
                    <h1 className="text-headline-lg text-navy-blue">Nos Catégories de Produits</h1>
                    <div className="mt-2 h-1 w-16 rounded bg-primary-container" />
                    <p className="mt-sm max-w-2xl text-body-md text-secondary">
                        Explorez notre gamme complète d'équipements médicaux, physiques et géophysiques certifiés OMS,
                        importés et distribués au Cameroun.
                    </p>
                </header>

                {/* Grille des catégories */}
                <ul className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    {categories.map((category) => (
                        <li key={category.id}>
                            <Link
                                href={`/catalogue?categorie=${category.slug}`}
                                className="group flex min-h-[56px] items-center gap-3 rounded-lg border border-outline-variant bg-white px-4 py-3 transition-colors hover:border-tertiary hover:bg-surface-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-tertiary"
                            >
                                <span
                                    className="material-symbols-outlined text-primary-container transition-transform group-hover:translate-x-0.5"
                                    aria-hidden="true"
                                >
                                    play_arrow
                                </span>
                                <span className="flex-1 text-body-md font-medium uppercase tracking-wide text-secondary group-hover:text-navy-blue">
                                    {category.name}
                                </span>
                                {category.products_count > 0 && (
                                    <span className="rounded-full bg-surface-3 px-2 py-0.5 text-label-caps text-secondary">
                                        {category.products_count}
                                    </span>
                                )}
                            </Link>
                        </li>
                    ))}
                </ul>

                {categories.length === 0 && (
                    <p className="rounded-lg border border-outline-variant bg-white p-md text-center text-body-md text-secondary">
                        Aucune catégorie disponible pour le moment.
                    </p>
                )}
            </div>
        </PublicLayout>
    );
}
