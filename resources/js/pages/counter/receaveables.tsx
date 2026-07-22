import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import ReceaveAblesButton from '@/elements/receaveables/ReceaveAblesButton';
import AppLayout from '@/layouts/app-layout';
import {
    counter,
    home,
    patientsRegisterPsNumber,
    receaveables as receaveablesRoute,
    serviceOrdersOverview,
} from '@/routes';
import {
    type BreadcrumbItem,
    type Panel,
    type PaymentMethod,
    type Receaveable,
} from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

interface PageProps {
    yearSelected: string;
    monthSelected: string;
    receaveables: {
        data: Receaveable[];
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
        [key: string]: any;
    };
    paymentMethods: PaymentMethod[];
    panelCompanies: Panel[];
    filters?: {
        status?: string;
        search?: string;
    };
    [key: string]: any;
}

const currency = new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    maximumFractionDigits: 2,
});

function formatMoney(value?: number | null): string {
    return currency.format(value ?? 0);
}

export default function ReveaveablesList() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Counters',
            href: counter().url,
        },
        {
            title: 'Receaveables',
            href: counter().url,
        },
    ];

    const { receaveables, paymentMethods, panelCompanies, filters } =
        usePage<PageProps>().props;

    const [status, setStatus] = useState<string>(filters?.status ?? 'unpaid');
    const [search, setSearch] = useState<string>(filters?.search ?? '');

    const applyFilters = () => {
        router.get(
            receaveablesRoute().url,
            {
                status: status || undefined,
                search: search || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const clearFilters = () => {
        setStatus('unpaid');
        setSearch('');
        router.get(
            receaveablesRoute().url,
            {},
            { preserveState: false, replace: true },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Receaveables - Counter" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="grid gap-4 rounded-xl bg-white p-4 text-[#1c398e] lg:grid-cols-3 dark:bg-neutral-950">
                    <div className="space-y-2 lg:col-span-2">
                        <Label htmlFor="receaveable-search">Search</Label>
                        <Input
                            id="receaveable-search"
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            onKeyDown={(event) =>
                                event.key === 'Enter' && applyFilters()
                            }
                            placeholder="Patient name, PS#, or SO#/short#"
                        />
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="receaveable-status">Status</Label>
                        <Select
                            value={status}
                            onValueChange={(value) => setStatus(value)}
                        >
                            <SelectTrigger id="receaveable-status">
                                <SelectValue placeholder="Unpaid" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="unpaid">Unpaid</SelectItem>
                                <SelectItem value="paid">Paid</SelectItem>
                                <SelectItem value="all">All</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="flex gap-3 lg:col-span-3">
                        <Button onClick={applyFilters}>Apply Filters</Button>
                        <Button variant="outline" onClick={clearFilters}>
                            Clear
                        </Button>
                    </div>
                </div>
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-0 text-[#1c398e] dark:bg-neutral-950">
                    <table className="bg-gray-50 text-left text-xs text-gray-700 uppercase dark:bg-neutral-950 dark:text-gray-400">
                        <thead>
                            <tr>
                                <th scope="col" className="px-6 py-3">
                                    Patient
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Service Order
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Transactions
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Expense Vouchers
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Orignal
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Remaining
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Status
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {receaveables.data.map((r) => {
                                const explodedPsid =
                                    r.patient.ps_number.split('/');
                                const linkedServiceOrder =
                                    r.linked_service_order;
                                const payments = r.payments ?? [];
                                const expenseVouchers =
                                    r.expense_vouchers ?? [];

                                return (
                                    <tr
                                        key={r.id}
                                        className="border-b border-gray-200 bg-white align-top dark:border-neutral-950 dark:bg-neutral-800"
                                    >
                                        <td
                                            scope="row"
                                            className="flex flex-col px-6 py-3 font-medium whitespace-nowrap text-gray-900 dark:text-white"
                                        >
                                            <Link
                                                href={
                                                    patientsRegisterPsNumber({
                                                        year:
                                                            explodedPsid[1] ||
                                                            '',
                                                        month:
                                                            explodedPsid[2] ||
                                                            '',
                                                        number:
                                                            explodedPsid[3] ||
                                                            '',
                                                    }).url
                                                }
                                            >
                                                <span className="text-blue-500">
                                                    {r.patient.name}
                                                </span>
                                            </Link>
                                        </td>
                                        <td className="px-6 py-3 normal-case">
                                            {linkedServiceOrder ? (
                                                <Link
                                                    href={
                                                        serviceOrdersOverview({
                                                            query: {
                                                                service_order_id:
                                                                    linkedServiceOrder.id,
                                                            },
                                                        }).url
                                                    }
                                                    className="text-blue-500"
                                                >
                                                    {
                                                        linkedServiceOrder.so_number
                                                    }
                                                </Link>
                                            ) : (
                                                <span className="text-gray-400">
                                                    —
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-6 py-3 normal-case">
                                            <div>
                                                TR:{' '}
                                                {r.transaction?.tr_number ??
                                                    '—'}
                                            </div>
                                            {payments.map((payment) => (
                                                <div
                                                    key={payment.id}
                                                    className="text-gray-500"
                                                >
                                                    Payment: {payment.tr_number}{' '}
                                                    (
                                                    {formatMoney(
                                                        payment.amount,
                                                    )}
                                                    )
                                                </div>
                                            ))}
                                        </td>
                                        <td className="px-6 py-3 normal-case">
                                            {expenseVouchers.length === 0 ? (
                                                <span className="text-gray-400">
                                                    —
                                                </span>
                                            ) : (
                                                expenseVouchers.map(
                                                    (voucher) => (
                                                        <div key={voucher.id}>
                                                            {voucher.vc_number}:{' '}
                                                            {formatMoney(
                                                                voucher.share_amount,
                                                            )}
                                                            {voucher.share_amount !==
                                                                voucher.amount && (
                                                                <span className="text-[10px] text-gray-500">
                                                                    {' '}
                                                                    (of{' '}
                                                                    {formatMoney(
                                                                        voucher.amount,
                                                                    )}
                                                                    , shared)
                                                                </span>
                                                            )}
                                                        </div>
                                                    ),
                                                )
                                            )}
                                        </td>
                                        <td className="px-6 py-3">
                                            {r.orignal_amount}
                                        </td>
                                        <td className="px-6 py-3">
                                            {r.amount}
                                        </td>
                                        <td className="px-6 py-3">
                                            {r.status}
                                        </td>
                                        <td className="px-6 py-3">
                                            <ReceaveAblesButton
                                                receaveable={r}
                                                paymentMethods={paymentMethods}
                                                panelCompanies={panelCompanies}
                                            />
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan={8} className="text-right">
                                    {(() => {
                                        const current =
                                            receaveables.current_page;
                                        const last = receaveables.last_page;

                                        function buildRange(
                                            curr: number,
                                            lastPage: number,
                                        ) {
                                            if (lastPage <= 7) {
                                                return Array.from(
                                                    { length: lastPage },
                                                    (_, i) => i + 1,
                                                ) as (number | '...')[];
                                            }

                                            const delta = 1; // show current +/- delta
                                            const range: number[] = [];
                                            for (
                                                let i = Math.max(
                                                    2,
                                                    curr - delta,
                                                );
                                                i <=
                                                Math.min(
                                                    lastPage - 1,
                                                    curr + delta,
                                                );
                                                i++
                                            ) {
                                                range.push(i);
                                            }

                                            const pages: (number | '...')[] = [
                                                1,
                                            ];
                                            if (range.length && range[0] > 2)
                                                pages.push('...');
                                            pages.push(...range);
                                            if (
                                                range.length &&
                                                range[range.length - 1] <
                                                    lastPage - 1
                                            )
                                                pages.push('...');
                                            pages.push(lastPage);
                                            return pages;
                                        }

                                        const pages = buildRange(current, last);

                                        const makeHref = (page: number) =>
                                            `?page=${page}&status=${status}&search=${encodeURIComponent(search)}`;

                                        return (
                                            <nav className="flex items-center justify-center gap-2 py-2">
                                                {/* Prev */}
                                                {current > 1 ? (
                                                    <a
                                                        href={makeHref(
                                                            current - 1,
                                                        )}
                                                        className="rounded border bg-white px-3 py-1 text-[#1c398e] hover:underline"
                                                        aria-label="Previous page"
                                                    >
                                                        ‹
                                                    </a>
                                                ) : (
                                                    <span className="rounded border bg-gray-100 px-3 py-1 text-gray-400">
                                                        ‹
                                                    </span>
                                                )}

                                                {/* Page items */}
                                                {pages.map((p, idx) =>
                                                    p === '...' ? (
                                                        <span
                                                            key={`dots-${idx}`}
                                                            className="px-3 py-1 text-gray-500"
                                                        >
                                                            …
                                                        </span>
                                                    ) : p === current ? (
                                                        <span
                                                            key={p}
                                                            aria-current="page"
                                                            className="rounded bg-[#06df72] px-3 py-1 font-medium text-white dark:bg-neutral-800"
                                                        >
                                                            {p}
                                                        </span>
                                                    ) : (
                                                        <a
                                                            key={p}
                                                            href={makeHref(p)}
                                                            className="rounded border bg-white px-3 py-1 text-[#1c398e] hover:underline"
                                                        >
                                                            {p}
                                                        </a>
                                                    ),
                                                )}

                                                {/* Next */}
                                                {current < last ? (
                                                    <a
                                                        href={makeHref(
                                                            current + 1,
                                                        )}
                                                        className="rounded border bg-white px-3 py-1 text-[#1c398e] hover:underline"
                                                        aria-label="Next page"
                                                    >
                                                        ›
                                                    </a>
                                                ) : (
                                                    <span className="rounded border bg-gray-100 px-3 py-1 text-gray-400">
                                                        ›
                                                    </span>
                                                )}
                                            </nav>
                                        );
                                    })()}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
