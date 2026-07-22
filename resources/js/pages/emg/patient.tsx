import DeptPatientForm from '@/elements/dept-portal/DeptPatientForm';
import AppLayout from '@/layouts/app-layout';
import {
    apiEmgDeleteAttachment,
    apiEmgSaveTreatment,
    apiEmgUpdateStatus,
    apiEmgUploadAttachment,
    emgDashboard,
    emgPatient,
} from '@/routes';
import { Head, usePage } from '@inertiajs/react';

export default function EmgPatient() {
    const { serviceOrder, previousVisits, formConfig, triages } =
        usePage<any>().props;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/' },
        { title: 'Emergency', href: emgDashboard().url },
        {
            title: serviceOrder.patient?.name ?? 'Patient',
            href: emgPatient({ id: serviceOrder.id }).url,
        },
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`EMG — ${serviceOrder.patient?.name ?? 'Patient'}`} />
            <DeptPatientForm
                deptName="Emergency"
                accentColor="bg-red-600"
                dashboardUrl={emgDashboard().url}
                saveApiUrl={
                    apiEmgSaveTreatment({ serviceOrder: serviceOrder.id }).url
                }
                updateStatusUrl={
                    apiEmgUpdateStatus({ serviceOrder: serviceOrder.id }).url
                }
                serviceOrder={serviceOrder}
                previousVisits={previousVisits ?? []}
                showVitals={formConfig?.showVitals ?? true}
                showExamFindings={formConfig?.showExamFindings ?? true}
                showPrescriptions={formConfig?.showPrescriptions ?? true}
                showFollowUp={formConfig?.showFollowUp ?? true}
                showTriage={formConfig?.showTriage ?? true}
                requireTreatmentTime={formConfig?.requireTreatmentTime ?? true}
                showAttachments={formConfig?.showAttachments ?? false}
                showDentalChart={formConfig?.showDentalChart ?? false}
                chiefComplaintLabel="Presenting Complaint / Triage"
                examSystems={[
                    'General',
                    'Airway',
                    'Breathing',
                    'Circulation',
                    'Neurological',
                    'Other',
                ]}
                triages={triages ?? []}
                uploadAttachmentUrl={
                    apiEmgUploadAttachment({ serviceOrder: serviceOrder.id })
                        .url
                }
                deleteAttachmentUrl={(attachmentId) =>
                    apiEmgDeleteAttachment({ attachment: attachmentId }).url
                }
            />
        </AppLayout>
    );
}
