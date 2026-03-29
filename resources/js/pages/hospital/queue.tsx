import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { counter, counterView, home, myCounterList, myCounterListYear, myCounterListYearMonth, patientsRegister, patientsRegisterPsNumber, patientsRegisterPsNumberDepartment, patientsRegisterYear, patientsRegisterYearMonth } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';

interface opdQueuePageProps {
    yearSelected: string;
    monthSelected: string;
    closings: {
        data: Array<{
            id: number;
            name: string;
            [key: string]: any;
        }>;
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
        [key: string]: any;
    };
    [key: string]: any;
}

export default function ServiceOrdersList() {

    let breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Hospital OPD',
            href: counter().url
        }
    ];
    
    const { serviceOrders } = usePage<opdQueuePageProps>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="OPD Hospital Queue" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72] dark:bg-[#262626]">
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-0 bg-white dark:bg-neutral-950 text-[#1c398e]">
                    <table className='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-neutral-950 dark:text-gray-400 text-left'>
                        <thead>
                            <tr>
                                <th scope="col" className="px-6 py-3">Info</th>
                                <th scope="col" className="px-6 py-3">Opening Amount</th>
                                <th scope="col" className="px-6 py-3">Closing Amount</th>
                                <th scope="col" className="px-6 py-3">Expense Payed</th>
                                <th scope="col" className="px-6 py-3">Closed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            {serviceOrders?.data.length === 0 && (
                                <tr  className='bg-white border-b dark:bg-neutral-800 dark:border-neutral-950 border-gray-200'>
                                    <td colSpan={5} rowSpan={4} className="px-6 py-3 text-center">
                                        No service orders found.
                                    </td>
                                </tr>
                            )}
                            {serviceOrders?.data.length > 0 && serviceOrders?.data.map((serviceOrder: any) => {

                                let explodedPsid = serviceOrder.so_number.split('/');

                                return (
                                    <tr key={serviceOrder.id} className='bg-white border-b dark:bg-neutral-800 dark:border-neutral-950 border-gray-200'>
                                        <td scope="row" className="px-6 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white flex flex-col">
                                            <Link href={counterView({
                                                ctYear: explodedPsid[1] || '',
                                                ctMonth: explodedPsid[2] || '',
                                                ctNumber: explodedPsid[3] || ''
                                            }).url} ><span className='text-blue-500'>CT# {serviceOrder.ct_number}</span></Link>
                                        </td>
                                        <td className="px-6 py-3">
                                            {serviceOrder.so_number}
                                        </td>
                                        <td className="px-6 py-3">
                                            {serviceOrder.status}
                                        </td>
                                        <td className="px-6 py-3">
                                            {serviceOrder.notes}
                                        </td>
                                        <td className="px-6 py-3">
                                            {serviceOrder.closed_at}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan={4} className='text-right'>

                                    
                                    {
                                        (() => {
                                            const current = serviceOrders.current_page;
                                            const last = serviceOrders.last_page;

                                            function buildRange(curr: number, lastPage: number) {
                                                if (lastPage <= 7) {
                                                    return Array.from({ length: lastPage }, (_, i) => i + 1) as (number | '...')[];
                                                }

                                                const delta = 1; // show current +/- delta
                                                const range: number[] = [];
                                                for (let i = Math.max(2, curr - delta); i <= Math.min(lastPage - 1, curr + delta); i++) {
                                                    range.push(i);
                                                }

                                                const pages: (number | '...')[] = [1];
                                                if (range.length && range[0] > 2) pages.push('...');
                                                pages.push(...range);
                                                if (range.length && range[range.length - 1] < lastPage - 1) pages.push('...');
                                                pages.push(lastPage);
                                                return pages;
                                            }

                                            const pages = buildRange(current, last);

                                            const makeHref = (page: number) =>
                                                `?page=${page}`;

                                            return (
                                                <nav className="flex items-center justify-center gap-2 py-2">
                                                    {/* Prev */}
                                                    {current > 1 ? (
                                                        <a
                                                            href={makeHref(current - 1)}
                                                            className="px-3 py-1 rounded border bg-white text-[#1c398e] hover:underline"
                                                            aria-label="Previous page"
                                                        >
                                                            ‹
                                                        </a>
                                                    ) : (
                                                        <span className="px-3 py-1 rounded border bg-gray-100 text-gray-400">‹</span>
                                                    )}

                                                    {/* Page items */}
                                                    {pages.map((p, idx) =>
                                                        p === '...' ? (
                                                            <span key={`dots-${idx}`} className="px-3 py-1 text-gray-500">
                                                                …
                                                            </span>
                                                        ) : p === current ? (
                                                            <span
                                                                key={p}
                                                                aria-current="page"
                                                                className="px-3 py-1 rounded bg-[#06df72] dark:bg-neutral-800 text-white font-medium"
                                                            >
                                                                {p}
                                                            </span>
                                                        ) : (
                                                            <a
                                                                key={p}
                                                                href={makeHref(p)}
                                                                className="px-3 py-1 rounded border bg-white text-[#1c398e] hover:underline"
                                                            >
                                                                {p}
                                                            </a>
                                                        )
                                                    )}

                                                    {/* Next */}
                                                    {current < last ? (
                                                        <a
                                                            href={makeHref(current + 1)}
                                                            className="px-3 py-1 rounded border bg-white text-[#1c398e] hover:underline"
                                                            aria-label="Next page"
                                                        >
                                                            ›
                                                        </a>
                                                    ) : (
                                                        <span className="px-3 py-1 rounded border bg-gray-100 text-gray-400">›</span>
                                                    )}
                                                </nav>
                                            );
                                        })()
                                    }



                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}