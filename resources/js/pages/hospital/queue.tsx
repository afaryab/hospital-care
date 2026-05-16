import TablePagination from '@/components/ui/table-pagination';
import AppLayout from '@/layouts/app-layout';
import { counter, counterView, home } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

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
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Hospital OPD',
            href: counter().url,
        },
    ];

    const { serviceOrders } = usePage<opdQueuePageProps>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="OPD Hospital Queue" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-0 text-[#1c398e] dark:bg-neutral-950">
                    <table className="bg-gray-50 text-left text-xs text-gray-700 uppercase dark:bg-neutral-950 dark:text-gray-400">
                        <thead>
                            <tr>
                                <th scope="col" className="px-6 py-3">
                                    Info
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
                                    Closed At
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {serviceOrders?.data.length === 0 && (
                                <tr className="border-b border-gray-200 bg-white dark:border-neutral-950 dark:bg-neutral-800">
                                    <td
                                        colSpan={5}
                                        rowSpan={4}
                                        className="px-6 py-3 text-center"
                                    >
                                        No service orders found.
                                    </td>
                                </tr>
                            )}
                            {serviceOrders?.data.length > 0 &&
                                serviceOrders?.data.map((serviceOrder: any) => {
                                    const explodedPsid =
                                        serviceOrder.so_number.split('/');

                                    return (
                                        <tr
                                            key={serviceOrder.id}
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
                                                        CT#{' '}
                                                        {serviceOrder.ct_number}
                                                    </span>
                                                </Link>
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
                                <td colSpan={4}>
                                    <TablePagination
                                        currentPage={serviceOrders.current_page}
                                        lastPage={serviceOrders.last_page}
                                        makeHref={(page) => `?page=${page}`}
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
