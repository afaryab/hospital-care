import DeptQueueDashboard from '@/elements/dept-portal/DeptQueueDashboard';
import AppLayout from '@/layouts/app-layout';
import {
    apiLabMyQueue,
    apiLabSearch,
    labDashboard,
    labPatient,
} from '@/routes';
import { Head, usePage } from '@inertiajs/react';
import { FlaskConical } from 'lucide-react';

const breadcrumbs = [
    { title: 'Dashboard', href: '/' },
    { title: 'Laboratory', href: labDashboard().url },
];

export default function LabDashboard() {
    const { hasAccess, recentOrders, todayStats, searchPrefill, flash } =
        usePage<any>().props;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Laboratory Portal" />
            <DeptQueueDashboard
                deptName="Laboratory"
                accentColor="bg-violet-600"
                accentClass="text-violet-600"
                icon={<FlaskConical className="h-6 w-6" />}
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
                patientUrl={(id) => labPatient({ id }).url}
                myQueueUrl={apiLabMyQueue().url}
                searchApiUrl={apiLabSearch().url}
                searchTypes={['PTH']}
                searchPrefill={searchPrefill}
                doctorScoped={false}
                flashError={(flash as any)?.searchError}
            />
        </AppLayout>
    );
}
