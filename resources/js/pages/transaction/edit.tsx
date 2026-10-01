import AppLayout from '@/layouts/app-layout';
import { home } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { LucideAlertTriangle, LucideRefreshCw } from 'lucide-react';

export default function TransactionEdit() {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: home().url },
    ];

    const { props } = usePage<{ transaction?: { tr_number: string } }>();
    const transaction = props.transaction;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Transaction Edit - Not Available" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-amber-50 p-1 dark:bg-[#262626] dark:bg-amber-950/40">
                <div className="flex h-full flex-1 flex-col items-center justify-center gap-6 rounded-xl bg-white p-8 text-gray-800 dark:bg-neutral-900 dark:text-neutral-100">
                    <div className="flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
                        <LucideAlertTriangle className="h-8 w-8 text-amber-600 dark:text-amber-400" />
                    </div>
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-neutral-100">
                        Editing Transactions is Not Allowed
                    </h2>
                    {transaction && (
                        <p className="font-mono text-sm text-gray-500 dark:text-neutral-400">
                            Transaction: {transaction.tr_number}
                        </p>
                    )}
                    <div className="max-w-md space-y-3 text-center">
                        <p className="text-gray-600 dark:text-neutral-300">
                            To maintain accurate financial records, direct
                            editing of transactions is not permitted.
                        </p>
                        <div className="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-left dark:border-blue-900 dark:bg-blue-950/40">
                            <LucideRefreshCw className="mt-0.5 h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" />
                            <div>
                                <p className="font-semibold text-blue-900 dark:text-blue-300">
                                    To correct a transaction:
                                </p>
                                <ol className="mt-1 list-inside list-decimal space-y-1 text-sm text-blue-800 dark:text-blue-300">
                                    <li>
                                        <strong>Refund</strong> the existing
                                        transaction
                                    </li>
                                    <li>
                                        <strong>Create a new</strong>{' '}
                                        transaction with the corrected details
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <p className="text-xs text-gray-400">
                            This ensures a complete audit trail of all financial
                            changes.
                        </p>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
