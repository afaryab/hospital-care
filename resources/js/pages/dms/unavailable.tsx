import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { home } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { FileWarning } from 'lucide-react';

type DocumentsUnavailableProps = {
    message: string;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: home().url },
    { title: 'Documents', href: '' },
];

export default function DocumentsUnavailable() {
    const { message } = usePage<DocumentsUnavailableProps>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Documents unavailable" />
            <div className="flex flex-1 items-center justify-center p-4">
                <div className="flex max-w-md flex-col items-center gap-4 rounded-xl bg-white p-8 text-center shadow-sm dark:bg-neutral-950">
                    <FileWarning className="h-10 w-10 text-amber-500" />
                    <h1 className="text-lg font-semibold">
                        Documents are temporarily unavailable
                    </h1>
                    <p className="text-sm text-muted-foreground">{message}</p>
                    <Button asChild variant="outline">
                        <Link href={home().url}>Back to dashboard</Link>
                    </Button>
                </div>
            </div>
        </AppLayout>
    );
}
