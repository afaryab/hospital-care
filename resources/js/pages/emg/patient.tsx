import DeptPatientForm from '@/elements/dept-portal/DeptPatientForm';
import AppLayout from '@/layouts/app-layout';
import { apiEmgSaveTreatment, apiEmgUpdateStatus, emgDashboard, emgPatient } from '@/routes';
import { Head, usePage } from '@inertiajs/react';

export default function EmgPatient() {
    const { serviceOrder, previousVisits } = usePage<any>().props;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/' },
        { title: 'Emergency', href: emgDashboard().url },
        { title: serviceOrder.patient?.name ?? 'Patient', href: emgPatient({ id: serviceOrder.id }).url },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`EMG — ${serviceOrder.patient?.name ?? 'Patient'}`} />
            <DeptPatientForm
                deptName="Emergency"
                accentColor="bg-red-600"
                dashboardUrl={emgDashboard().url}
                saveApiUrl={apiEmgSaveTreatment({ serviceOrder: serviceOrder.id }).url}
                updateStatusUrl={apiEmgUpdateStatus({ serviceOrder: serviceOrder.id }).url}
                serviceOrder={serviceOrder}
                previousVisits={previousVisits ?? []}
                showVitals={true}
                showExamFindings={true}
                showPrescriptions={true}
                showFollowUp={true}
                chiefComplaintLabel="Presenting Complaint / Triage"
                examSystems={['General', 'Airway', 'Breathing', 'Circulation', 'Neurological', 'Other']}
            />
        </AppLayout>
    );
}
