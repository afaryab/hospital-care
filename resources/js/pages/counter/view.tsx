import { Button } from '@/components/ui/button';
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
import { LucideChevronDown, LucideChevronRight, LucideChevronUp, LucidePrinter, LucideShoppingBasket, LucideX } from 'lucide-react';
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

    const { openCounter } = usePage().props

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
                        <div className="flex h-full w-full flex-col gap-4 overflow-x-auto rounded-xl" >
                            <div className='grid h-full grid-cols-1 lg:grid-cols-3 gap-4 lg:divide-x divide-[#06df72]'>
                                <div className='h-full flex flex-col gap-4 lg:pr-4'>
                                    <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4 rounded-xl">
                                        <p className="text-3xl font-semibold text-gray-800">{openCounter?.ct_number}</p>
                                        <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">CT Number</p>
                                    </div>
                                    {openCounter.status =='OPEN' && (<div className='grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-4 items-center'>
                                        <Link href={counterSelectPatient().url} className='border shadow-lg hover:inset-shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white h-full rounded-xl text-neutral-950 mt-4 flex flex-row md:flex-col'>
                                            <LucideChevronUp className='mr-2 h-12 w-12 text-green-500' />
                                            <span className='text-xl font-bold'>Income</span>
                                        </Link>
                                        <Link href={counterExpense().url} className='border shadow-lg hover:inset-shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white h-full rounded-xl text-neutral-950 mt-4 flex flex-row md:flex-col'>
                                            <LucideChevronDown className='mr-2 h-12 w-12 text-red-500' />
                                            <span className='text-xl font-bold'>Expense</span>
                                        </Link>
                                        <Link href={counterClose().url} className='col-span-1 md:col-span-2 2xl:col-span-1 border shadow-lg hover:inset-shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white h-full rounded-xl text-neutral-950 mt-4 flex flex-row md:flex-col'>
                                            <LucideX className='mr-2 h-12 w-12 text-yellow-500' />
                                            <span className='text-xl font-bold flex-1'>Close</span>
                                        </Link>
                                    </div>)}
                                    <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4 flex flex-row rounded-xl">
                                        <div className='flex-1 flex flex-col'>
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.status}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Status</p>
                                        </div>
                                    </div>
                                    <div className='grid grid-cols-2 gap-4'>
                                        <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4 rounded-xl">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.opening_amount}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Opening</p>
                                        </div>
                                        <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4 rounded-xl">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.closing_amount}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Closing</p>
                                        </div>
                                        {/* <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4 rounded-xl">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.closing_amount_cash}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Cash In hand</p>
                                        </div>
                                        <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4 rounded-xl">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.closing_amount_cheque}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Cheques</p>
                                        </div> */}
                                        <div className="col-span-2 border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4 rounded-xl">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.expense_payed}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Expense Payed</p>
                                        </div>
                                    </div>
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
                                    </div> : <div className='overflow-y-auto min-h-96 max-h-[700px] my-4 w-full'>
                                        <table className='text-xs text-gray-700 uppercase bg-white dark:bg-neutral-950 dark:text-gray-400 text-left w-full'>
                                            <thead className='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400'>
                                                <tr className='border-b-2'>
                                                    <th className='sticky top-0 px-4 py-2'>Transaction</th>
                                                    <th className='sticky top-0 px-4 py-2'>Patient</th>
                                                    <th className='sticky top-0 px-4 py-2'>Drives</th>
                                                    <th className='sticky top-0 px-4 py-2'>Amount</th>
                                                    <th className='sticky top-0 px-4 py-2'>Date</th>
                                                    <th className='sticky top-0 px-4 py-2'>Date</th>
                                                </tr>
                                            </thead>
                                            {openCounter?.transactions.length > 0 && <tbody>
                                                {openCounter?.transactions?.map((transaction:any, index:number) => (
                                                    <tr key={index} className='border-b dark:border-gray-600 h-12'>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'><Link href={'/CT-'+transaction.tr_number} target='_blank' className='text-blue-600 hover:underline'>
                                                            {transaction.income_or_expense == 'INCOME' ? (
                                                                <LucideChevronUp className='inline-block mr-1 h-6 w-6 text-green-500' />
                                                            ) : (
                                                                <LucideChevronDown className='inline-block mr-1 h-6 w-6 text-red-500' />
                                                            )}

                                                                    <div className='inline-block mr-1 h-6 w-6 text-gray-500'>
                                                                        {transaction.tr_number}
                                                                    </div>
                                                                    <div className='flex flex-row'>
                                                                        {transaction.income_or_expense == 'INCOME' && (
                                                                            <>
                                                                                {transaction.elements.map((element:any, idx:number) => (<>
                                                                                    {element.service && <span key={idx} className='text-blue-600 hover:underline ml-2 text-xs bg-blue-100 px-1 rounded border border-blue-300'>
                                                                                        {element?.service?.name}
                                                                                    </span>}
                                                                                    {element.service_recestation && <span key={idx} className='text-yellow-600 hover:underline ml-2 text-xs bg-yellow-100 px-1 rounded border border-yellow-300'>
                                                                                        {element?.service_recestation?.name}
                                                                                    </span>}
                                                                                    </>
                                                                                ))}
                                                                            </>
                                                                        )}
                                                                        {transaction.income_or_expense == 'EXPENSE' && (
                                                                            <>
                                                                                {transaction.elements.map((element:any, idx:number) => (<>
                                                                                        {element.exp_voucher && <span key={idx} className='text-blue-600 hover:underline ml-2 text-xs bg-blue-100 px-1 rounded border border-blue-300'>
                                                                                            {element.exp_voucher.type}: {element.exp_voucher.vc_number}
                                                                                        </span>}
                                                                                        {element.expense && <span className='inline-block mr-1 h-6 w-6 text-gray-500'>
                                                                                            {element.expense.type}: {element.expense.id}
                                                                                        </span>}
                                                                                    </>
                                                                                ))}
                                                                            </>
                                                                        )}
                                                                    </div>
                                                                </Link></td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>{
                                                            transaction.income_or_expense == 'INCOME' ? (
                                                                <Link href={'/'+transaction.patient.ps_number} target='__blank' className='text-blue-600 hover:underline'>
                                                                    <div className='inline-block mr-1 h-6 w-6 text-gray-500'>
                                                                        {transaction.patient.ps_number}
                                                                    </div>
                                                                    <div className='flex flex-row'>
                                                                        {transaction.patient.name && <span className='text-blue-600 hover:underline text-xs bg-blue-100 px-1 rounded-l border border-blue-300'>
                                                                            {transaction.patient.name}
                                                                        </span>}
                                                                        {transaction.patient.contact && <span className='text-gray-600 hover:underline text-xs bg-gray-100 px-1 border border-gray-300'>
                                                                            {transaction.patient.contact}
                                                                        </span>}
                                                                        {transaction.patient.age && <span className='text-green-600 hover:underline text-xs bg-green-100 px-1 border border-green-300'>
                                                                            {transaction.patient.age} yrs
                                                                        </span>}
                                                                        {transaction.patient.gender && <span className='text-pink-600 hover:underline text-xs bg-pink-100 px-1 rounded-r border border-pink-300'>
                                                                            {transaction.patient.gender}
                                                                        </span>}
                                                                    </div>
                                                                </Link>
                                                            ) : (
                                                                transaction.income_or_expense
                                                            )
                                                        }</td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>
                                                            {transaction.elements && transaction.elements.length > 0 ? (
                                                                <div className='flex flex-row'>
                                                                    {transaction.elements.map((element:any, idx:number) => (<>
                                                                        {element.service_order && <span className='text-indigo-600 hover:underline text-xs bg-blue-100 px-1 rounded-l border border-blue-300'>
                                                                            {element.service_order.type}: {element.service_order.so_number}
                                                                        </span>}
                                                                    </>))}
                                                                </div>
                                                            ) : (
                                                                <></>
                                                            )}
                                                        </td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>
                                                            <div className='flex flex-row gap-1'>
                                                                <span className='text-slate-600 hover:underline ml-2 text-xs bg-slate-100 px-1 rounded border border-slate-300'>
                                                                    {transaction.amount} PKR
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>
                                                            <span title={new Date(transaction.created_at).toLocaleString()}>
                                                                {formatRelativeTime(transaction.created_at)}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>}
                                            {openCounter?.transactions.length == 0 && <tbody>
                                                <tr className='border-b dark:border-gray-600'>
                                                    <td colSpan={4} className='px-4 py-2 font-medium text-gray-900 dark:text-white'>No transactions found.</td>
                                                </tr>
                                            </tbody>}
                                            {openCounter?.transactions.length > 0 && <tfoot>
                                                <tr className='border-t-2 dark:border-gray-600'>
                                                    <th className='px-4 py-2 text-gray-900 dark:text-white text-left' colSpan={3}>Total</th>
                                                    <th className='px-4 py-2 text-gray-900 dark:text-white text-left'>{openCounter?.transactions.reduce((total:number, transaction:any) => total + Number(transaction.amount), 0)} PKR</th>
                                                    <th className='px-4 py-2' colSpan={3}></th>
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
