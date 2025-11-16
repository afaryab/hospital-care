import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { home, patientsRegister, patientsRegisterPsNumber, patientsRegisterPsNumberDepartment, patientsRegisterYear, patientsRegisterYearMonth } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';

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
    [key: string]: any;
}

export default function Register() {

    let breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Register',
            href: patientsRegister().url
        }
    ];
    
    const { yearSelected, monthSelected, patientsPaginated } = usePage<PageProps>().props;

    if(yearSelected){
        breadcrumbs.push({
            title: yearSelected,
            href: patientsRegisterYear({
                year: yearSelected
            }).url
        })
    }

    if(monthSelected){
        breadcrumbs.push({
            title: getMonthAgainstNumer(monthSelected),
            href: patientsRegisterYearMonth({
                year: yearSelected,
                month: monthSelected
            }).url
        })
    }


    const [year, setYear] = useState<string>(yearSelected as string);
    const [month, setMonth] = useState<string>(monthSelected as string);
    const [patientName , setPatientName] = useState<string>('');
    const [patientContact , setPatientContact] = useState<string>('');


    useEffect(() => {
        // Only navigate if the current values differ from the initial props
        if (year !== yearSelected || month !== monthSelected) {
            let url = '';
            if(year != '0' && month != '0'){
                url = patientsRegisterYearMonth({
                    year: year,
                    month: month
                }).url
            }else if(year != '0'){
                url = patientsRegisterYear({
                    year: year
                }).url
            }else{
                url = patientsRegister().url
            }

            router.get(url,{
                name: patientName,
                contact: patientContact
            });
        }
    }, [year, month, yearSelected, monthSelected]);


    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72] dark:bg-[#262626]">
                <div className="flex flex-0 flex-row gap-4 rounded-xl p-2 bg-[#06df72] dark:bg-[#0a0a0a]">
                    <div className="grid gap-2">
                        <Label htmlFor="year">Year</Label>
                        <Select value={year.toString()} onValueChange={(value) => setYear(value)}>
                            <SelectTrigger id="year">
                                <SelectValue placeholder="Select Year" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem key={0} value={'0'}>All</SelectItem>
                                {Array.from({ length: 10 }, (_, i) => {
                                    const yearOption = new Date().getFullYear() - i;
                                    return (
                                        <SelectItem key={yearOption} value={yearOption.toString()}>
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
                        <Select value={month.toString()} onValueChange={(value) => setMonth(value)}>
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
                        {/* <InputError message={errors.email} /> */}
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="patient_name">Patient Name</Label>
                        <Input
                            id="patient_name"
                            type="text"
                            name="patient_name"
                            required
                            autoFocus
                            tabIndex={3}
                            autoComplete="false"
                            placeholder='Patient name'
                            value={patientName} onChange={(e) => setPatientName(e.target.value)}
                        />
                        {/* <InputError message={errors.email} /> */}
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="patient_contact">Patient Contact</Label>
                        <Input
                            id="patient_contact"
                            type="text"
                            name="patient_contact"
                            required
                            autoFocus
                            tabIndex={3}
                            autoComplete="false"
                            placeholder='Patient name'
                            value={patientContact} onChange={(e) => setPatientContact(e.target.value)}
                        />
                        {/* <InputError message={errors.email} /> */}
                    </div>
                </div>
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-0 bg-white dark:bg-neutral-950 text-[#1c398e]">
                    <table className='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-neutral-950 dark:text-gray-400 text-left'>
                        <thead>
                            <tr>
                                <th scope="col" className="px-6 py-3">Info</th>
                                <th scope="col" className="px-6 py-3">Contact</th>
                                <th scope="col" className="px-6 py-3">Departmental Records</th>
                                <th scope="col" className="px-6 py-3">Others</th>
                            </tr>
                        </thead>
                        <tbody>
                            {patientsPaginated.data.map((p) => {

                                let explodedPsid = p.ps_number.split('/');

                                return (
                                    <tr key={p.id} className='bg-white border-b dark:bg-neutral-800 dark:border-neutral-950 border-gray-200'>
                                        <th scope="row" className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white flex flex-col">
                                            <Link href={patientsRegisterPsNumber({
                                                year: explodedPsid[1] || '',
                                                month: explodedPsid[2] || '',
                                                number: explodedPsid[3] || ''
                                            }).url} ><span className='text-blue-500'>PS# {p.ps_number}</span></Link>
                                            <span>Name: {p.name} {(
                                                p.guardian && p.relation
                                                ) && <span>({p.relation} of {p.guardian})</span>}</span>
                                            <span>Gender: {p.gender}</span>
                                        </th>
                                        <td className="px-6 py-4">
                                            <span>{p.contact}</span>
                                            <span>{p.cnic}</span>
                                        </td>
                                        <td className="px-6 py-4 flex flex-row">
                                            {['OPD','IND','EMR','PTH','XRY','RAD'].map((itm) => {
                                                return <Link href={patientsRegisterPsNumberDepartment({
                                                        year: explodedPsid[1] || '',
                                                        month: explodedPsid[2] || '',
                                                        number: explodedPsid[3] || '',
                                                        departmentKey: itm
                                                    }).url} ><span className='text-blue-500'>{itm}</span></Link>
                                            })}
                                        </td>
                                        <td className="px-6 py-4"></td>
                                    </tr>
                                );
                            })}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colSpan={4} className='text-right'>

                                    
                                    {
                                        (() => {
                                            const current = patientsPaginated.current_page;
                                            const last = patientsPaginated.last_page;

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
                                                `?page=${page}&year=${year}&month=${month}`;

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


function getMonthAgainstNumer(monthString = '00'){
    switch (monthString){
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