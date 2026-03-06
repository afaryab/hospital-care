import { Button } from '@/components/ui/button';

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Label } from '@/components/ui/label';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import BulletsWrapper from '@/elements/bullets-wrapper';
import HumanSimpleBody from '@/human/simple-body';
import AppLayout from '@/layouts/app-layout';
import { counterClose, counterExpense, counterSelectPatient, counterView, home, myCounterList, patientsRegister, patientsRegisterPsNumberDepartment, printClosingStatement } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { LucideChevronDown, LucideChevronRight, LucideChevronUp, LucideLink, LucidePrinter, LucideShoppingBasket, LucideX } from 'lucide-react';
import { useState } from 'react';

function formatRelativeTime(input: string | Date): string {
    const date = typeof input === 'string' ? new Date(input) : input;
    if (!date || isNaN(date.getTime())) return '';
    const diffMs = Date.now() - date.getTime();
    const rtf = new Intl.RelativeTimeFormat(undefined, { numeric: 'auto' });

    const seconds = Math.round(diffMs / 1000);
    if (seconds < 60) return rtf.format(-seconds, 'second');
    const minutes = Math.round(seconds / 60);
    if (minutes < 60) return rtf.format(-minutes, 'minute');
    const hours = Math.round(minutes / 60);
    if (hours < 24) return rtf.format(-hours, 'hour');
    const days = Math.round(hours / 24);
    if (days < 30) return rtf.format(-days, 'day');
    const months = Math.round(days / 30);
    if (months < 12) return rtf.format(-months, 'month');
    const years = Math.round(months / 12);
    return rtf.format(-years, 'year');
}

