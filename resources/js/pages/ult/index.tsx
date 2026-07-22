import DeptQueueDashboard from '@/elements/dept-portal/DeptQueueDashboard';
import AppLayout from '@/layouts/app-layout';
import {
    apiUltMyQueue,
    apiUltSearch,
    ultDashboard,
    ultPatient,
} from '@/routes';
import { Head, usePage } from '@inertiajs/react';
import { ScanLine } from 'lucide-react';

const breadcrumbs = [
    { title: 'Dashboard', href: '/' },
    { title: 'Ultrasound', href: ultDashboard().url },
];

export default function UltDashboard() {
    const { isUltDoctor, recentOrders, todayStats, flash } =
        usePage<any>().props;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Ultrasound Portal" />
            <DeptQueueDashboard
                deptName="Ultrasound"
                accentColor="bg-teal-600"
                accentClass="text-teal-600"
                icon={<ScanLine className="h-6 w-6" />}
                hasAccess={isUltDoctor}
                orders={recentOrders ?? []}
                stats={
                    todayStats ?? {
                        open: 0,
                        in_progress: 0,
                        treated: 0,
                        total: 0,
                    }
                }
                noAccessMessage="You need an Ultrasound Doctor profile to access this portal."
                patientUrl={(id) => ultPatient({ id }).url}
                myQueueUrl={apiUltMyQueue().url}
                searchApiUrl={apiUltSearch().url}
                searchTypes={['ULT']}
                flashError={(flash as any)?.searchError}
            />
        </AppLayout>
    );
}
