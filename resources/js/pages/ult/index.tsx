import DeptQueueDashboard from '@/elements/dept-portal/DeptQueueDashboard';
import AppLayout from '@/layouts/app-layout';
import { ultDashboard, ultPatient, ultSearch } from '@/routes';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs = [{ title: 'Dashboard', href: '/' }, { title: 'Ultrasound', href: ultDashboard().url }];

export default function UltDashboard() {
    const { isUltDoctor, recentOrders, todayStats, flash } = usePage<any>().props;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Ultrasound Portal" />
            <DeptQueueDashboard
                deptName="Ultrasound"
                accentClass="text-teal-600"
                hasAccess={isUltDoctor}
                orders={recentOrders ?? []}
                stats={todayStats ?? { open: 0, in_progress: 0, treated: 0, total: 0 }}
                noAccessMessage="You need an Ultrasound Doctor profile to access this portal."
                searchUrl={ultSearch().url}
                patientUrl={(id) => ultPatient({ id }).url}
                flashError={(flash as any)?.searchError}
            />
        </AppLayout>
    );
}