export default function CounterView() {

    const { openCounter } = usePage().props as { openCounter: any }

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Counters',
            href: openCounter ? counterView({
                ctYear: openCounter.year,
                ctMonth: openCounter.month,
                ctNumber: openCounter.number
            }).url : myCounterList().url, 
        },
    ];

    let bullets = [];

    openCounter && bullets.push({ 
        title: openCounter && openCounter.ct_number,
        url: openCounter && counterView({
            ctYear: openCounter.year,
            ctMonth: openCounter.month,
            ctNumber: openCounter.number
        }).url,
        active: true
    });

    const [printVariant , setPrintVariant] = useState<'mini' | 'normal'>('normal');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Counter ${openCounter?.ct_number} `} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72] dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white dark:bg-neutral-950 text-gray-800 dark:text-white">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-col gap-4 rounded-xl" >
                            <div className='h-full flex flex-col gap-4 lg:divide-y divide-[#06df72]'>
                                <div className='flex flex-row gap-4 lg:pr-4 pb-6'>
                                    <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white rounded-xl flex-1">
                                        <p className="text-3xl font-semibold text-gray-800">{openCounter?.ct_number}</p>
                                        <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">CT Number</p>
                                    </div>
                                    {openCounter.status =='OPEN' && (<div className='grid grid-cols-3 gap-4 items-center'>
                                        <Link href={counterSelectPatient().url} className='border shadow-lg hover:inset-shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white h-full rounded-xl text-neutral-950flex flex-row md:flex-col text-center'>
                                            <LucideChevronUp className='inline-block h-12 w-12 text-green-500' />
                                            <span className='text-xl font-bold w-full inline-block'>Income</span>
                                        </Link>
                                        <Link href={counterExpense().url} className='border shadow-lg hover:inset-shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white h-full rounded-xl text-neutral-950flex flex-row md:flex-col text-center'>
                                            <LucideChevronDown className='inline-block h-12 w-12 text-red-500' />
                                            <span className='text-xl font-bold w-full inline-block'>Expense</span>
                                        </Link>
                                        <Link href={counterClose().url} className='border shadow-lg hover:inset-shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white h-full rounded-xl text-neutral-950flex flex-row md:flex-col text-center'>
                                            <LucideX className='inline-block h-12 w-12 text-yellow-500' />
                                            <span className='text-xl font-bold w-full inline-block'>Close</span>
                                        </Link>
                                    </div>)}
                                </div>
                                <div className={clsx('grid h-full grid-cols-1 pr-4 gap-4 md:col-span-2')}>
                                    {openCounter?.status !== 'OPEN' ? <div className='flex flex-col space-y-4 h-full'>
                                        <div className='w-full flex flex-row'>
                                            <div className="grid gap-2">
                                                <Label htmlFor="print">Print Version</Label>
                                                <Select value={printVariant} onValueChange={(value) => setPrintVariant(value)}>
                                                    <SelectTrigger id="print">
                                                        <SelectValue placeholder="Select Print Version" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem key={'normal'} value={'normal'}>Full page</SelectItem>
                                                        <SelectItem key={'mini'} value={'mini'}>Mini page</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                {/* <InputError message={errors.email} /> */}
                                            </div>
                                        </div>
                                        <iframe
                                            title="Closing Statement"
                                            src={printClosingStatement({
                                                year: openCounter.year,
                                                month: openCounter.month,
                                                number: openCounter.number,
                                            }).url + `?variant=${printVariant}`}
                                            className='w-full min-h-[300px] h-full border'
                                        ></iframe>
                                    </div> : <div className='my-4 w-full'>
                                        <table className='text-xs text-gray-700 uppercase bg-white dark:bg-neutral-950 dark:text-gray-400 text-left w-full'>
                                            <thead className='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400'>
                                                <tr className='border-b-2'>
                                                    <th className='sticky top-0 px-4 py-2'>Transaction</th>
                                                    <th className='sticky top-0 px-4 py-2'>Patient</th>
                                                    <th className='sticky top-0 px-4 py-2'>Drives</th>
                                                    <th className='sticky top-0 px-4 py-2 text-right'>Amount</th>
                                                    <th className='sticky top-0 px-4 py-2'>Date</th>
                                                    <th className='sticky top-0 px-4 py-2'>...</th>
                                                </tr>
                                            </thead>
                                            {openCounter?.transactions.length > 0 && <tbody>
                                                {openCounter?.transactions?.map((transaction:any, index:number) => (
                                                    <tr key={index} className='border-b dark:border-gray-600 h-12'>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-l border-dotted border-gray-300'>
                                                            <div className='text-blue-600'>
                                                                <div className='inline text-gray-500 mb-1'>
                                                                    {transaction.income_or_expense == 'INCOME' ? (
                                                                        <LucideChevronUp className='inline mr-1 h-3 w-3 text-green-500' />
                                                                    ) : (
                                                                        <LucideChevronDown className='inline mr-1 h-3 w-3 text-red-500' />
                                                                    )}
                                                                    {transaction.tr_number}
                                                                </div>
                                                                <div className='flex flex-row'>
                                                                    {transaction.income_or_expense == 'INCOME' && (
                                                                        <>
                                                                            {transaction.elements.map((element:any, idx:number) => (<>
                                                                                {element.service && <span key={idx} className='text-green-600 text-xs bg-green-100 px-1 rounded-l border border-green-300'>
                                                                                    {element?.service?.name}
                                                                                </span>}
                                                                                {element.service_recestation && <span key={idx} className='text-lime-600 text-xs bg-lime-100 px-1 rounded border border-lime-300'>
                                                                                    {element?.service_recestation?.name}
                                                                                </span>}
                                                                                </>
                                                                            ))}
                                                                        </>
                                                                    )}
                                                                    {transaction.income_or_expense == 'EXPENSE' && (
                                                                        <>
                                                                            {transaction.elements.map((element:any, idx:number) => (<>
                                                                                    {element.exp_voucher && <span key={idx} className='text-orange-600 text-xs bg-orange-100 px-1 rounded-l border border-orange-300'>
                                                                                        {element.exp_voucher.type}: {element.exp_voucher.vc_number}
                                                                                    </span>}
                                                                                    {element.expense && <span className='text-red-600 text-xs bg-red-100 px-1 rounded-l border border-red-300'>
                                                                                        {element.expense.type}: {element.expense.id}
                                                                                    </span>}
                                                                                </>
                                                                            ))}
                                                                        </>
                                                                    )}
                                                                    <Link href={'/CT-'+transaction.tr_number} target='_blank' className='text-gray-600 text-xs bg-gray-100 px-1 rounded-r border border-gray-300' >
                                                                        <LucideLink className='inline h-3 w-3 text-gray-500' />
                                                                    </Link>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-300'>{
                                                            transaction.income_or_expense == 'INCOME' ? (
                                                                <div className='text-blue-600'>
                                                                    <div className='inline text-gray-500 mb-1'>
                                                                        {transaction.patient.ps_number}
                                                                    </div>
                                                                    <div className='flex flex-row'>
                                                                        {transaction?.patient?.name && <span className='text-blue-600 text-xs bg-blue-100 rounded-l px-1 border border-blue-300'>
                                                                            {transaction?.patient?.name}
                                                                        </span>}

                                                                        <Link href={'/'+transaction.patient.ps_number} target='_blank' className='text-gray-600 text-xs bg-gray-100 px-1 rounded-r border border-gray-300' >
                                                                            <LucideLink className='inline h-3 w-3 text-gray-500' />
                                                                        </Link>
                                                                    </div>
                                                                </div>
                                                            ) : (
                                                                transaction.income_or_expense
                                                            )
                                                        }</td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-300'>
                                                            <div className='flex flex-row'>
                                                            {transaction.elements && transaction.elements.length > 0 ? (
                                                                <>
                                                                    {transaction.elements.map((element:any, idx:number) => (<div key={idx} className='flex'>
                                                                        {element?.service_order && <span className='text-gray-600 text-xs bg-gray-100 px-1 rounded-l border border-gray-300'>
                                                                            {element.service?.name}
                                                                        </span>}
                                                                        {element?.service_order && <span className='text-indigo-600 text-xs bg-indigo-100 px-1 border border-indigo-300'>
                                                                            {element?.service_order?.so_number}
                                                                        </span>}
                                                                        {element?.service_order && <Link href={'/'+element?.service_order?.so_number} target='_blank' className='text-gray-600 text-xs bg-gray-100 px-1 rounded-r border border-gray-300' >
                                                                            <LucideLink className='inline h-3 w-3 text-gray-500' />
                                                                        </Link>}
                                                                    </div>))}
                                                                </>
                                                            ) : (
                                                                <></>
                                                            )}
                                                            {transaction.receaveable && (<>
                                                                    <span className='text-purple-600 text-xs bg-purple-100 px-1 rounded-l border border-purple-300'>
                                                                        Rceaveable: {transaction.receaveable.id}
                                                                    </span>
                                                                    <Link href={'/'+transaction.receaveable.id} target='_blank' className='text-gray-600 text-xs bg-gray-100 px-1 rounded-r border border-gray-300' >
                                                                        <LucideLink className='inline h-3 w-3 text-gray-500' />
                                                                    </Link>
                                                                </>
                                                            )}
                                                            </div>
                                                        </td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-300'>
                                                            <div className='flex flex-col gap-1'>
                                                                <div className='flex flex-row justify-end'>
                                                                    <span className='text-slate-600 text-xs bg-slate-100 px-1 rounded-l border border-slate-300'>
                                                                        {transaction.amount} PKR
                                                                    </span>
                                                                    <span className='text-slate-600 text-xs bg-slate-100 px-1 rounded-r border border-slate-300'>
                                                                        {transaction.type}
                                                                    </span>
                                                                </div>
                                                                
                                                            </div>
                                                        </td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-300'>
                                                            <span title={new Date(transaction.created_at).toLocaleString()}>
                                                                {formatRelativeTime(transaction.created_at)}
                                                            </span>
                                                        </td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-300'>
                                                            <DropdownMenu>
                                                                    <DropdownMenuTrigger asChild>
                                                                        <Button
                                                                            variant="ghost"
                                                                            size="icon"
                                                                            className="h-9 w-9 rounded-md"
                                                                        >
                                                                            ...
                                                                        </Button>
                                                                    </DropdownMenuTrigger>
                                                                    <DropdownMenuContent align="end">
                                                                        <div className='flex flex-col'>
                                                                            {transaction?.is_refunded == 0 && <Link href={'/CT-'+transaction.tr_number} target='_blank' >
                                                                                Print
                                                                            </Link>}
                                                                            {transaction?.is_refunded == 0 && <Link href={'/CT-'+transaction.tr_number+'/edit'} target='_blank' >
                                                                                Edit
                                                                            </Link>}
                                                                            {(transaction?.patient?.name && transaction?.is_refunded == 0) && <Link href={'/CT-EXP?type=petty&category=Refund&amount='+transaction.amount+'&transaction_number='+transaction.tr_number+'&payed_to_other='+transaction.patient.name} target='_blank' >
                                                                                Refund
                                                                            </Link>}
                                                                        </div>
                                                                    </DropdownMenuContent>
                                                                </DropdownMenu>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>}
                                            {openCounter?.transactions.length > 0 && openCounter?.transactions.length < 10 && <tbody>
                                                {Array.from({ length: Math.ceil((10 - openCounter.transactions.length)/3) }).map((_, index) => (
                                                    <tr key={`empty-${index}`} className='border-b border-dotted dark:border-gray-100 h-12 bg-gray-200/50'>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-l border-dotted border-gray-100'></td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-100'></td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-100'></td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-100'></td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-100'></td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white border-r border-dotted border-gray-100'></td>
                                                    </tr>
                                                ))}
                                            </tbody>}
                                            {openCounter?.transactions.length == 0 && <tbody>
                                                <tr className='border-b border-dotted dark:border-gray-600 bg-gray-100/50'>
                                                    <td colSpan={4} className='px-4 py-2 font-medium text-gray-900 dark:text-white'>No transactions found.</td>
                                                </tr>
                                            </tbody>}
                                            {openCounter?.transactions.length > 0 && <tfoot>
                                                <tr className='border-t-2 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700'>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={3}></th>
                                                    <th className='text-right'>{openCounter?.transactions.filter((transaction:any) => transaction.income_or_expense === 'INCOME' && (transaction.type === 'CASH' || transaction.type === 'Cash' || transaction.type === 'cash')).reduce((total:number, transaction:any) => total + Number(transaction.amount), 0)} PKR</th>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={2}>Cash</th>
                                                </tr>
                                                <tr className='border-t-2 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700'>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={3}></th>
                                                    <th className='text-right'>{openCounter?.transactions.filter((transaction:any) => transaction.income_or_expense === 'INCOME' && (transaction.type === 'CHEQUE' || transaction.type === 'Cheque' || transaction.type === 'cheque')).reduce((total:number, transaction:any) => total + Number(transaction.amount), 0)} PKR</th>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={2}>Cheque</th>
                                                </tr>
                                                <tr className='border-t-2 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700'>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={3}></th>
                                                    <th className='text-right'>{openCounter?.transactions.filter((transaction:any) => transaction.income_or_expense === 'INCOME' && (transaction.type === 'BANK_TRANSFER' || transaction.type === 'Bank_Transfer' || transaction.type === 'bank_transfer')).reduce((total:number, transaction:any) => total + Number(transaction.amount), 0)} PKR</th>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={2}>Bank Transfer</th>
                                                </tr>
                                                <tr className='border-t-2 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700'>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={3}></th>
                                                    <th className='text-right'>{openCounter?.transactions.filter((transaction:any) => transaction.income_or_expense === 'INCOME' && (transaction.type === 'CARD' || transaction.type === 'Card' || transaction.type === 'card')).reduce((total:number, transaction:any) => total + Number(transaction.amount), 0)} PKR</th>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={2}>Card</th>
                                                </tr>
                                                <tr className='border-t-2 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700'>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={3}></th>
                                                    <th className='text-right'>{openCounter?.transactions.filter((transaction:any) => transaction.income_or_expense === 'EXPENSE').reduce((total:number, transaction:any) => total + Number(transaction.amount), 0)} PKR</th>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={2}>Expense Paid</th>
                                                </tr>
                                                <tr className='border-t-2 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700'>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={3}></th>
                                                    <th className='text-right'>{openCounter?.transactions.filter((transaction:any) => transaction.income_or_expense === 'VOUCHER-PAY').reduce((total:number, transaction:any) => total + Number(transaction.amount), 0)} PKR</th>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={2}>Voucher Payments</th>
                                                </tr>
                                                <tr className='border-t-2 dark:border-gray-600 bg-gray-50 dark:bg-gray-700'>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={3}></th>
                                                    <th className='text-right'>{openCounter?.transactions.reduce((total:number, transaction:any) => (transaction.income_or_expense === 'EXPENSE' || transaction.income_or_expense === 'VOUCHER-PAY') ? total - Number(transaction.amount) : total + Number(transaction.amount), 0)} PKR</th>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={2}>Total</th>
                                                </tr>
                                                <tr className='border-t-2 dark:border-gray-600 bg-gray-50 dark:bg-gray-700'>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={3}></th>
                                                    <th className='text-right'>{openCounter?.transactions.filter((transaction:any) => transaction.receaveable && transaction.receaveable.length > 0).reduce((total:number, transaction:any) => total + Number(transaction.receaveable.id), 0)} PKR</th>
                                                    <th className='px-4 py-2 text-gray-900 text-md dark:text-white' colSpan={2}>Reveaveables</th>
                                                </tr>
                                            </tfoot>}
                                        </table>
                                    </div>}


                                </div>
                            </div>
                        </div>
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}
