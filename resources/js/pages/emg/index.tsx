import DeptQueueDashboard from '@/elements/dept-portal/DeptQueueDashboard';
import AppLayout from '@/layouts/app-layout';
import {
    apiEmgMyQueue,
    apiEmgSearch,
    emgDashboard,
    emgPatient,
} from '@/routes';
import { Head, usePage } from '@inertiajs/react';
import { Siren } from 'lucide-react';

const breadcrumbs = [
    { title: 'Dashboard', href: '/' },
    { title: 'Emergency', href: emgDashboard().url },
];

export default function EmgDashboard() {
    const { isEmgDoctor, isDoctorScoped, recentOrders, todayStats, flash } =
        usePage<any>().props;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Emergency Portal" />
            <DeptQueueDashboard
                deptName="Emergency"
                accentColor="bg-red-600"
                accentClass="text-red-600"
                icon={<Siren className="h-6 w-6" />}
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
                noAccessMessage="You need an Emergency Doctor or Nursing Staff profile to access this portal."
                patientUrl={(id) => emgPatient({ id }).url}
                myQueueUrl={apiEmgMyQueue().url}
                searchApiUrl={apiEmgSearch().url}
                searchTypes={['EMG']}
                doctorScoped={isDoctorScoped}
                showCallButton={false}
                flashError={(flash as any)?.searchError}
            />
        </AppLayout>
    );
}
