import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import BulletsWrapper from '@/elements/bullets-wrapper';
import PatientMiniCard from '@/elements/patient/mini-card';
import HumanSimpleBody from '@/human/simple-body';
import AppLayout from '@/layouts/app-layout';
import { counterExpense, counterList, counterSelectDepartment, counterSelectPatient, counterView, home, myCounterList, patientsRegister, patientsRegisterPsNumberDepartment, printClosingStatement, printTransaction } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { LucidePrinter, LucideShoppingBasket } from 'lucide-react';
import { useState } from 'react';

export default function TransactionSearch() {


    let breadcrumbs: BreadcrumbItem[] = [
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

    let bullets = [];

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
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72] dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white text-gray-800">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white" >
                            <div className="flex-1 flex flex-col">
                                <h2 className="text-xl font-semibold text-center">Transaction Search</h2>
                                <form method='POST' className="flex flex-col gap-4 items-center justify-center flex-1">
                                    <div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="transactionNumber">Transaction Number</Label>
                                        <div className="relative">
                                            <input
                                                id="transactionNumber"
                                                type="text"
                                                value={transactionNumber}
                                                onChange={(e) => setTransactionNumber(e.target.value)}
                                                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
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
