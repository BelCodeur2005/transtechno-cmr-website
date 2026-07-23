import { usePage } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import { PageProps } from '@/types';

const produitLinks = [
    { label: 'Toutes les catégories', href: '/categories' },
    { label: 'Réactifs', href: '/reactifs' },
    { label: 'Pôle Hémodialyse', href: '/pole-hemodialyse' },
    { label: 'Demander un devis', href: '/demande-cotation' },
];

const entrepriseLinks = [
    { label: 'Qui sommes-nous', href: '/qui-sommes-nous' },
    { label: 'Services & SAV', href: '/services-sav' },
    { label: 'Actualités', href: '/actualites' },
    { label: 'Contactez-nous', href: '/contact' },
];

function FacebookIcon() {
    return (
        <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5" aria-hidden="true">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
        </svg>
    );
}

function LinkedInIcon() {
    return (
        <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5" aria-hidden="true">
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
        </svg>
    );
}

export default function Footer() {
    const { company } = usePage<PageProps>().props;
    const year = new Date().getFullYear();

    return (
        <footer className="border-t-4 border-brand-red bg-dark-navy text-white">
            <div className="mx-auto max-w-7xl px-4 py-lg md:px-margin">
                <div className="grid grid-cols-1 gap-md sm:grid-cols-2 lg:grid-cols-4">
                    {/* Col 1 — Logo + présentation */}
                    <div>
                        <div className="mb-sm inline-block rounded bg-white p-2">
                            <img
                                src="/images/logo.png"
                                alt="Transtechno Cameroun"
                                className="h-14 w-auto object-contain"
                            />
                        </div>
                        <p className="text-body-sm leading-relaxed text-white/70">
                            Science technology and medical instrument. Leader au Cameroun pour l'équipement hospitalier
                            de haute précision. Solutions clés en main pour cliniques et hôpitaux.
                        </p>
                        <div className="mt-sm flex gap-2">
                            <a
                                href="https://www.facebook.com/TRANSTECHNO-CAMEROUN"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Page Facebook Transtechno Cameroun"
                                className="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20"
                            >
                                <FacebookIcon />
                            </a>
                            <a
                                href="https://www.linkedin.com/company/transtechno-cameroun"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Page LinkedIn Transtechno Cameroun"
                                className="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20"
                            >
                                <LinkedInIcon />
                            </a>
                        </div>
                    </div>

                    {/* Col 2 — Navigation (2 sous-groupes) */}
                    <div className="grid grid-cols-2 gap-sm sm:col-span-2 lg:col-span-1 lg:grid-cols-1 lg:gap-md">
                        <nav aria-label="Liens produits">
                            <h2 className="mb-sm text-label-caps uppercase text-brand-red">Produits</h2>
                            <ul className="space-y-2">
                                {produitLinks.map((link) => (
                                    <li key={link.label}>
                                        <Link href={link.href} className="text-body-sm text-white/70 hover:text-white">
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </nav>
                        <nav aria-label="Liens entreprise">
                            <h2 className="mb-sm text-label-caps uppercase text-brand-red">Entreprise</h2>
                            <ul className="space-y-2">
                                {entrepriseLinks.map((link) => (
                                    <li key={link.label}>
                                        <Link href={link.href} className="text-body-sm text-white/70 hover:text-white">
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </nav>
                    </div>

                    {/* Col 3 — Contact */}
                    <div>
                        <h2 className="mb-sm text-label-caps uppercase text-brand-red">Contact</h2>
                        <ul className="space-y-2 text-body-sm text-white/70">
                            <li className="flex items-start gap-2">
                                <span className="material-symbols-outlined text-[18px] text-brand-red" aria-hidden="true">mail</span>
                                <a href={`mailto:${company.email}`} className="hover:text-white">{company.email}</a>
                            </li>
                            <li className="flex items-start gap-2">
                                <span className="material-symbols-outlined text-[18px] text-brand-red" aria-hidden="true">smartphone</span>
                                <span>
                                    <a href={`tel:${company.phone_1.replace(/\s/g, '')}`} className="hover:text-white">{company.phone_1}</a>
                                    <br />
                                    <a href={`https://wa.me/${company.whatsapp}`} target="_blank" rel="noopener noreferrer" className="hover:text-white">
                                        {company.phone_2} <span className="text-[10px] opacity-70">(WhatsApp)</span>
                                    </a>
                                </span>
                            </li>
                            {company.phone_3 && (
                                <li className="flex items-start gap-2">
                                    <span className="material-symbols-outlined text-[18px] text-brand-red" aria-hidden="true">call</span>
                                    <a href={`tel:${company.phone_3.replace(/\s/g, '')}`} className="hover:text-white">{company.phone_3}</a>
                                </li>
                            )}
                            <li className="flex items-start gap-2">
                                <span className="material-symbols-outlined text-[18px] text-brand-red" aria-hidden="true">location_on</span>
                                <span>
                                    {company.address_line1},<br />
                                    {company.address_line2}<br />
                                    {company.address_city}
                                </span>
                            </li>
                        </ul>
                    </div>

                    {/* Col 4 — Certifications */}
                    <div>
                        <h2 className="mb-sm text-label-caps uppercase text-brand-red">Certifications</h2>
                        <ul className="space-y-2">
                            <li className="flex items-center gap-2 rounded-lg bg-white/5 px-3 py-2">
                                <span className="material-symbols-outlined text-tertiary" aria-hidden="true">
                                    verified
                                </span>
                                <span className="text-body-sm font-medium">Certifié OMS Qualité</span>
                            </li>
                            <li className="flex items-start gap-2 rounded-lg bg-white/5 px-3 py-2">
                                <span className="material-symbols-outlined mt-0.5 text-tertiary" aria-hidden="true">
                                    health_and_safety
                                </span>
                                <span className="text-body-sm font-medium">
                                    Agréé Minsanté
                                    <br />
                                    <span className="text-white/50 font-normal">Arrêté N° 0683</span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {/* Barre inférieure */}
            <div className="border-t border-white/10">
                <div className="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-label-caps uppercase text-white/50 sm:flex-row sm:items-center sm:justify-between md:px-margin">
                    <p>© {year} Transtechno Cameroun. Tous droits réservés.</p>
                    <div className="flex flex-wrap gap-sm sm:gap-md">
                        <Link href="/politique-confidentialite" className="hover:text-white">
                            Confidentialité
                        </Link>
                        <Link href="/politique-confidentialite#cookies" className="hover:text-white">
                            Cookies
                        </Link>
                        <Link href="/mentions-legales" className="hover:text-white">
                            Mentions légales
                        </Link>
                        <Link href="/conditions-vente" className="hover:text-white">
                            Conditions de vente
                        </Link>
                    </div>
                </div>
            </div>
        </footer>
    );
}
