import DeptPatientForm from '@/elements/dept-portal/DeptPatientForm';
import AppLayout from '@/layouts/app-layout';
import {
    apiLabSaveTreatment,
    apiLabUpdateStatus,
    labDashboard,
    labPatient,
} from '@/routes';
import { Head, usePage } from '@inertiajs/react';

export default function LabPatient() {
    const { serviceOrder, previousVisits } = usePage<any>().props;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/' },
        { title: 'Laboratory', href: labDashboard().url },
        {
            title: serviceOrder.patient?.name ?? 'Patient',
            href: labPatient({ id: serviceOrder.id }).url,
        },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`LAB — ${serviceOrder.patient?.name ?? 'Patient'}`} />
            <DeptPatientForm
                deptName="Laboratory"
                accentColor="bg-violet-600"
                dashboardUrl={labDashboard().url}
                saveApiUrl={
                    apiLabSaveTreatment({ serviceOrder: serviceOrder.id }).url
                }
                updateStatusUrl={
                    apiLabUpdateStatus({ serviceOrder: serviceOrder.id }).url
                }
                serviceOrder={serviceOrder}
                previousVisits={previousVisits ?? []}
                showVitals={false}
                showExamFindings={false}
                showPrescriptions={false}
                showFollowUp={false}
                chiefComplaintLabel="Test Requested / Clinical Indication"
                treatmentPlanLabel="Lab Results"
                treatmentPlanPlaceholder={`Enter results here. Example:\nHb: 12.5 g/dL\nWBC: 8,000/μL\nPlatelets: 220,000/μL\nBlood Sugar (F): 95 mg/dL`}
            />
        </AppLayout>
    );
}
