import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { home, transactionView } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: home().url },
    { title: 'Transaction Search', href: '#' },
];

export default function TransactionSearch() {
    const [transactionNumber, setTransactionNumber] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        // Expected format: TR/YYYY/MM/DD/NNNN
        const parts = transactionNumber.trim().toUpperCase().split('/');
        if (parts[0] !== 'TR' || parts.length !== 5) {
            setError(
                'Enter a valid transaction number (e.g. TR/2024/01/15/0001)',
            );
            return;
        }

        const [, tYear, tMonth, tDay, tNumber] = parts;
        router.visit(transactionView({ tYear, tMonth, tDay, tNumber }).url);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Search Transaction" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-2 text-gray-800">
                    <div className="flex h-full w-full flex-col gap-4 overflow-x-auto rounded-xl bg-white p-2">
                        <div className="flex flex-1 flex-col">
                            <h2 className="text-center text-xl font-semibold">
                                Transaction Search
                            </h2>
                            <form
                                onSubmit={handleSubmit}
                                className="flex flex-1 flex-col items-center justify-center gap-4"
                            >
                                <div className="w-full max-w-sm space-y-2">
                                    <Label htmlFor="transactionNumber">
                                        Transaction Number
                                    </Label>
                                    <input
                                        id="transactionNumber"
                                        type="text"
                                        value={transactionNumber}
                                        onChange={(e) => {
                                            setTransactionNumber(
                                                e.target.value,
                                            );
                                            setError('');
                                        }}
                                        className="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        placeholder="TR/2024/01/15/0001"
                                        autoFocus
                                    />
                                    {error && (
                                        <p className="text-sm text-red-600">
                                            {error}
                                        </p>
                                    )}
                                </div>
                                <Button
                                    type="submit"
                                    className="w-full max-w-sm"
                                    disabled={!transactionNumber.trim()}
                                >
                                    View Transaction
                                </Button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
