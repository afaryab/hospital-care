import { type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

/**
 * The hospital's own branding — its uploaded logo (Hospital Settings) with a
 * white backing so transparent/dark logos stay legible, plus its name. Falls
 * back to the hospital name alone when no logo has been uploaded; the
 * application's own logo lives in the footer (see AppFooter), not here.
 */
export default function HospitalBrand({
    className = '',
}: {
    className?: string;
}) {
    const { hospital } = usePage<SharedData>().props;
    const name = hospital?.name || 'Hospital Care';

    return (
        <div className={`flex min-w-0 items-center gap-2 my-8 ${className}`}>
            {hospital?.logoUrl ? (<>
                <span className="inline-flex shrink-0 items-center justify-center rounded-md bg-white p-1">
                    <img
                        src={hospital.logoUrl}
                        alt={name}
                        className="h-12 w-auto object-contain"
                    />
                </span>
                <span className="truncate text-md leading-tight font-semibold group-data-[collapsible=icon]:hidden">
                    {name}
                </span>
            </>) : (<>
                <span className="flex aspect-square size-8 shrink-0 items-center justify-center rounded-md text-sm font-semibold text-sidebar-primary-foreground">
                    <img
                        src="/logo.png"
                        alt={name}
                        className="h-12 w-auto object-contain"
                    />
                </span>
                <span className="truncate text-md leading-tight font-semibold group-data-[collapsible=icon]:hidden">
                    {name}
                </span>
            </>)}
        </div>
    );
}
