import DeptPatientForm from '@/elements/dept-portal/DeptPatientForm';
import AppLayout from '@/layouts/app-layout';
import { apiUltSaveTreatment, apiUltUpdateStatus, ultDashboard, ultPatient } from '@/routes';
import { Head, usePage } from '@inertiajs/react';

export default function UltPatient() {
    const { serviceOrder, previousVisits } = usePage<any>().props;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/' },
        { title: 'Ultrasound', href: ultDashboard().url },
        { title: serviceOrder.patient?.name ?? 'Patient', href: ultPatient({ id: serviceOrder.id }).url },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`ULT — ${serviceOrder.patient?.name ?? 'Patient'}`} />
            <DeptPatientForm
                deptName="Ultrasound"
                accentColor="bg-teal-600"
                dashboardUrl={ultDashboard().url}
                saveApiUrl={apiUltSaveTreatment({ serviceOrder: serviceOrder.id }).url}
                updateStatusUrl={apiUltUpdateStatus({ serviceOrder: serviceOrder.id }).url}
                serviceOrder={serviceOrder}
                previousVisits={previousVisits ?? []}
                showVitals={false}
                showExamFindings={false}
                showPrescriptions={false}
                showFollowUp={false}
                chiefComplaintLabel="Referral Reason"
                treatmentPlanLabel="Ultrasound Report"
                treatmentPlanPlaceholder={`FINDINGS:\nLiver: \nGallbladder: \nKidneys: \nBladder: \nUterus/Prostate: \nOther: \n\nIMPRESSION:\n`}
            />
        </AppLayout>
    );
}
