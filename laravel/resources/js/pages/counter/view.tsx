import { Button } from '@/components/ui/button';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import BulletsWrapper from '@/elements/bullets-wrapper';
import HumanSimpleBody from '@/human/simple-body';
import AppLayout from '@/layouts/app-layout';
import { counterList, counterSelectPatient, counterView, home, patientsRegister, patientsRegisterPsNumberDepartment } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function CounterView() {

    const { openCounter } = usePage().props

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Counters',
            href: openCounter ? counterView({
                ctYear: openCounter.year,
                ctMonth: openCounter.month,
                ctNumber: openCounter.number
            }).url : counterList().url, 
        },
    ];

    let bullets = [];

    openCounter && bullets.push({ 
        title: openCounter && openCounter.ct_number,
        url: openCounter && counterView({
            ctYear: openCounter.year,
            ctMonth: openCounter.month,
            ctNumber: openCounter.number
        }).url
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Counter ${openCounter?.ct_number} `} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white text-[#1c398e]">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white text-[#1c398e]" >
                            <div className='grid grid-cols-1 md:grid-cols-3'>
                                <div>
                                    <div className='w-full text-left flex flex-col'>
                                        <span className='text-2xl font-bold'>Counter</span>
                                        <span className='text-sm'>CT Number: {openCounter?.ct_number}</span>
                                    </div>
                                    <h2>Counter Transactions</h2>
                                    <table className='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 text-left'>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div>

                                    <div className='w-full text-center flex flex-col'>
                                        <span className='text-2xl font-bold'>Counter</span>
                                        <span className='text-sm'>CT Number: {openCounter?.ct_number}</span>
                                    </div>

                                </div>
                                <div>

                                    {openCounter.status =='OPEN' && (
                                        <Link href={counterSelectPatient().url} className='w-full text-right flex flex-col'>
                                            <Button variant='default'>
                                                <span className='text-2xl font-bold'>Continue</span>
                                            </Button>
                                        </Link>
                                    )}
                                    
                                </div>
                            </div>
                        </div>
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}
