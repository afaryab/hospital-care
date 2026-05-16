import DeptPatientForm from '@/elements/dept-portal/DeptPatientForm';
import AppLayout from '@/layouts/app-layout';
import { apiDntSaveTreatment, apiDntUpdateStatus, dntDashboard, dntPatient } from '@/routes';
import { Head, usePage } from '@inertiajs/react';

export default function DntPatient() {
    const { serviceOrder, previousVisits } = usePage<any>().props;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/' },
        { title: 'Dental', href: dntDashboard().url },
        { title: serviceOrder.patient?.name ?? 'Patient', href: dntPatient({ id: serviceOrder.id }).url },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`DNT — ${serviceOrder.patient?.name ?? 'Patient'}`} />
            <DeptPatientForm
                deptName="Dental"
                dashboardUrl={dntDashboard().url}
                saveApiUrl={apiDntSaveTreatment({ serviceOrder: serviceOrder.id }).url}
                updateStatusUrl={apiDntUpdateStatus({ serviceOrder: serviceOrder.id }).url}
                serviceOrder={serviceOrder}
                previousVisits={previousVisits ?? []}
            />
        </AppLayout>
    );
}
