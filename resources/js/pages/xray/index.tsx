import DeptQueueDashboard from '@/elements/dept-portal/DeptQueueDashboard';
import AppLayout from '@/layouts/app-layout';
import {
    apiXrayMyQueue,
    apiXraySearch,
    xrayDashboard,
    xrayPatient,
} from '@/routes';
import { Head, usePage } from '@inertiajs/react';
import { Radiation } from 'lucide-react';

const breadcrumbs = [
    { title: 'Dashboard', href: '/' },
    { title: 'Radiology', href: xrayDashboard().url },
];

export default function XrayDashboard() {
    const { isXrayTech, recentOrders, todayStats, searchPrefill, flash } =
        usePage<any>().props;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Radiology / X-Ray Portal" />
            <DeptQueueDashboard
                deptName="Radiology / X-Ray"
                accentColor="bg-orange-600"
                accentClass="text-orange-600"
                icon={<Radiation className="h-6 w-6" />}
                hasAccess={isXrayTech}
                orders={recentOrders ?? []}
                stats={
                    todayStats ?? {
                        open: 0,
                        in_progress: 0,
                        treated: 0,
                        total: 0,
                    }
                }
                noAccessMessage="You need an X-Ray Technician profile to access this portal."
                patientUrl={(id) => xrayPatient({ id }).url}
                myQueueUrl={apiXrayMyQueue().url}
                searchApiUrl={apiXraySearch().url}
                searchTypes={['XRAY', 'RAD']}
                searchPrefill={searchPrefill}
                flashError={(flash as any)?.searchError}
            />
        </AppLayout>
    );
}
