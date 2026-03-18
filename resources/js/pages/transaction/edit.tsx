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
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-amber-50 dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col items-center justify-center gap-6 rounded-xl p-8 bg-white text-gray-800">
                    <div className="flex items-center justify-center w-16 h-16 rounded-full bg-amber-100">
                        <LucideAlertTriangle className="w-8 h-8 text-amber-600" />
                    </div>
                    <h2 className="text-2xl font-bold text-gray-900">Editing Transactions is Not Allowed</h2>
                    {transaction && (
                        <p className="text-sm text-gray-500 font-mono">Transaction: {transaction.tr_number}</p>
                    )}
                    <div className="max-w-md text-center space-y-3">
                        <p className="text-gray-600">
                            To maintain accurate financial records, direct editing of transactions is not permitted.
                        </p>
                        <div className="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-lg p-4 text-left">
                            <LucideRefreshCw className="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                            <div>
                                <p className="font-semibold text-blue-900">To correct a transaction:</p>
                                <ol className="mt-1 text-sm text-blue-800 list-decimal list-inside space-y-1">
                                    <li><strong>Refund</strong> the existing transaction</li>
                                    <li><strong>Create a new</strong> transaction with the corrected details</li>
                                </ol>
                            </div>
                        </div>
                        <p className="text-xs text-gray-400">
                            This ensures a complete audit trail of all financial changes.
                        </p>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
