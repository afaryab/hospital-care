import DeptPatientForm from '@/elements/dept-portal/DeptPatientForm';
import AppLayout from '@/layouts/app-layout';
import {
    apiLabDeleteAttachment,
    apiLabSaveTreatment,
    apiLabUpdateStatus,
    apiLabUploadAttachment,
    labDashboard,
    labPatient,
} from '@/routes';
import { Head, usePage } from '@inertiajs/react';

export default function LabPatient() {
    const { serviceOrder, previousVisits, formConfig } = usePage<any>().props;
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
                showVitals={formConfig?.showVitals ?? false}
                showExamFindings={formConfig?.showExamFindings ?? false}
                showPrescriptions={formConfig?.showPrescriptions ?? false}
                showFollowUp={formConfig?.showFollowUp ?? false}
                showAttachments={formConfig?.showAttachments ?? true}
                chiefComplaintLabel="Test Requested / Clinical Indication"
                treatmentPlanLabel="Lab Results"
                treatmentPlanPlaceholder={`Enter results here. Example:\nHb: 12.5 g/dL\nWBC: 8,000/μL\nPlatelets: 220,000/μL\nBlood Sugar (F): 95 mg/dL`}
                uploadAttachmentUrl={apiLabUploadAttachment({ serviceOrder: serviceOrder.id }).url}
                deleteAttachmentUrl={(attachmentId) => apiLabDeleteAttachment({ attachment: attachmentId }).url}
            />
        </AppLayout>
    );
}
