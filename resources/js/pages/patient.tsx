import PatientMiniCard from '@/elements/patient/mini-card';
import PatientTreatmentsHistoryCard from '@/elements/patient/treatments-history-card';
import ServiceOrderView from '@/elements/serviceorder/service-order-view';
import AppLayout from '@/layouts/app-layout';
import {
    home,
    patientsRegister,
    patientsRegisterPsNumberDepartment,
} from '@/routes';
import { Patient, type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type PatientProps = {
    patientData: Patient;
    departmentKey: string;
    serviceOrder: any;
    serviceDepartments: any[];
};

export default function PatientView() {
    const { patientData, departmentKey, serviceOrder, serviceDepartments } =
        usePage<PatientProps>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Register',
            href: patientsRegister().url,
        },
        {
            title: `Patient ${patientData?.ps_number} ${patientData?.name} `,
            href: '',
        },
    ];

    if (departmentKey) {
        breadcrumbs.push({
            title: `Department ${departmentKey}`,
            href: patientsRegisterPsNumberDepartment({
                year: patientData?.year || '',
                month: patientData?.month || '',
                number: patientData?.number || '',
                departmentKey: departmentKey,
            }).url,
        });
    }

    if (serviceOrder) {
        breadcrumbs.push({
            title: `Service Order ${serviceOrder.so_number}`,
            href: '',
        });
    }

    const explodedPsid = patientData?.ps_number.split('/');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head
                title={`Patient ${patientData?.ps_number} ${patientData?.name} `}
            />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <PatientMiniCard patient={patientData} className="w-full" />
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-0 text-[#1c398e] dark:bg-neutral-950">
                    <div className="flex h-full flex-row divide-x divide-gray-200">
                        <div className="divide-y divide-gray-200">
                            {serviceDepartments.map((dept: any) => (
                                <Link
                                    href={
                                        patientsRegisterPsNumberDepartment({
                                            year: explodedPsid[1] || '',
                                            month: explodedPsid[2] || '',
                                            number: explodedPsid[3] || '',
                                            departmentKey: dept.slug,
                                        }).url
                                    }
                                    key={dept.slug}
                                    className="flex cursor-pointer flex-row gap-2 p-4"
                                >
                                    <img
                                        src={dept.image}
                                        alt={dept.name}
                                        className="h-12 w-12"
                                    />
                                    <div className="flex-1">
                                        <h2 className="text-lg font-bold">
                                            {dept.name}
                                        </h2>
                                        <span className="text-sm font-bold">
                                            {dept.slug}
                                        </span>
                                    </div>
                                </Link>
                            ))}
                        </div>
                        {serviceOrder ? (
                            <div className="flex-1 p-4">
                                <ServiceOrderView serviceOrder={serviceOrder} />
                            </div>
                        ) : (
                            <div className="grid flex-1 grid-cols-1 gap-6 p-4 xl:grid-cols-2">
                                <PatientTreatmentsHistoryCard
                                    patient={patientData}
                                    departmentKey={departmentKey}
                                    className="h-full"
                                />
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
