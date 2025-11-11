import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import BulletsWrapper from '@/elements/bullets-wrapper';
import HumanSimpleBody from '@/human/simple-body';
import AppLayout from '@/layouts/app-layout';
import { counterList, counterSelectPatient, counterView, home, patientsRegister, patientsRegisterPsNumberDepartment, printClosingStatement } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { LucidePrinter, LucideShoppingBasket } from 'lucide-react';
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
            }).url : counterList().url, 
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
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white text-gray-800">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white" >
                            <div className='grid h-full grid-cols-1 md:grid-cols-3 gap-4 divide-x divide-[#06df72]'>
                                <div className='h-full flex flex-col gap-4 pr-4'>
                                    <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4">
                                        <p className="text-3xl font-semibold text-gray-800">{openCounter?.ct_number}</p>
                                        <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">CT Number</p>
                                    </div>
                                    <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4 flex flex-row">
                                        <div className='flex-1 flex flex-col'>
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.status}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Status</p>
                                        </div>
                                        {openCounter.status =='OPEN' && (
                                            <Link href={counterSelectPatient().url}>
                                                <Button variant='default' className='w-full h-full text-right align-right justify-end flex flex-col'>
                                                    <LucideShoppingBasket className='mr-2 h-5 w-5' />
                                                    <span className='text-2xl font-bold'>Continue</span>
                                                </Button>
                                            </Link>
                                        )}
                                    </div>
                                    <div className='grid grid-cols-2 gap-4'>
                                        <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.opening_amount}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Opening</p>
                                        </div>
                                        <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.expense_payed}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Closing</p>
                                        </div>
                                        <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.closing_amount_cash}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Cash In hand</p>
                                        </div>
                                        <div className="border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.closing_amount_cheque}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Cheques</p>
                                        </div>
                                        <div className="col-span-2 border shadow-lg xl:p-6 p-4 sm:w-auto w-full bg-white mt-4">
                                            <p className="text-3xl font-semibold text-gray-800">{openCounter?.closing_amount_card}</p>
                                            <p className="text-base leading-4 xl:mt-4 mt-2 text-gray-600">Card</p>
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
                                    </div> : <div className='flex-1 border shadow-lg h-full grid grid-cols-1 overflow-y-auto max-h-96 my-4 '>
                                        <table className='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 text-left'>
                                            <thead className='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400'>
                                                <tr className='border-b-2'>
                                                    <th className='px-4 py-2'>Type</th>
                                                    <th className='px-4 py-2'>PS Number</th>
                                                    <th className='px-4 py-2'>Amount</th>
                                                    <th className='px-4 py-2'>Date</th>
                                                </tr>
                                            </thead>
                                            {openCounter?.transactions.length > 0 && <tbody>
                                                {openCounter?.transactions?.map((transaction:any, index:number) => (
                                                    <tr key={index} className='border-b dark:border-gray-600'>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>{transaction.type}</td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>{transaction.patient.ps_number}</td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>{transaction.amount}</td>
                                                        <td className='px-4 py-2 font-medium text-gray-900 dark:text-white'>{transaction.created_at}</td>
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
