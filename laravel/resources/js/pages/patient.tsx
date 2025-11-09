import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import HumanSimpleBody from '@/human/simple-body';
import AppLayout from '@/layouts/app-layout';
import { home, patientsRegister, patientsRegisterPsNumberDepartment } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Patient() {

    const { patientData , departmentKey, serviceDepartments } = usePage().props

    const breadcrumbs: BreadcrumbItem[] = [
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

    console.log(serviceDepartments);

    const explodedPsid = patientData?.ps_number.split('/');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Patient ${patientData?.ps_number} ${patientData?.name} `} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72]">
                <div className="flex flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white text-[#1c398e]">
                    <figure className="flex flex-col">
                        <h1>{patientData.ps_number}</h1>
                        <figcaption className="flex items-left justify-start space-x-6 my-2">
                            <img className="w-9 h-9" src={
                                patientData?.gender == 'm' ? '/img/male-blue.png' : (patientData?.gender == 'f' ? '/img/female-blue.png' : (patientData?.gender == 't' ? '/img/transgender-blue.png' : '/img/avatar.png'))
                            } alt="profile picture" />
                            <div className="space-y-0.5 font-medium dark:text-white text-left rtl:text-right flex-1">
                                <div>{patientData?.name}</div>
                                <small className="text-sm text-gray-500 dark:text-gray-400 ">{patientData?.contact ? patientData?.contact : 'Contact number is missing'}</small>
                            </div>
                            <div className='flex flex-col text-center px-3'>
                                <span className='font-bold'>Total visit</span>
                                <span>{patientData?.total_visit ? `${patientData?.total_visit} ago` : '? ago'}</span>
                            </div>
                            <div className='flex flex-col text-center px-3'>
                                <span className='font-bold'>Last visit</span>
                                <span>{patientData?.last_visit ? `${patientData?.last_visit} ago` : '? ago'}</span>
                            </div>
                            <div className='flex flex-col text-center px-3'>
                                <span className='font-bold'>Age</span>
                                <span>{patientData?.age ? `${patientData?.age} years` : '? years'}</span>
                            </div>
                        </figcaption>
                    </figure>
                </div>
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white text-[#1c398e]">
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
                                <HumanSimpleBody />
                            </div>
                            <div className='col-span-2 xl:col-span-1'>
                                Treatments will be listed here!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
