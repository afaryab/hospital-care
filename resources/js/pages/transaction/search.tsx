import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import BulletsWrapper from '@/elements/bullets-wrapper';
import AppLayout from '@/layouts/app-layout';
import { home } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

export default function TransactionSearch() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        // {
        //     title: 'Counters',
        //     href: openCounter ? counterView({
        //         ctYear: openCounter.year,
        //         ctMonth: openCounter.month,
        //         ctNumber: openCounter.number
        //     }).url : myCounterList().url,
        // },
    ];

    const bullets: any[] = [];

    // openCounter && bullets.push({
    //     title: openCounter && openCounter.ct_number,
    //     url: openCounter && counterView({
    //         ctYear: openCounter.year,
    //         ctMonth: openCounter.month,
    //         ctNumber: openCounter.number
    //     }).url,
    //     active: false
    // });

    // if(transaction.income_or_expense === 'INCOME'){

    //     bullets.push({
    //         title: `Patient ${transaction?.patient?.ps_number}`,
    //         url: transaction?.patient?.ps_number && counterSelectDepartment({
    //             pYear: transaction.patient.year,
    //             pMonth: transaction.patient.month,
    //             number: transaction.patient.number
    //         }).url,
    //         active: false
    //     });

    //     bullets.push({
    //         title: `Transaction ${transaction.tr_number}`,
    //         url: printTransaction({
    //             year: transaction.year,
    //             month: transaction.month,
    //             day: transaction.day,
    //             number: transaction.number,
    //         }).url,
    //         active: true
    //     });
    // }

    const [transactionNumber, setTransactionNumber] = useState('');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Search Transaction`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-2 text-gray-800">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-col gap-4 overflow-x-auto rounded-xl bg-white p-2">
                            <div className="flex flex-1 flex-col">
                                <h2 className="text-center text-xl font-semibold">
                                    Transaction Search
                                </h2>
                                <form
                                    method="POST"
                                    className="flex flex-1 flex-col items-center justify-center gap-4"
                                >
                                    <div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="transactionNumber">
                                            Transaction Number
                                        </Label>
                                        <div className="relative">
                                            <input
                                                id="transactionNumber"
                                                type="text"
                                                value={transactionNumber}
                                                onChange={(e) =>
                                                    setTransactionNumber(
                                                        e.target.value,
                                                    )
                                                }
                                                className="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                                placeholder="Enter transaction number"
                                            />
                                        </div>
                                    </div>
                                    <Button
                                        type="submit"
                                        className="w-full max-w-sm"
                                    >
                                        View Transaction
                                    </Button>
                                </form>
                            </div>
                        </div>
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}
