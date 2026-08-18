import AppLogoIcon from '@/components/app-logo-icon';

/**
 * The application's own branding, relocated here from the header now that
 * the header shows the hospital's own logo/name (see HospitalBrand) instead.
 */
export default function AppFooter({ className = '' }: { className?: string }) {
    return (
        <div
            className={`flex flex-col items-center gap-1 py-3 text-center ${className}`}
        >
            <AppLogoIcon
                size={6}
                direction="horizontal"
                className="opacity-70"
            />
            <span className="text-[11px] text-muted-foreground">
                Developed by{' '}
                <a
                    href="https://hospitalos.pk"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="underline underline-offset-2 hover:text-foreground"
                >
                    hospitalos.pk
                </a>
            </span>
        </div>
    );
}
