import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import TablePagination from '@/components/ui/table-pagination';
import AppLayout from '@/layouts/app-layout';
import { MONTHS } from '@/lib/constants';
import {
    counter,
    counterView,
    home,
    myCounterList,
    myCounterListYear,
    myCounterListYearMonth,
} from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

interface PageProps {
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

export default function CountersList() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Counters',
            href: counter().url,
        },
    ];

    const { yearSelected, monthSelected, closings, openCounter } =
        usePage<PageProps>().props;

    if (yearSelected) {
        breadcrumbs.push({
            title: yearSelected,
            href: myCounterListYear({
                year: yearSelected,
            }).url,
        });
    }

    if (monthSelected) {
        breadcrumbs.push({
            title: getMonthAgainstNumer(monthSelected),
            href: myCounterListYearMonth({
                year: yearSelected,
                month: monthSelected,
            }).url,
        });
    }

    const [year, setYear] = useState<string>(yearSelected as string);
    const [month, setMonth] = useState<string>(monthSelected as string);

    useEffect(() => {
        // Only navigate if the current values differ from the initial props
        if (year !== yearSelected || month !== monthSelected) {
            let url = '';
            if (year != '0' && month != '0') {
                url = myCounterListYearMonth({
                    year: year,
                    month: month,
                }).url;
            } else if (year != '0') {
                url = myCounterListYear({
                    year: year,
                }).url;
            } else {
                url = myCounterList().url;
            }

            router.get(url, {}, { preserveState: true });
        }
    }, [year, month, yearSelected, monthSelected]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex flex-0 flex-row gap-4 rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-teal-50 via-white to-sky-50 p-6 shadow-sm dark
:border-sidebar-border dark:from-teal-950/40 dark:via-gray-900 dark:to-sky-950/40">
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
                        {/* <InputError message={errors.email} /> */}
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
                                {MONTHS.map((m) => (
                                    <SelectItem key={m.value} value={m.value}>
                                        {m.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {/* <InputError message={errors.email} /> */}
                    </div>
                </div>
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-0 text-[#1c398e] dark:bg-neutral-950">
                    <table className="bg-gray-50 text-left text-xs text-gray-700 uppercase dark:bg-neutral-950 dark:text-gray-400">
                        <thead>
                            <tr>
                                <th scope="col" className="px-6 py-3">
                                    Info
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Status
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Opening Amount
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Closing Amount
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Expense Payed
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Opened At
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Closed At
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {closings.data.map((p) => {
                                const explodedPsid = p.ct_number.split('/');

                                return (
                                    <tr
                                        key={p.id}
                                        className="border-b border-gray-200 bg-white dark:border-neutral-950 dark:bg-neutral-800"
                                    >
                                        <td
                                            scope="row"
                                            className="flex flex-col px-6 py-3 font-medium whitespace-nowrap text-gray-900 dark:text-white"
                                        >
                                            <Link
                                                href={
                                                    counterView({
                                                        ctYear:
                                                            explodedPsid[1] ||
                                                            '',
                                                        ctMonth:
                                                            explodedPsid[2] ||
                                                            '',
                                                        ctNumber:
                                                            explodedPsid[3] ||
                                                            '',
                                                    }).url
                                                }
                                            >
                                                <span className="text-blue-500">
                                                    CT# {p.ct_number}
                                                </span>
                                            </Link>
                                        </td>
                                        <td className="px-6 py-3">
                                            <CounterStatusBadge
                                                status={p.status}
                                            />
                                        </td>
                                        <td className="px-6 py-3">
                                            {p.opening_amount}
                                        </td>
                                        <td className="px-6 py-3">
                                            {p.closing_amount}
                                        </td>
                                        <td className="px-6 py-3">
                                            {p.expense_payed}
                                        </td>
                                        <td className="px-6 py-3">
                                            {formatDateTime(p.created_at)}
                                        </td>
                                        <td className="px-6 py-3">
                                            {formatDateTime(p.closed_at)}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan={7}>
                                    <TablePagination
                                        currentPage={closings.current_page}
                                        lastPage={closings.last_page}
                                        makeHref={(page) =>
                                            `?page=${page}&year=${year}&month=${month}`
                                        }
                                    />
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}

function formatDateTime(value?: string | null) {
    if (!value) {
        return '—';
    }

    const parsed = new Date(value);

    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return parsed.toLocaleString();
}

function CounterStatusBadge({ status }: { status?: string }) {
    const normalized = (status ?? '').toUpperCase();

    const styles: Record<string, string> = {
        OPEN: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        CLOSED: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        REPORTED:
            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    };

    const badgeClass =
        styles[normalized] ??
        'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';

    return (
        <span
            className={`inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ${badgeClass}`}
        >
            {normalized || 'UNKNOWN'}
        </span>
    );
}

function getMonthAgainstNumer(monthString = '00') {
    switch (monthString) {
        case '00':
            return 'All';
            break;
        case '01':
            return 'Jan';
            break;
        case '02':
            return 'Feb';
            break;
        case '03':
            return 'Mar';
            break;
        case '04':
            return 'Apr';
            break;
        case '05':
            return 'May';
            break;
        case '06':
            return 'Jun';
            break;
        case '07':
            return 'Jul';
            break;
        case '08':
            return 'Aug';
            break;
        case '09':
            return 'Sep';
            break;
        case '10':
            return 'Oct';
            break;
        case '11':
            return 'Nov';
            break;
        case '12':
            return 'Dec';
            break;
        default:
            return 'Unknown';
    }
}
