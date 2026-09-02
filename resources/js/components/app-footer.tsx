import AppLogoIcon from '@/components/app-logo-icon';

/**
 * The application's own branding, relocated here from the header now that
 * the header shows the hospital's own logo/name (see HospitalBrand) instead.
 */
export default function AppFooter({ className = '' }: { className?: string }) {
    return (
        <div
            className={`fixed right-2 bottom-5 z-[99] inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/80 px-4 py-2 shadow-lg backdrop-blur-md transition hover:shadow-xl dark:bg-neutral-900/70 dark:text-neutral-100 ${className}`}
        >
            <AppLogoIcon
                size={3}
                direction="horizontal"
                className="opacity-70"
            />
            <span className="text-[9px] text-muted-foreground">
                <a
                    href="https://medicalos.pk"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="underline underline-offset-2 hover:text-foreground"
                >
                    medicalos.pk
                </a>
            </span>
        </div>
    );
}
