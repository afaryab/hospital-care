import ReceaveAblesButton from '@/elements/receaveables/ReceaveAblesButton';
import AppLayout from '@/layouts/app-layout';
import { counter, home, patientsRegisterPsNumber } from '@/routes';
import { type BreadcrumbItem, type Panel, type PaymentMethod } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

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
    paymentMethods: PaymentMethod[];
    panelCompanies: Panel[];
    [key: string]: any;
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

    const { receaveables, paymentMethods, panelCompanies } =
        usePage<PageProps>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Receaveables - Counter" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-0 text-[#1c398e] dark:bg-neutral-950">
                    <table className="bg-gray-50 text-left text-xs text-gray-700 uppercase dark:bg-neutral-950 dark:text-gray-400">
                        <thead>
                            <tr>
                                <th scope="col" className="px-6 py-3">
                                    Patient
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
                            {receaveables.data.map((r: any) => {
                                const explodedPsid =
                                    r.patient.ps_number.split('/');

                                return (
                                    <tr
                                        key={r.id}
                                        className="border-b border-gray-200 bg-white dark:border-neutral-950 dark:bg-neutral-800"
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
                                            {r.closed_at}
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
                                <td colSpan={4} className="text-right">
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
                                            `?page=${page}`;

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
