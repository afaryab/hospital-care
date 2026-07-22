import DeptQueueDashboard from '@/elements/dept-portal/DeptQueueDashboard';
import AppLayout from '@/layouts/app-layout';
import { apiDntMyQueue, apiDntSearch, dntDashboard, dntPatient } from '@/routes';
import { Head, usePage } from '@inertiajs/react';
import { BriefcaseMedical } from 'lucide-react';

const breadcrumbs = [{ title: 'Dashboard', href: '/' }, { title: 'Dental', href: dntDashboard().url }];

export default function DntDashboard() {
    const { isDentist, recentOrders, todayStats, flash } = usePage<any>().props;
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dental Portal" />
            <DeptQueueDashboard
                deptName="Dental"
                accentColor="bg-sky-600"
                accentClass="text-sky-600"
                icon={<BriefcaseMedical className="h-6 w-6" />}
                hasAccess={isDentist}
                orders={recentOrders ?? []}
                stats={todayStats ?? { open: 0, in_progress: 0, treated: 0, total: 0 }}
                noAccessMessage="You need a Dentist profile to access this portal."
                patientUrl={(id) => dntPatient({ id }).url}
                myQueueUrl={apiDntMyQueue().url}
                searchApiUrl={apiDntSearch().url}
                searchTypes={['DNT']}
                flashError={(flash as any)?.searchError}
            />
        </AppLayout>
    );
}
