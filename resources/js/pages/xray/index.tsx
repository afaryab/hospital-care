import DeptQueueDashboard from '@/elements/dept-portal/DeptQueueDashboard';
import AppLayout from '@/layouts/app-layout';
import { xrayDashboard, xrayPatient, xraySearch } from '@/routes';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs = [{ title: 'Dashboard', href: '/' }, { title: 'Radiology', href: xrayDashboard().url }];

export default function XrayDashboard() {
    const { isXrayTech, recentOrders, todayStats, flash } = usePage<any>().props;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Radiology / X-Ray Portal" />
            <DeptQueueDashboard
                deptName="Radiology / X-Ray"
                accentClass="text-orange-600"
                hasAccess={isXrayTech}
                orders={recentOrders ?? []}
                stats={todayStats ?? { open: 0, in_progress: 0, treated: 0, total: 0 }}
                noAccessMessage="You need an X-Ray Technician profile to access this portal."
                searchUrl={xraySearch().url}
                patientUrl={(id) => xrayPatient({ id }).url}
                flashError={(flash as any)?.searchError}
            />
        </AppLayout>
    );
}
