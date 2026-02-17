import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import BulletsWrapper from '@/elements/bullets-wrapper';
import PatientMiniCard from '@/elements/patient/mini-card';
import HumanSimpleBody from '@/human/simple-body';
import AppLayout from '@/layouts/app-layout';
import { counterExpense, counterSelectDepartment, counterSelectPatient, counterView, home, myCounterList, patientsRegister, patientsRegisterPsNumberDepartment, printClosingStatement, printTransaction } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { LucidePrinter, LucideShoppingBasket } from 'lucide-react';
import { useMemo, useState } from 'react';

type TransactionElement = {
    id: number;
    amount: number;
    doctor_id?: number | null;
    note?: string | null;
    service?: { id: number; name: string } | null;
    service_order?: { id: number; so_number: string } | null;
};

type Transaction = {
    id: number;
    tr_number: string;
    type: 'CASH' | 'CARD' | 'INSURANCE' | 'OTHER';
    income_or_expense: 'INCOME' | 'EXPENSE';
    amount: number;
    customer_payed?: number | null;
    change?: number | null;
    elements: TransactionElement[];
    year: string;
    month: string;
    day: string;
    number: string;
};

export default function TransactionEdit() {


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

    const { props } = usePage<{ transaction?: Transaction }>();
    const transaction = props.transaction;

    const initialElements = useMemo(() => (transaction?.elements ?? []).map(e => ({
        id: e.id,
        amount: e.amount,
        doctor_id: e.doctor_id ?? null,
        note: e.note ?? '',
    })), [transaction]);

    const { data, setData, post, processing } = useForm({
        transaction_id: transaction?.id ?? 0,
        type: transaction?.type ?? 'CASH',
        amount: transaction?.amount ?? 0,
        customer_payed: transaction?.customer_payed ?? 0,
        change: transaction?.change ?? 0,
        elements: initialElements,
    });

    const onSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('transaction-update'));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit Transaction`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72] dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white text-gray-800">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white" >
                            <div className="flex-1 flex flex-col">
                                <h2 className="text-xl font-semibold text-center">Edit Transaction</h2>
                                <form onSubmit={onSubmit} className="flex flex-col gap-6 items-center justify-start flex-1">
                                    <div className="w-full max-w-3xl grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div className="space-y-2">
                                            <Label htmlFor="tr">TR Number</Label>
                                            <input id="tr" disabled value={transaction?.tr_number ?? ''} className="w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-50 text-gray-500" />
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="type">Payment Method</Label>
                                            <Select value={data.type} onValueChange={(v) => setData('type', v as any)}>
                                                <SelectTrigger id="type"><SelectValue placeholder="Select" /></SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="CASH">Cash</SelectItem>
                                                    <SelectItem value="CARD">Card</SelectItem>
                                                    <SelectItem value="INSURANCE">Insurance</SelectItem>
                                                    <SelectItem value="OTHER">Other</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="total">Total Amount</Label>
                                            <input id="total" type="number" value={data.amount as number} onChange={(e) => setData('amount', Number(e.target.value) || 0)} className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="paid">Customer Paid</Label>
                                            <input id="paid" type="number" value={data.customer_payed as number} onChange={(e) => setData('customer_payed', Number(e.target.value) || 0)} className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="change">Change</Label>
                                            <input id="change" type="number" value={data.change as number} onChange={(e) => setData('change', Number(e.target.value) || 0)} className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                    </div>

                                    <div className="w-full max-w-3xl">
                                        <div className="flex items-center justify-between mb-2">
                                            <h3 className="text-lg font-semibold">Elements</h3>
                                            <span className="text-sm text-gray-500">{data.elements.length} item(s)</span>
                                        </div>
                                        <div className="space-y-2">
                                            {data.elements.map((el, idx) => (
                                                <div key={el.id} className="rounded-lg border border-gray-200 p-3 flex flex-col gap-2">
                                                    <div className="flex items-center justify-between">
                                                        <div className="text-sm font-medium">#{el.id} {transaction?.elements[idx]?.service?.name ?? 'Element'}</div>
                                                        <div className="text-xs text-gray-500">SO: {transaction?.elements[idx]?.service_order?.so_number ?? '-'}</div>
                                                    </div>
                                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                        <div className="space-y-1">
                                                            <Label htmlFor={`el-amount-${el.id}`}>Amount</Label>
                                                            <input id={`el-amount-${el.id}`} type="number" value={el.amount as number} onChange={(e) => {
                                                                const val = Number(e.target.value) || 0;
                                                                setData('elements', data.elements.map((it, i) => i === idx ? { ...it, amount: val } : it));
                                                            }} className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                                        </div>
                                                        <div className="space-y-1">
                                                            <Label htmlFor={`el-doctor-${el.id}`}>Doctor ID</Label>
                                                            <input id={`el-doctor-${el.id}`} type="number" value={el.doctor_id ?? ''} onChange={(e) => {
                                                                const val = e.target.value ? Number(e.target.value) : null;
                                                                setData('elements', data.elements.map((it, i) => i === idx ? { ...it, doctor_id: val } : it));
                                                            }} className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                                        </div>
                                                        <div className="space-y-1 md:col-span-1">
                                                            <Label htmlFor={`el-note-${el.id}`}>Note</Label>
                                                            <input id={`el-note-${el.id}`} type="text" value={el.note ?? ''} onChange={(e) => {
                                                                setData('elements', data.elements.map((it, i) => i === idx ? { ...it, note: e.target.value } : it));
                                                            }} className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>

                                    <Button type="submit" disabled={processing} className="w-full max-w-3xl">
                                        {processing ? 'Saving…' : 'Update Transaction'}
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
