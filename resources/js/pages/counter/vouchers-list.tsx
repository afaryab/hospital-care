import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import {
    counterExpense,
    counterExpenseNewVoucher,
    counterExpenseVouchersList,
    home,
} from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

interface ExpenseVoucher {
    id: number;
    vc_number: string;
    amount: string;
    notes?: string;
    status: 'payed' | 'pending';
    created_at: string;
    exp_category?: { id: number; name: string } | null;
    payed_to?: { id: number; name: string } | null;
}

interface PageProps {
    yearSelected: string;
    monthSelected: string;
    vouchers: {
        data: ExpenseVoucher[];
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
        [key: string]: any;
    };
    [key: string]: any;
}

export default function VouchersList() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Expenses',
            href: counterExpense().url,
        },
        {
            title: 'Doctor Vouchers',
            href: counterExpenseVouchersList().url,
        },
    ];

    const { yearSelected, monthSelected, vouchers } =
        usePage<PageProps>().props;

    const [year, setYear] = useState<string>(yearSelected as string);
    const [month, setMonth] = useState<string>(monthSelected as string);

    useEffect(() => {
        if (year !== yearSelected || month !== monthSelected) {
            const query: Record<string, string> = {};
            if (year !== '0') query.year = year;
            if (month !== '0') query.month = month;

            router.get(
                counterExpenseVouchersList({ query }).url,
                {},
                { preserveState: true },
            );
        }
    }, [year, month, yearSelected, monthSelected]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Doctor Vouchers" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex flex-0 flex-row items-end gap-4 rounded-xl bg-[#1c398e] p-2 dark:bg-[#0a0a0a]">
                    <div className="grid gap-2">
                        <Label htmlFor="year">Year</Label>
                        <Select
                            value={year.toString()}
                            onValueChange={(value) => setYear(value)}
                        >
                            <SelectTrigger id="year">
                                <SelectValue placeholder="Select Year" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem key={0} value={'0'}>
                                    All
                                </SelectItem>
                                {Array.from({ length: 10 }, (_, i) => {
                                    const yearOption =
                                        new Date().getFullYear() - i;
                                    return (
                                        <SelectItem
                                            key={yearOption}
                                            value={yearOption.toString()}
                                        >
                                            {yearOption}
                                        </SelectItem>
                                    );
                                })}
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="month">Month</Label>
                        <Select
                            value={month.toString()}
                            onValueChange={(value) => setMonth(value)}
                        >
                            <SelectTrigger id="month">
                                <SelectValue placeholder="Select Month" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="0">All</SelectItem>
                                <SelectItem value="01">January</SelectItem>
                                <SelectItem value="02">February</SelectItem>
                                <SelectItem value="03">March</SelectItem>
                                <SelectItem value="04">April</SelectItem>
                                <SelectItem value="05">May</SelectItem>
                                <SelectItem value="06">June</SelectItem>
                                <SelectItem value="07">July</SelectItem>
                                <SelectItem value="08">August</SelectItem>
                                <SelectItem value="09">September</SelectItem>
                                <SelectItem value="10">October</SelectItem>
                                <SelectItem value="11">November</SelectItem>
                                <SelectItem value="12">December</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="grid gap-2">
                        <Link href={counterExpenseNewVoucher().url}>
                            <Button variant="secondary">
                                + New Doctor Voucher
                            </Button>
                        </Link>
                    </div>
                </div>
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-0 text-[#1c398e] dark:bg-neutral-950">
                    <table className="bg-gray-50 text-left text-xs text-gray-700 uppercase dark:bg-neutral-950 dark:text-gray-400">
                        <thead>
                            <tr>
                                <th scope="col" className="px-6 py-3">
                                    Voucher #
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Category
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Payed To
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Amount
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Status
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Notes
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Date
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {vouchers.data.map((v) => (
                                <tr
                                    key={v.id}
                                    className="border-b border-gray-200 bg-white dark:border-neutral-950 dark:bg-neutral-800"
                                >
                                    <td className="px-6 py-3 font-medium whitespace-nowrap text-gray-900 dark:text-white">
                                        <span className="text-blue-500">
                                            {v.vc_number}
                                        </span>
                                    </td>
                                    <td className="px-6 py-3">
                                        {v.exp_category?.name ?? '-'}
                                    </td>
                                    <td className="px-6 py-3">
                                        {v.payed_to?.name ?? '-'}
                                    </td>
                                    <td className="px-6 py-3">{v.amount}</td>
                                    <td className="px-6 py-3">
                                        <span
                                            className={`inline-block rounded px-2 py-1 text-xs font-semibold ${
                                                v.status === 'payed'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-yellow-100 text-yellow-800'
                                            }`}
                                        >
                                            {v.status === 'payed'
                                                ? 'Payed'
                                                : 'Pending'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-3">
                                        {v.notes ?? '-'}
                                    </td>
                                    <td className="px-6 py-3">
                                        {v.created_at
                                            ? new Date(
                                                  v.created_at,
                                              ).toLocaleDateString()
                                            : '-'}
                                    </td>
                                    <td className="px-6 py-3">
                                        {v.status === 'pending' && (
                                            <Link
                                                href={
                                                    counterExpense({
                                                        query: {
                                                            voucher_id:
                                                                v.id.toString(),
                                                        },
                                                    }).url
                                                }
                                            >
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    Pay
                                                </Button>
                                            </Link>
                                        )}
                                    </td>
                                </tr>
                            ))}
                            {vouchers.data.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={8}
                                        className="px-6 py-3 text-center text-gray-500"
                                    >
                                        No vouchers found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan={8} className="text-right">
                                    {(() => {
                                        const current = vouchers.current_page;
                                        const last = vouchers.last_page;

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
                                            const delta = 1;
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
                                            counterExpenseVouchersList({
                                                query: {
                                                    page: page.toString(),
                                                    year,
                                                    month,
                                                },
                                            }).url;

                                        return (
                                            <nav className="flex items-center justify-center gap-2 py-2">
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
                                                {pages.map((p, idx) =>
                                                    p === '...' ? (
                                                        <span
                                                            key={`ellipsis-${idx}`}
                                                            className="px-2"
                                                        >
                                                            …
                                                        </span>
                                                    ) : (
                                                        <a
                                                            key={p}
                                                            href={makeHref(
                                                                p as number,
                                                            )}
                                                            className={`rounded border px-3 py-1 ${
                                                                p === current
                                                                    ? 'bg-[#1c398e] font-bold text-white'
                                                                    : 'bg-white text-[#1c398e] hover:underline'
                                                            }`}
                                                        >
                                                            {p}
                                                        </a>
                                                    ),
                                                )}
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
