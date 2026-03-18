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

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Counter ${openCounter?.ct_number} `} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72] dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white dark:bg-neutral-950 text-gray-800 dark:text-white">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-col gap-4 rounded-xl" >
                            <div className='h-full flex flex-col lg:divide-y divide-[#06df72]'>
                                <div className='flex flex-row gap-4 lg:pr-4 pb-4'>
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
                                <CounterViewTabs openCounter={openCounter} />
                            </div>
                        </div>
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}


type CounterTab = 'general' | 'print' | 'income-report' | 'expense-report' | 'receivables-report' | 'services-report';

const tabConfig: { key: CounterTab; label: string; color: string }[] = [
    { key: 'general', label: 'General', color: 'green' },
    { key: 'print', label: 'Print', color: 'blue' },
    { key: 'income-report', label: 'Income Report', color: 'emerald' },
    { key: 'expense-report', label: 'Expense Report', color: 'red' },
    { key: 'receivables-report', label: 'Receivables Report', color: 'purple' },
    { key: 'services-report', label: 'Services Report', color: 'indigo' },
];

const CounterViewTabs = ({openCounter}: {openCounter: any}) => {
    
    const [activeTab, setActiveTab] = useState<CounterTab>('general');

    const closingUrl = printClosingStatement({
        year: openCounter.year,
        month: openCounter.month,
        number: openCounter.number,
    }).url;

    return (
        <>
        <div className='flex flex-row gap-2 border-b divide-y-0 divide-gray-300 overflow-x-auto'>
            {tabConfig.map(tab => (
                <button key={tab.key} onClick={() => setActiveTab(tab.key)} className={clsx(
                    'text-sm py-2 px-3 font-medium whitespace-nowrap',
                    activeTab === tab.key
                        ? `border-b-2 border-${tab.color}-500 text-${tab.color}-600`
                        : 'text-gray-500 hover:text-gray-700'
                )}>
                    {tab.label}
                </button>
            ))}
        </div>
        <div>
            {activeTab === 'general' && <CounterTransactionsOverview openCounter={openCounter} />}
            {activeTab === 'print' && <CounterReportIframe title='Closing Statement' src={closingUrl} showVariant />}
            {activeTab === 'income-report' && <CounterReportIframe title='Income Report' src={closingUrl + '?report=income'} />}
            {activeTab === 'expense-report' && <CounterReportIframe title='Expense Report' src={closingUrl + '?report=expense'} />}
            {activeTab === 'receivables-report' && <CounterReportIframe title='Receivables Report' src={closingUrl + '?report=receivables'} />}
            {activeTab === 'services-report' && <CounterReportIframe title='Services Report' src={closingUrl + '?report=services'} />}
        </div>

        </>
    );
}

const CounterReportIframe = ({title, src, showVariant}: {title: string; src: string; showVariant?: boolean}) => {
    const [printVariant, setPrintVariant] = useState<'mini' | 'normal'>('normal');
    const iframeSrc = showVariant ? src + `?variant=${printVariant}` : src;

    return (
        <div className='flex flex-col space-y-4 h-full my-4'>
            {showVariant && (
                <div className='w-full flex flex-row'>
                    <div className='grid gap-2'>
                        <Label htmlFor='print'>Print Version</Label>
                        <Select value={printVariant} onValueChange={(value: string) => setPrintVariant(value as 'mini' | 'normal')}>
                            <SelectTrigger id='print'>
                                <SelectValue placeholder='Select Print Version' />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem key='normal' value='normal'>Full page</SelectItem>
                                <SelectItem key='mini' value='mini'>Mini page</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            )}
            <iframe
                title={title}
                src={iframeSrc}
                className='w-full min-h-[500px] h-full border rounded'
            />
        </div>
    );
}

const CounterTransactionsOverview = ({openCounter}: {openCounter: any}) => {

    const typeNorm = (t: string) => (t ?? '').toUpperCase();

    const sumByFilter = (filter: (tr: any) => boolean) =>
        openCounter?.transactions?.filter(filter).reduce((total: number, tr: any) => total + Number(tr.amount), 0) ?? 0;

    const incomeByType = (type: string) =>
        sumByFilter((tr: any) => tr.income_or_expense === 'INCOME' && typeNorm(tr.type) === type);
    
    return (<div className={clsx('grid h-full grid-cols-1 pr-4 gap-4 md:col-span-2')}>
                <div className='my-4 w-full overflow-x-auto'>
                    <table className='text-xs text-gray-700 bg-white dark:bg-neutral-950 dark:text-gray-400 text-left w-full border-collapse'>
                        <thead className='text-[11px] text-gray-500 uppercase tracking-wider bg-gray-50 dark:bg-gray-700 dark:text-gray-400'>
                            <tr>
                                <th className='sticky top-0 px-3 py-2.5 font-semibold w-8'>#</th>
                                <th className='sticky top-0 px-3 py-2.5 font-semibold'>Transaction</th>
                                <th className='sticky top-0 px-3 py-2.5 font-semibold'>Type</th>
                                <th className='sticky top-0 px-3 py-2.5 font-semibold'>Patient / Paid To</th>
                                <th className='sticky top-0 px-3 py-2.5 font-semibold'>Details</th>
                                <th className='sticky top-0 px-3 py-2.5 font-semibold text-right'>Amount</th>
                                <th className='sticky top-0 px-3 py-2.5 font-semibold'>Method</th>
                                <th className='sticky top-0 px-3 py-2.5 font-semibold'>Date</th>
                                <th className='sticky top-0 px-3 py-2.5 font-semibold w-10'></th>
                            </tr>
                        </thead>
                        {openCounter?.transactions.length > 0 && <tbody>
                            {openCounter?.transactions?.map((transaction:any, index:number) => {
                                const isIncome = transaction.income_or_expense === 'INCOME';
                                const isExpense = transaction.income_or_expense === 'EXPENSE';
                                const isVoucherPay = transaction.income_or_expense === 'VOUCHER-PAY';
                                const isRefunded = transaction.is_refunded == 1;

                                return (
                                <tr key={index} className={clsx(
                                    'border-b dark:border-gray-600 transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/50',
                                    isRefunded && 'opacity-50 line-through',
                                )}>
                                    {/* Row number with colored left border */}
                                    <td className={clsx(
                                        'px-3 py-2.5 text-center text-gray-400 border-l-3',
                                        isIncome && 'border-l-green-500',
                                        isExpense && 'border-l-red-500',
                                        isVoucherPay && 'border-l-orange-500',
                                    )}>
                                        {openCounter.transactions.length - index}
                                    </td>

                                    {/* Transaction number */}
                                    <td className='px-3 py-2.5'>
                                        <span className='font-mono text-blue-600 dark:text-blue-400 font-medium'>
                                            {transaction.tr_number}
                                        </span>
                                        {isRefunded && (
                                            <span className='ml-1 text-[10px] bg-red-100 text-red-600 px-1 rounded font-semibold'>REFUNDED</span>
                                        )}
                                    </td>

                                    {/* Type badge */}
                                    <td className='px-3 py-2.5'>
                                        <span className={clsx(
                                            'inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-full',
                                            isIncome && 'bg-green-100 text-green-700',
                                            isExpense && 'bg-red-100 text-red-700',
                                            isVoucherPay && 'bg-orange-100 text-orange-700',
                                        )}>
                                            {isIncome && <LucideChevronUp className='h-3 w-3' />}
                                            {(isExpense || isVoucherPay) && <LucideChevronDown className='h-3 w-3' />}
                                            {isIncome ? 'Income' : isVoucherPay ? 'Voucher' : 'Expense'}
                                        </span>
                                    </td>

                                    {/* Patient / Paid To */}
                                    <td className='px-3 py-2.5'>
                                        {isIncome && transaction.patient ? (
                                            <div className='flex flex-col gap-0.5'>
                                                <Link href={'/'+transaction.patient.ps_number} target='_blank' className='text-blue-600 hover:underline font-medium'>
                                                    {transaction.patient.name}
                                                </Link>
                                                <span className='text-[10px] text-gray-400'>{transaction.patient.ps_number}</span>
                                            </div>
                                        ) : (isExpense || isVoucherPay) ? (
                                            <div className='flex flex-col gap-0.5'>
                                                {transaction.elements?.map((el: any, idx: number) => (
                                                    el.exp_voucher?.payed_to_user?.name || el.exp_voucher?.payedTo?.name ? (
                                                        <span key={idx} className='text-gray-700 dark:text-gray-300 font-medium'>
                                                            {el.exp_voucher?.payed_to_user?.name ?? el.exp_voucher?.payedTo?.name}
                                                        </span>
                                                    ) : el.expense_category ? (
                                                        <span key={idx} className='text-gray-500 text-[10px]'>{el.expense_category.name}</span>
                                                    ) : null
                                                ))}
                                                {transaction.notes && <span className='text-[10px] text-gray-400 truncate max-w-[150px]'>{transaction.notes}</span>}
                                            </div>
                                        ) : (
                                            <span className='text-gray-400'>-</span>
                                        )}
                                    </td>

                                    {/* Details — services, SOs, vouchers, categories, doctors */}
                                    <td className='px-3 py-2.5'>
                                        <div className='flex flex-col gap-1'>
                                            {isIncome && transaction.elements?.map((el: any, idx: number) => (
                                                <div key={idx} className='flex flex-wrap items-center gap-1'>
                                                    {el.service && (
                                                        <span className='text-[10px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded border border-green-200'>
                                                            {el.service.name}
                                                        </span>
                                                    )}
                                                    {el.service_recestation && (
                                                        <span className='text-[10px] bg-lime-50 text-lime-700 px-1.5 py-0.5 rounded border border-lime-200'>
                                                            {el.service_recestation.name}
                                                        </span>
                                                    )}
                                                    {el.service_order && (
                                                        <Link href={'/'+el.service_order.so_number} target='_blank' className='text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-200 hover:bg-indigo-100'>
                                                            {el.service_order.so_number}
                                                        </Link>
                                                    )}
                                                    {el.doctor && (
                                                        <span className='text-[10px] bg-purple-50 text-purple-700 px-1.5 py-0.5 rounded border border-purple-200'>
                                                            Dr. {el.doctor.name}
                                                        </span>
                                                    )}
                                                    {el.amount && transaction.elements.length > 1 && (
                                                        <span className='text-[10px] text-gray-400'>{Number(el.amount).toLocaleString()} PKR</span>
                                                    )}
                                                </div>
                                            ))}
                                            {(isExpense || isVoucherPay) && transaction.elements?.map((el: any, idx: number) => (
                                                <div key={idx} className='flex flex-wrap items-center gap-1'>
                                                    {el.expense_category && (
                                                        <span className='text-[10px] bg-red-50 text-red-700 px-1.5 py-0.5 rounded border border-red-200'>
                                                            {el.expense_category.name}
                                                        </span>
                                                    )}
                                                    {el.exp_voucher && (
                                                        <span className='text-[10px] bg-orange-50 text-orange-700 px-1.5 py-0.5 rounded border border-orange-200'>
                                                            {el.exp_voucher.vc_number}
                                                        </span>
                                                    )}
                                                    {el.service_order && (
                                                        <Link href={'/'+el.service_order.so_number} target='_blank' className='text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-200 hover:bg-indigo-100'>
                                                            {el.service_order.so_number}
                                                        </Link>
                                                    )}
                                                    {el.notes && (
                                                        <span className='text-[10px] text-gray-400 italic truncate max-w-[120px]' title={el.notes}>{el.notes}</span>
                                                    )}
                                                </div>
                                            ))}
                                            {transaction.receaveable && (
                                                <div className='flex flex-wrap items-center gap-1'>
                                                    <span className='text-[10px] bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded border border-purple-200'>
                                                        Receivable
                                                    </span>
                                                    {transaction.receaveable.panel && (
                                                        <span className='text-[10px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded border border-blue-200'>
                                                            {transaction.receaveable.panel.name}
                                                        </span>
                                                    )}
                                                    {transaction.receaveable.patient && (
                                                        <span className='text-[10px] bg-gray-50 text-gray-600 px-1.5 py-0.5 rounded border border-gray-200'>
                                                            {transaction.receaveable.patient.name}
                                                        </span>
                                                    )}
                                                    <span className={clsx(
                                                        'text-[10px] px-1.5 py-0.5 rounded font-semibold',
                                                        transaction.receaveable.status === 'PAID'
                                                            ? 'bg-green-50 text-green-700 border border-green-200'
                                                            : 'bg-amber-50 text-amber-700 border border-amber-200',
                                                    )}>
                                                        {transaction.receaveable.status ?? 'PENDING'}
                                                    </span>
                                                    {transaction.receaveable.due_date && (
                                                        <span className='text-[10px] text-gray-400'>
                                                            Due: {new Date(transaction.receaveable.due_date).toLocaleDateString()}
                                                        </span>
                                                    )}
                                                    <span className='text-[10px] font-mono text-purple-600'>
                                                        {Number(transaction.receaveable.amount).toLocaleString()} PKR
                                                    </span>
                                                </div>
                                            )}
                                        </div>
                                    </td>

                                    {/* Amount */}
                                    <td className='px-3 py-2.5 text-right font-mono'>
                                        <span className={clsx(
                                            'font-semibold',
                                            isIncome && 'text-green-700 dark:text-green-400',
                                            (isExpense || isVoucherPay) && 'text-red-600 dark:text-red-400',
                                        )}>
                                            {isIncome ? '+' : '-'}{Number(transaction.amount).toLocaleString()}
                                        </span>
                                        <span className='text-[10px] text-gray-400 ml-0.5'>PKR</span>
                                    </td>

                                    {/* Payment method */}
                                    <td className='px-3 py-2.5'>
                                        <span className={clsx(
                                            'text-[10px] font-medium px-1.5 py-0.5 rounded',
                                            typeNorm(transaction.type) === 'CASH' && 'bg-emerald-100 text-emerald-700',
                                            typeNorm(transaction.type) === 'CARD' && 'bg-sky-100 text-sky-700',
                                            typeNorm(transaction.type) === 'CHEQUE' && 'bg-amber-100 text-amber-700',
                                            typeNorm(transaction.type) === 'BANK_TRANSFER' && 'bg-violet-100 text-violet-700',
                                            !['CASH','CARD','CHEQUE','BANK_TRANSFER'].includes(typeNorm(transaction.type)) && 'bg-gray-100 text-gray-600',
                                        )}>
                                            {typeNorm(transaction.type) === 'BANK_TRANSFER' ? 'Bank' : (transaction.type ?? '-')}
                                        </span>
                                    </td>

                                    {/* Date */}
                                    <td className='px-3 py-2.5 text-gray-500'>
                                        <span title={new Date(transaction.created_at).toLocaleString()}>
                                            {formatRelativeTime(transaction.created_at)}
                                        </span>
                                    </td>

                                    {/* Actions */}
                                    <td className='px-3 py-2.5'>
                                        <DropdownMenu>
                                            <DropdownMenuTrigger asChild>
                                                <Button variant="ghost" size="icon" className="h-7 w-7 rounded-md">
                                                    ...
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                {transaction?.is_refunded == 0 && (
                                                    <DropdownMenuItem asChild>
                                                        <Link href={'/CT-'+transaction.tr_number} target='_blank'>
                                                            <LucidePrinter className='mr-2 h-3 w-3' /> Print
                                                        </Link>
                                                    </DropdownMenuItem>
                                                )}
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </td>
                                </tr>
                            );
                            })}
                        </tbody>}
                        {openCounter?.transactions.length > 0 && openCounter?.transactions.length < 10 && <tbody>
                            {Array.from({ length: Math.ceil((10 - openCounter.transactions.length)/3) }).map((_, index) => (
                                <tr key={`empty-${index}`} className='border-b border-dotted dark:border-gray-100 h-10 bg-gray-50/30'>
                                    <td colSpan={9}></td>
                                </tr>
                            ))}
                        </tbody>}
                        {openCounter?.transactions.length == 0 && <tbody>
                            <tr className='border-b border-dotted dark:border-gray-600 bg-gray-50/50'>
                                <td colSpan={9} className='px-4 py-6 text-center text-gray-400'>No transactions found.</td>
                            </tr>
                        </tbody>}
                        {openCounter?.transactions.length > 0 && <tfoot className='text-xs'>
                            <tr className='border-t-2 bg-gray-50/80 dark:bg-gray-700'>
                                <td colSpan={5} className='px-3 py-2 text-right font-medium text-gray-500'>Cash Income</td>
                                <td className='px-3 py-2 text-right font-mono font-semibold text-green-700'>{incomeByType('CASH').toLocaleString()} <span className='text-gray-400 font-normal'>PKR</span></td>
                                <td colSpan={3}></td>
                            </tr>
                            <tr className='bg-gray-50/80 dark:bg-gray-700'>
                                <td colSpan={5} className='px-3 py-2 text-right font-medium text-gray-500'>Cheque Income</td>
                                <td className='px-3 py-2 text-right font-mono font-semibold text-green-700'>{incomeByType('CHEQUE').toLocaleString()} <span className='text-gray-400 font-normal'>PKR</span></td>
                                <td colSpan={3}></td>
                            </tr>
                            <tr className='bg-gray-50/80 dark:bg-gray-700'>
                                <td colSpan={5} className='px-3 py-2 text-right font-medium text-gray-500'>Bank Transfer Income</td>
                                <td className='px-3 py-2 text-right font-mono font-semibold text-green-700'>{incomeByType('BANK_TRANSFER').toLocaleString()} <span className='text-gray-400 font-normal'>PKR</span></td>
                                <td colSpan={3}></td>
                            </tr>
                            <tr className='bg-gray-50/80 dark:bg-gray-700'>
                                <td colSpan={5} className='px-3 py-2 text-right font-medium text-gray-500'>Card Income</td>
                                <td className='px-3 py-2 text-right font-mono font-semibold text-green-700'>{incomeByType('CARD').toLocaleString()} <span className='text-gray-400 font-normal'>PKR</span></td>
                                <td colSpan={3}></td>
                            </tr>
                            <tr className='bg-red-50/50 dark:bg-gray-700'>
                                <td colSpan={5} className='px-3 py-2 text-right font-medium text-gray-500'>Expense Paid</td>
                                <td className='px-3 py-2 text-right font-mono font-semibold text-red-600'>{sumByFilter((tr: any) => tr.income_or_expense === 'EXPENSE').toLocaleString()} <span className='text-gray-400 font-normal'>PKR</span></td>
                                <td colSpan={3}></td>
                            </tr>
                            <tr className='bg-orange-50/50 dark:bg-gray-700'>
                                <td colSpan={5} className='px-3 py-2 text-right font-medium text-gray-500'>Voucher Payments</td>
                                <td className='px-3 py-2 text-right font-mono font-semibold text-orange-600'>{sumByFilter((tr: any) => tr.income_or_expense === 'VOUCHER-PAY').toLocaleString()} <span className='text-gray-400 font-normal'>PKR</span></td>
                                <td colSpan={3}></td>
                            </tr>
                            <tr className='border-t-2 border-gray-300 bg-white dark:bg-gray-800'>
                                <td colSpan={5} className='px-3 py-2.5 text-right font-bold text-gray-800 dark:text-white'>Net Total</td>
                                <td className='px-3 py-2.5 text-right font-mono font-bold text-gray-900 dark:text-white'>
                                    {openCounter?.transactions.reduce((total:number, tr:any) => (tr.income_or_expense === 'EXPENSE' || tr.income_or_expense === 'VOUCHER-PAY') ? total - Number(tr.amount) : total + Number(tr.amount), 0).toLocaleString()} <span className='text-gray-400 font-normal'>PKR</span>
                                </td>
                                <td colSpan={3}></td>
                            </tr>
                            <tr className='bg-purple-50/50 dark:bg-gray-700'>
                                <td colSpan={5} className='px-3 py-2 text-right font-medium text-gray-500'>Receivables</td>
                                <td className='px-3 py-2 text-right font-mono font-semibold text-purple-600'>
                                    {openCounter?.transactions.filter((tr:any) => tr.receaveable).reduce((sum:number, tr:any) => sum + Number(tr.receaveable.amount), 0).toLocaleString()} <span className='text-gray-400 font-normal'>PKR</span>
                                </td>
                                <td colSpan={2} className='px-3 py-2 text-purple-500 text-[11px]'>{openCounter?.transactions.filter((tr:any) => tr.receaveable).length} items</td>
                                <td></td>
                            </tr>
                        </tfoot>}
                    </table>
                </div>


            </div>)
}

