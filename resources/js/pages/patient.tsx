import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import PatientMiniCard from '@/elements/patient/mini-card';
import PatientTreatmentsHistoryCard from '@/elements/patient/treatments-history-card';
import HumanSimpleBody from '@/human/simple-body';
import AppLayout from '@/layouts/app-layout';
import { home, patientsRegister, patientsRegisterPsNumberDepartment } from '@/routes';
import { Patient, type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type PatientProps = {
    patientData: Patient;
    departmentKey: string;
    serviceOrder: any;
    serviceDepartments: any[];
}

export default function PatientView() {

    const { patientData , departmentKey, serviceOrder, serviceDepartments } = usePage<PatientProps>().props

    let breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Register',
            href: patientsRegister().url
        },
        {
            title: `Patient ${patientData?.ps_number} ${patientData?.name} `,
            href: ''
        }
    ];

    if(departmentKey){
        breadcrumbs.push({
            title: `Department ${departmentKey}`,
            href: patientsRegisterPsNumberDepartment({
                year: patientData?.year || '',
                month: patientData?.month || '',
                number: patientData?.number || '',
                departmentKey: departmentKey
            }).url
        });
    }

    if(serviceOrder){
        breadcrumbs.push({
            title: `Service Order ${serviceOrder.so_number}`,
            href: ''
        });
    }

    console.log(patientData);

    const explodedPsid = patientData?.ps_number.split('/');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Patient ${patientData?.ps_number} ${patientData?.name} `} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72] dark:bg-[#262626]">
                <PatientMiniCard patient={patientData} className='w-full'/>
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-0 bg-white dark:bg-neutral-950 text-[#1c398e]">
                    <div className='h-full grid gap-6 grid-cols-2 xl:grid-cols-6 divide-x divide-gray-200'>
                        <div className='col-span-1 xl:col-span-2 divide-y divide-gray-200'>
                            {serviceDepartments.map((dept: any) => (
                                <Link href={patientsRegisterPsNumberDepartment({
                                    year: explodedPsid[1] || '',
                                    month: explodedPsid[2] || '',
                                    number: explodedPsid[3] || '',
                                    departmentKey: dept.slug
                                }).url} key={dept.slug} className='flex flex-row gap-2 p-4 cursor-pointer'>
                                    <img src={dept.image} alt={dept.name} className='w-12 h-12'/>
                                    <div className='flex-1'>
                                        <h2 className='font-bold text-lg'>{dept.name}</h2>
                                        <span className='font-bold text-sm'>{dept.slug}</span>
                                    </div>
                                </Link>
                            ))}
                        </div>
                        <div className='col-span-1 xl:col-span-4 grid gap-6 grid-cols-2'>
                            <div className='hidden xl:block'>
                                <PatientTreatmentsHistoryCard patient={patientData} departmentKey={departmentKey} className='h-full'/>
                            </div>
                            <div className='col-span-2 xl:col-span-1'>
                                {
                                    serviceOrder ? (
                                        <div className='p-4 border border-gray-200 rounded-lg'>
                                            <h2 className='font-bold text-lg mb-2'>Service Order Details</h2>
                                            <p><span className='font-bold'>SO Number:</span> {serviceOrder.so_number}</p>
                                            <p><span className='font-bold'>Department:</span> {serviceOrder?.department?.name}</p>
                                            {/* <p><span className='font-bold'>Service:</span> {serviceOrder.service_name}</p>
                                            <p><span className='font-bold'>Status:</span> {serviceOrder.status}</p> */}
                                        </div>
                                    ) : (
                                        <></>
                                    )
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
