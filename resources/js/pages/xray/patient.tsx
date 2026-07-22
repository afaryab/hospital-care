import DeptPatientForm from '@/elements/dept-portal/DeptPatientForm';
import AppLayout from '@/layouts/app-layout';
import {
    apiXrayDeleteAttachment,
    apiXraySaveTreatment,
    apiXrayUpdateStatus,
    apiXrayUploadAttachment,
    xrayDashboard,
    xrayPatient,
} from '@/routes';
import { Head, usePage } from '@inertiajs/react';

export default function XrayPatient() {
    const { serviceOrder, previousVisits, formConfig } = usePage<any>().props;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/' },
        { title: 'Radiology', href: xrayDashboard().url },
        {
            title: serviceOrder.patient?.name ?? 'Patient',
            href: xrayPatient({ id: serviceOrder.id }).url,
        },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`XRAY — ${serviceOrder.patient?.name ?? 'Patient'}`} />
            <DeptPatientForm
                deptName="Radiology / X-Ray"
                accentColor="bg-orange-600"
                dashboardUrl={xrayDashboard().url}
                saveApiUrl={
                    apiXraySaveTreatment({ serviceOrder: serviceOrder.id }).url
                }
                updateStatusUrl={
                    apiXrayUpdateStatus({ serviceOrder: serviceOrder.id }).url
                }
                serviceOrder={serviceOrder}
                previousVisits={previousVisits ?? []}
                showVitals={formConfig?.showVitals ?? false}
                showExamFindings={formConfig?.showExamFindings ?? false}
                showPrescriptions={formConfig?.showPrescriptions ?? false}
                showFollowUp={formConfig?.showFollowUp ?? false}
                showAttachments={formConfig?.showAttachments ?? true}
                chiefComplaintLabel="Referral Reason / Clinical History"
                treatmentPlanLabel="Radiology Report"
                treatmentPlanPlaceholder={`TECHNIQUE:\n\nFINDINGS:\nLungs: \nCardiac silhouette: \nMediastinum: \nBones: \nOther: \n\nIMPRESSION:\n`}
                uploadAttachmentUrl={apiXrayUploadAttachment({ serviceOrder: serviceOrder.id }).url}
                deleteAttachmentUrl={(attachmentId) => apiXrayDeleteAttachment({ attachment: attachmentId }).url}
            />
        </AppLayout>
    );
}
