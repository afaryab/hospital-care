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
import TablePagination from '@/components/ui/table-pagination';
import AppLayout from '@/layouts/app-layout';
import { MONTHS } from '@/lib/constants';
import {
    home,
    patientsRegister,
    patientsRegisterPsNumber,
    patientsRegisterPsNumberDepartment,
    patientsRegisterYear,
    patientsRegisterYearMonth,
} from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

interface PageProps {
    yearSelected: string;
    monthSelected: string;
    patientsPaginated: {
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
    filters?: {
        search?: string;
        contact?: string;
    };
    [key: string]: any;
}

export default function Register() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Register',
            href: patientsRegister().url,
        },
    ];

    const { yearSelected, monthSelected, patientsPaginated, filters } =
        usePage<PageProps>().props;

    if (yearSelected) {
        breadcrumbs.push({
            title: yearSelected,
            href: patientsRegisterYear({
                year: yearSelected,
            }).url,
        });
    }

    if (monthSelected) {
        breadcrumbs.push({
            title: getMonthAgainstNumer(monthSelected),
            href: patientsRegisterYearMonth({
                year: yearSelected,
                month: monthSelected,
            }).url,
        });
    }

    const [year, setYear] = useState<string>(yearSelected as string);
    const [month, setMonth] = useState<string>(monthSelected as string);
    const [search, setSearch] = useState<string>(filters?.search ?? '');
    const [contact, setContact] = useState<string>(filters?.contact ?? '');

    const registerUrlForPeriod = (y: string, m: string) => {
        if (y != '0' && m != '0') {
            return patientsRegisterYearMonth({ year: y, month: m }).url;
        }
        if (y != '0') {
            return patientsRegisterYear({ year: y }).url;
        }
        return patientsRegister().url;
    };

    const applyFilters = () => {
        router.get(
            registerUrlForPeriod(year, month),
            {
                search: search || undefined,
                contact: contact || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const clearFilters = () => {
        setSearch('');
        setContact('');
        router.get(
            registerUrlForPeriod(year, month),
            {},
            { preserveState: false, replace: true },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="dark :border-sidebar-border flex flex-wrap items-end gap-4 rounded-2xl border border-sidebar-border/70 bg-gradient-to-br from-teal-50 via-white to-sky-50 p-6 shadow-sm dark:from-teal-950/40 dark:via-gray-900 dark:to-sky-950/40">
                    <div className="grid gap-2">
                        <Label htmlFor="year">Year</Label>
                        <Select
                            value={year.toString()}
                            onValueChange={(value) => {
                                setYear(value);
                                router.get(
                                    registerUrlForPeriod(value, month),
                                    {
                                        search: search || undefined,
                                        contact: contact || undefined,
                                    },
                                    { preserveState: true, replace: true },
                                );
                            }}
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
                            onValueChange={(value) => {
                                setMonth(value);
                                router.get(
                                    registerUrlForPeriod(year, value),
                                    {
                                        search: search || undefined,
                                        contact: contact || undefined,
                                    },
                                    { preserveState: true, replace: true },
                                );
                            }}
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
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="search">Search</Label>
                        <Input
                            id="search"
                            type="text"
                            name="search"
                            autoFocus
                            autoComplete="off"
                            placeholder="Patient name, PS#, or SO#/short#"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) =>
                                e.key === 'Enter' && applyFilters()
                            }
                        />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="patient_contact">Patient Contact</Label>
                        <Input
                            id="patient_contact"
                            type="text"
                            name="patient_contact"
                            autoComplete="off"
                            placeholder="+92-"
                            value={contact}
                            onChange={(e) => setContact(e.target.value)}
                            onKeyDown={(e) =>
                                e.key === 'Enter' && applyFilters()
                            }
                        />
                    </div>
                    <div className="flex gap-3">
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
                                    Info
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Contact
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Departmental Records
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Others
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {patientsPaginated.data.map((p) => {
                                const explodedPsid = p.ps_number.split('/');

                                return (
                                    <tr
                                        key={p.id}
                                        className="border-b border-gray-200 bg-white dark:border-neutral-950 dark:bg-neutral-800"
                                    >
                                        <th
                                            scope="row"
                                            className="flex flex-col px-6 py-4 font-medium whitespace-nowrap text-gray-900 dark:text-white"
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
                                                    PS# {p.ps_number}
                                                </span>
                                            </Link>
                                            <span>
                                                Name: {p.name}{' '}
                                                {p.guardian && p.relation && (
                                                    <span>
                                                        ({p.relation} of{' '}
                                                        {p.guardian})
                                                    </span>
                                                )}
                                            </span>
                                            <span>Gender: {p.gender}</span>
                                        </th>
                                        <td className="px-6 py-4">
                                            <span>{p.contact}</span>
                                            <span>{p.cnic}</span>
                                        </td>
                                        <td className="flex flex-row px-6 py-4">
                                            {[
                                                'OPD',
                                                'IND',
                                                'EMR',
                                                'PTH',
                                                'XRY',
                                                'RAD',
                                            ].map((itm) => {
                                                return (
                                                    <Link
                                                        href={
                                                            patientsRegisterPsNumberDepartment(
                                                                {
                                                                    year:
                                                                        explodedPsid[1] ||
                                                                        '',
                                                                    month:
                                                                        explodedPsid[2] ||
                                                                        '',
                                                                    number:
                                                                        explodedPsid[3] ||
                                                                        '',
                                                                    departmentKey:
                                                                        itm,
                                                                },
                                                            ).url
                                                        }
                                                    >
                                                        <span className="text-blue-500">
                                                            {itm}
                                                        </span>
                                                    </Link>
                                                );
                                            })}
                                        </td>
                                        <td className="px-6 py-4"></td>
                                    </tr>
                                );
                            })}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan={4}>
                                    <TablePagination
                                        currentPage={
                                            patientsPaginated.current_page
                                        }
                                        lastPage={patientsPaginated.last_page}
                                        makeHref={(page) =>
                                            `?page=${page}&year=${year}&month=${month}&search=${encodeURIComponent(search)}&contact=${encodeURIComponent(contact)}`
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
