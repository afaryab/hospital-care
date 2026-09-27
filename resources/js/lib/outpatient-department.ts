/**
 * Department context passed by OpdDoctorController to the shared outpatient
 * pages (opd/index, opd/patient) — lets OPD and Peds reuse the same screens.
 */
export type OutpatientDepartment = {
    type: string;
    label: string;
    profileLabel: string;
    dashboardUrl: string;
    patientUrlTemplate: string;
    apiMyQueueUrl: string;
    apiSearchUrl: string;
    apiSaveTreatmentUrlTemplate: string;
    apiStatusUrlTemplate: string;
};

export const withId = (template: string, id: number | string) =>
    template.replace('__ID__', String(id));
