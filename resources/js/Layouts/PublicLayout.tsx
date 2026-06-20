import CookieBanner from '@/Components/Public/CookieBanner';
import Footer from '@/Components/Public/Footer';
import Header from '@/Components/Public/Header';
import QuoteDrawer from '@/Components/Public/QuoteDrawer';
import WhatsAppFab from '@/Components/Public/WhatsAppFab';
import { PropsWithChildren } from 'react';

interface PublicLayoutProps {
    /** Nombre d'articles dans le panier de devis (badge header). */
    quoteCount?: number;
}

export default function PublicLayout({ children, quoteCount = 0 }: PropsWithChildren<PublicLayoutProps>) {
    return (
        <div className="flex min-h-dvh flex-col bg-background text-secondary">
            <a
                href="#main-content"
                className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded focus:bg-navy-blue focus:px-4 focus:py-2 focus:text-white"
            >
                Aller au contenu principal
            </a>
            <Header quoteCount={quoteCount} />
            <main id="main-content" className="flex-1">
                {children}
            </main>
            <Footer />
            <QuoteDrawer />
            <WhatsAppFab />
            <CookieBanner />
        </div>
    );
}
