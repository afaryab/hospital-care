import AppFooter from '@/components/app-footer';
import HospitalBrand from '@/components/hospital-brand';
import { type PropsWithChildren } from 'react';

type PublicBookingShellProps = {
    title: string;
    description?: string;
};

export default function PublicBookingShell({
    title,
    description,
    children,
}: PropsWithChildren<PublicBookingShellProps>) {
    return (
        <div className="flex min-h-svh flex-col items-center bg-background px-4 py-8 md:py-12">
            <div className="w-full max-w-xl rounded-xl bg-white p-6 shadow-md md:p-8 dark:bg-neutral-900">
                <HospitalBrand className="my-0 mb-6 justify-center" />
                <div className="mb-6 space-y-1 text-center">
                    <h1 className="text-xl font-semibold">{title}</h1>
                    {description && (
                        <p className="text-sm text-muted-foreground">
                            {description}
                        </p>
                    )}
                </div>
                {children}
            </div>
            <AppFooter />
        </div>
    );
}
