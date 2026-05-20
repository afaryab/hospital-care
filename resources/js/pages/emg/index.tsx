import DeptQueueDashboard from '@/elements/dept-portal/DeptQueueDashboard';
import AppLayout from '@/layouts/app-layout';
import { emgDashboard, emgPatient, emgSearch } from '@/routes';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs = [
    { title: 'Dashboard', href: '/' },
    { title: 'Emergency', href: emgDashboard().url },
];

export default function EmgDashboard() {
    const { isEmgDoctor, recentOrders, todayStats, flash } =
        usePage<any>().props;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Emergency Portal" />
            <DeptQueueDashboard
                deptName="Emergency"
                accentClass="text-red-600"
                hasAccess={isEmgDoctor}
                orders={recentOrders ?? []}
                stats={
                    todayStats ?? {
                        open: 0,
                        in_progress: 0,
                        treated: 0,
                        total: 0,
                    }
                }
                noAccessMessage="You need an Emergency Doctor profile to access this portal."
                searchUrl={emgSearch().url}
                patientUrl={(id) => emgPatient({ id }).url}
                flashError={(flash as any)?.searchError}
            />
        </AppLayout>
    );
}
