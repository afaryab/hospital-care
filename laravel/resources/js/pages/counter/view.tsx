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
                                    </div> : <div className='flex-1 border shadow-lg h-full grid grid-cols-1 overflow-y-auto min-h-96 max-h-[700px] my-4 '>
                                        <table className='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-neutral-950 dark:text-gray-400 text-left'>
                                            <thead className='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400'>
                                                <tr className='border-b-2'>
                                                    <th className='px-4 py-2'>Transaction</th>
                                                    <th className='px-4 py-2'>Patient</th>
                                                    <th className='px-4 py-2'>Drives</th>
                                                    <th className='px-4 py-2'>Amount</th>
                                                    <th className='px-4 py-2'>Date</th>
                                                </tr>
                                            </thead>
                                            {openCounter?.transactions.length > 0 && <tbody>
                                                {openCounter?.transactions?.map((transaction:any, index:number) => (
                                                    <tr key={index} className='border-b dark:border-gray-600'>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'><Link href={'/CT-'+transaction.tr_number} target='_blank' className='text-blue-600 hover:underline'>
                                                            {transaction.income_or_expense == 'INCOME' ? (
                                                                <LucideChevronUp className='inline-block mr-1 h-4 w-4 text-green-500' />
                                                            ) : (
                                                                <LucideChevronDown className='inline-block mr-1 h-4 w-4 text-red-500' />
                                                            )}

                                                                    {transaction.tr_number}
                                                                </Link></td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>{
                                                            transaction.income_or_expense == 'INCOME' ? (
                                                                <Link href={'/'+transaction.patient.ps_number} target='__blank' className='text-blue-600 hover:underline'>
                                                                    {transaction.patient.ps_number}
                                                                </Link>
                                                            ) : (
                                                                transaction.income_or_expense
                                                            )
                                                        }</td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>
                                                            {transaction.elements && transaction.elements.length > 0 ? (
                                                                <ul className='list-none list-inside'>
                                                                    {transaction.elements.map((element:any, idx:number) => (
                                                                        element.service_order && <li key={idx}>
                                                                            <Link href={'/'+element.service_order.so_number} target='__blank' className='text-blue-600 hover:underline'>
                                                                                {element?.service_order?.so_number}
                                                                            </Link>
                                                                        </li>
                                                                    ))}
                                                                    {transaction?.elements.map((element:any, idx:number) => (
                                                                        element.exp_voucher && <li key={idx}>
                                                                            <Link href={'/'+element.exp_voucher.vc_number} target='__blank' className='text-blue-600 hover:underline'>
                                                                                {element?.exp_voucher?.vc_number}
                                                                            </Link>
                                                                        </li>
                                                                    ))}
                                                                    {/* {transaction?.elements.map((element:any, idx:number) => (
                                                                        <li key={idx}>{element?.expense?.id}</li>
                                                                    ))} */}
                                                                </ul>
                                                            ) : (
                                                                ""
                                                            )}
                                                        </td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>{transaction.amount}</td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>{new Date(transaction.created_at).toLocaleString()}</td>
                                                    </tr>
                                                ))}
                                            </tbody>}
                                            {openCounter?.transactions.length == 0 && <tbody>
                                                <tr className='border-b dark:border-gray-600'>
                                                    <td colSpan={4} className='px-4 py-2 font-medium text-gray-900 dark:text-white'>No transactions found.</td>
                                                </tr>
                                            </tbody>}
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
