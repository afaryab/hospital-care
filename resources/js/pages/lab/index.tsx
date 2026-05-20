import DeptQueueDashboard from '@/elements/dept-portal/DeptQueueDashboard';
import AppLayout from '@/layouts/app-layout';
import { labDashboard, labPatient, labSearch } from '@/routes';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs = [
    { title: 'Dashboard', href: '/' },
    { title: 'Laboratory', href: labDashboard().url },
];

export default function LabDashboard() {
    const { hasAccess, recentOrders, todayStats, flash } = usePage<any>().props;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Laboratory Portal" />
            <DeptQueueDashboard
                deptName="Laboratory"
                accentClass="text-violet-600"
                hasAccess={hasAccess}
                orders={recentOrders ?? []}
                stats={
                    todayStats ?? {
                        open: 0,
                        in_progress: 0,
                        treated: 0,
                        total: 0,
                    }
                }
                noAccessMessage="You need a staff profile to access the Lab portal."
                searchUrl={labSearch().url}
                patientUrl={(id) => labPatient({ id }).url}
                flashError={(flash as any)?.searchError}
            />
        </AppLayout>
    );
}
