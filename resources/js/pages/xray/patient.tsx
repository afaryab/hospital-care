import DeptPatientForm from '@/elements/dept-portal/DeptPatientForm';
import AppLayout from '@/layouts/app-layout';
import { apiXraySaveTreatment, apiXrayUpdateStatus, xrayDashboard, xrayPatient } from '@/routes';
import { Head, usePage } from '@inertiajs/react';

export default function XrayPatient() {
    const { serviceOrder, previousVisits } = usePage<any>().props;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/' },
        { title: 'Radiology', href: xrayDashboard().url },
        { title: serviceOrder.patient?.name ?? 'Patient', href: xrayPatient({ id: serviceOrder.id }).url },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`XRAY — ${serviceOrder.patient?.name ?? 'Patient'}`} />
            <DeptPatientForm
                deptName="Radiology / X-Ray"
                dashboardUrl={xrayDashboard().url}
                saveApiUrl={apiXraySaveTreatment({ serviceOrder: serviceOrder.id }).url}
                updateStatusUrl={apiXrayUpdateStatus({ serviceOrder: serviceOrder.id }).url}
                serviceOrder={serviceOrder}
                previousVisits={previousVisits ?? []}
            />
        </AppLayout>
    );
}
