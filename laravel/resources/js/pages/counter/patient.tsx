import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { RadioInput } from '@/components/ui/input-radio';
import { Label } from '@/components/ui/label';
import { MaskInput } from '@/components/ui/mask-input';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import PatientHistoryMiniTree from '@/elements/history/patient-history-mini-tree';
import FindOrSelectPatient from '@/elements/patient/find-or-select-patient';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { MultiSelect } from '@/components/ui/multi-select';
import { AdvancedTagSelect } from '@/components/ui/tag-select';
import AppLayout from '@/layouts/app-layout';
import { apiPatientsSearch, apiPatientsStore, counter, counterSelectDepartment, counterSelectDepartmentService, counterSelectPatient, counterView, home, patientsRegisterPsNumberDepartment } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { AlertTriangle } from 'lucide-react';
import { useEffect, useState } from 'react';
import { clsx } from 'clsx';
import { patient } from '@/actions/App/Http/Controllers/WebController';
import BulletsWrapper from '@/elements/bullets-wrapper';
import PatientMiniCard from '@/elements/patient/mini-card';
import PatientTransactionsHistoryCard from '@/elements/patient/transactions-history-card';

export default function Counter() {

    const {selectedPatient, departments, departmentKey, openCounter, services} = usePage().props;

    const step = !selectedPatient ? 1 : (!departmentKey ? 2 : 3);

    let breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Counter',
            href: openCounter ? counterView({
                ctYear: openCounter.year,
                ctMonth: openCounter.month,
                ctNumber: openCounter.number
            }).url : counter().url,
        }
    ];
    

    let bullets = [
        { 
            title: openCounter && openCounter.ct_number,
            url: openCounter && counterView({
                ctYear: openCounter.year,
                ctMonth: openCounter.month,
                ctNumber: openCounter.number
            }).url,
            active: step === 1
        }];

    if(selectedPatient?.name){
        bullets.push({ 
            title: selectedPatient && selectedPatient?.ps_number,
            url: selectedPatient && counterSelectDepartment({
                pYear: selectedPatient.year,
                pMonth: selectedPatient.month,
                number: selectedPatient.number
            }).url,
            active: step === 2
        });
        breadcrumbs.push({
            title: selectedPatient?.name,
            href: selectedPatient && counterSelectDepartment({
                pYear: selectedPatient.year,
                pMonth: selectedPatient.month,
                number: selectedPatient.number
            }).url
        });
    }else{
        bullets.push({
            title: 'No patient selected',
            url: counterSelectPatient({
                ctYear: openCounter.year,
                ctMonth: openCounter.month,
                ctNumber: openCounter.number
            }).url,
            active: step === 2
        });
        breadcrumbs.push({
            title: 'Select Patient',
            href: counterSelectPatient({
                ctYear: openCounter.year,
                ctMonth: openCounter.month,
                ctNumber: openCounter.number
            }).url
        });
    }

    (selectedPatient?.name && departmentKey != '') && bullets.push({ 
        title: departmentKey != '' && `Departments (${departmentKey})`,
        url: (selectedPatient && departmentKey != '') && counterSelectDepartmentService({
            pYear: selectedPatient.year,
            pMonth: selectedPatient.month,
            number: selectedPatient.number,
            departmentKey: departmentKey as string
        }).url,
        active: step === 3
    });

    console.log(step, bullets, openCounter, selectedPatient, departmentKey, services);


    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Counter" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white text-gray-800">
                    <BulletsWrapper bullets={bullets}>
                        {step === 1 && <StepOne openCounter={openCounter} />}
                        {step === 2 && <StepTwo openCounter={openCounter} patient={selectedPatient} departments={departments} />}
                        {step === 3 && <StepThree openCounter={openCounter} patient={selectedPatient} departments={departments} departmentKey={departmentKey} services={services} />}
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}



function StepThree({openCounter, patient, departments, departmentKey, services}:any) {

    const [selectedServices, setSelectedServices] = useState<string[]>([]);

    const [formData, setFormData] = useState<any>({
        total: 0,
        items: []
    });

    useEffect(() => {
        console.log(selectedServices);

        let totalCharges = 0;
        
        const customerSelectedServicesCartArray = selectedServices.map((serviceId:any) => {
            
            const sl = services.find((s:any) => s.id == serviceId);
            totalCharges += sl?.charges || 0;
            return {
                serviceId: sl?.id || '',
                name: sl?.name || '',
                quantity: 1,
                charges: sl?.charges || 0,
            };
        }, {});

        const newFormData = {
            total: totalCharges,
            items: customerSelectedServicesCartArray
        };

        setFormData(newFormData);
    }, [selectedServices]);



    return <div className='flex flex-col h-full w-full space-y-4'>
        <div className='flex flex-row h-full w-full space-x-6'>
            <div>
                <h3 className='text-3xl mb-2 font-bold'>Add Bill</h3>
                <div className='flex-1 grid grid-cols-4 gap-4 w-full'>
                    {departments.filter((department:any) => department.slug === departmentKey).map((department:any) => (
                        <div key={department.id} className='flex flex-col items-left justify-start'>
                            <Link href={counterSelectDepartmentService({
                                pYear: patient.year,
                                pMonth: patient.month,
                                number: patient.number,
                                departmentKey: department.slug
                            }).url} className='h-full w-32 border rounded-xl flex flex-col items-center justify-center'>
                                <img src={department.image} alt={department.name} className='w-12 h-12 object-contain'/>
                                <span className='text-center text-sm mt-2 max-w-28'>{department.name}</span>
                            </Link>
                        </div>
                    ))}
                    <PatientMiniCard patient={patient} className='col-span-3 max-w-md'/>
                </div>
                <div className='py-4'>
                    <div className="grid gap-2">
                        <Label htmlFor="service">Service</Label>
                        <AdvancedTagSelect
                            options={services.map((service:any) => ({value: service.id, label: service.name}))}
                            value={selectedServices}
                            onValueChange={setSelectedServices}
                            placeholder="Select services..."
                            maxItems={2}
                        />
                        {/* <InputError message={errors.email} /> */}
                    </div>
                </div>
                <div className='py-4'>
                    <div className="grid gap-2">
                        <table className="w-full text-left border">
                            <thead>
                                <tr className="flex border-b">
                                    <th className="w-full p-2">Product</th>
                                    <th className="min-w-[44px] p-2">QTY</th>
                                    <th className="min-w-[74px] p-2">Total</th>
                                </tr>
                            </thead>
                            <tbody className='divide-y '>
                            {formData.items.map((item:any) => {
                                return (<tr key={item.serviceId} className="flex py-1">
                                    <td className="flex-1 p-2">{item.name}</td>
                                    <td className="">
                                        <Input type="number" name={`quantity_${item.serviceId}`} className='w-16' defaultValue={item.quantity} min={1} />
                                    </td>
                                    <td className="">
                                        <Input type="number" name={`charges_${item.serviceId}`} className='w-24' defaultValue={item.charges} min={1} />
                                    </td>
                                </tr>);
                            })}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div className='flex-shrink flex max-w-96 bg-gray-300 min-w-80 w-full inset-shadow-lg'>
                <div className="w-80 mx-auto my-auto rounded bg-gray-50 px-6 pt-8 shadow-lg">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/d/d5/Tailwind_CSS_Logo.svg" alt="chippz" className="mx-auto w-16 py-4" />
                    <div className="flex flex-col justify-center items-center gap-2">
                        <h4 className="font-semibold">Business Name</h4>
                        <p className="text-xs">Some address goes here</p>
                    </div>
                    <div className="flex flex-col gap-3 border-b py-6 text-xs">
                    <p className="flex justify-between">
                        <span className="text-gray-400">Receipt No.:</span>
                        <span>####</span>
                    </p>
                    <p className="flex justify-between">
                        <span className="text-gray-400">Order Type:</span>
                        <span>{departmentKey}</span>
                    </p>
                    <p className="flex justify-between">
                        <span className="text-gray-400">Patient:</span>
                        <span>{patient.name}</span>
                    </p>
                    <p className="flex justify-between">
                        <span className="text-gray-400">{patient.ps_number}</span>
                    </p>
                    <p className="flex justify-between">
                        <span className="text-gray-400">TR/{patient.ps_number}</span>
                    </p>
                    </div>
                    <div className="flex flex-col gap-3 pb-6 pt-2 text-xs">
                    <table className="w-full text-left">
                        <thead>
                        <tr className="flex">
                            <th className="w-full py-2">Product</th>
                            <th className="min-w-[44px] py-2">QTY</th>
                            <th className="min-w-[44px] py-2">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                            {formData.items.map((item:any) => (<tr className="flex">
                                <td className="flex-1 py-1">{item.name}</td>
                                <td className="min-w-[44px]">{item.quantity}</td>
                                <td className="min-w-[44px]">${item.total}</td>
                            </tr>))}
                        </tbody>
                    </table>
                    <div className=" border-b border border-dashed"></div>
                    <div className="py-4 justify-center items-center flex flex-col gap-2">
                        <p className="flex gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M21.3 12.23h-3.48c-.98 0-1.85.54-2.29 1.42l-.84 1.66c-.2.4-.6.65-1.04.65h-3.28c-.31 0-.75-.07-1.04-.65l-.84-1.65a2.567 2.567 0 0 0-2.29-1.42H2.7c-.39 0-.7.31-.7.7v3.26C2 19.83 4.18 22 7.82 22h8.38c3.43 0 5.54-1.88 5.8-5.22v-3.85c0-.38-.31-.7-.7-.7ZM12.75 2c0-.41-.34-.75-.75-.75s-.75.34-.75.75v2h1.5V2Z" fill="#000"></path><path d="M22 9.81v1.04a2.06 2.06 0 0 0-.7-.12h-3.48c-1.55 0-2.94.86-3.63 2.24l-.75 1.48h-2.86l-.75-1.47a4.026 4.026 0 0 0-3.63-2.25H2.7c-.24 0-.48.04-.7.12V9.81C2 6.17 4.17 4 7.81 4h3.44v3.19l-.72-.72a.754.754 0 0 0-1.06 0c-.29.29-.29.77 0 1.06l2 2c.01.01.02.01.02.02a.753.753 0 0 0 .51.2c.1 0 .19-.02.28-.06.09-.03.18-.09.25-.16l2-2c.29-.29.29-.77 0-1.06a.754.754 0 0 0-1.06 0l-.72.72V4h3.44C19.83 4 22 6.17 22 9.81Z" fill="#000"></path></svg> info@example.com</p>
                        <p className="flex gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"><path fill="#000" d="M11.05 14.95L9.2 16.8c-.39.39-1.01.39-1.41.01-.11-.11-.22-.21-.33-.32a28.414 28.414 0 01-2.79-3.27c-.82-1.14-1.48-2.28-1.96-3.41C2.24 8.67 2 7.58 2 6.54c0-.68.12-1.33.36-1.93.24-.61.62-1.17 1.15-1.67C4.15 2.31 4.85 2 5.59 2c.28 0 .56.06.81.18.26.12.49.3.67.56l2.32 3.27c.18.25.31.48.4.7.09.21.14.42.14.61 0 .24-.07.48-.21.71-.13.23-.32.47-.56.71l-.76.79c-.11.11-.16.24-.16.4 0 .08.01.15.03.23.03.08.06.14.08.2.18.33.49.76.93 1.28.45.52.93 1.05 1.45 1.58.1.1.21.2.31.3.4.39.41 1.03.01 1.43zM21.97 18.33a2.54 2.54 0 01-.25 1.09c-.17.36-.39.7-.68 1.02-.49.54-1.03.93-1.64 1.18-.01 0-.02.01-.03.01-.59.24-1.23.37-1.92.37-1.02 0-2.11-.24-3.26-.73s-2.3-1.15-3.44-1.98c-.39-.29-.78-.58-1.15-.89l3.27-3.27c.28.21.53.37.74.48.05.02.11.05.18.08.08.03.16.04.25.04.17 0 .3-.06.41-.17l.76-.75c.25-.25.49-.44.72-.56.23-.14.46-.21.71-.21.19 0 .39.04.61.13.22.09.45.22.7.39l3.31 2.35c.26.18.44.39.55.64.1.25.16.5.16.78z"></path></svg> +234XXXXXXXX</p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
}

function StepTwo({openCounter, patient, departments}:any) {
    return <div className='flex flex-row h-full w-full space-y-4'>
        <PatientTransactionsHistoryCard patient={patient} className='w-1/4 pr-6' />
        <div className='flex-1 flex flex-col h-full w-full space-y-4'>
            <PatientMiniCard patient={patient} className='max-w-md'/>
            <h3 className='text-3xl mb-2 font-bold'>Departments</h3>
            <div className='flex-1 grid grid-cols-6 gap-4 w-full'>
                {departments.map((department:any) => (
                    <div key={department.id} className='flex flex-col items-center justify-center'>
                        <Link href={counterSelectDepartmentService({
                            pYear: patient.year,
                            pMonth: patient.month,
                            number: patient.number,
                            departmentKey: department.slug
                        }).url} className='h-32 w-32 border rounded-xl flex flex-col items-center justify-center'>
                            <img src={department.image} alt={department.name} className='w-12 h-12 object-contain'/>
                            <span className='text-center text-sm mt-2 max-w-28'>{department.name}</span>
                        </Link>
                    </div>
                ))}
            </div>
            <h3 className='text-3xl mb-2 font-bold'>RECESITATION</h3>
            <div className='flex-1 grid grid-cols-6 gap-4 w-full'>
                {departments.filter((department:any) => department.have_composit_services).map((department:any) => (
                    <div key={department.id} className='flex flex-col items-center justify-center'>
                        <Link href={counterSelectDepartmentService({
                            pYear: patient.year,
                            pMonth: patient.month,
                            number: patient.number,
                            departmentKey: `RECES-${department.slug}`
                        }).url} className='h-32 w-32 border rounded-xl flex flex-col items-center justify-center'>
                            <img src={department.image} alt={department.name} className='w-12 h-12 object-contain'/>
                            <span className='text-center text-sm mt-2 max-w-28'>{department.name}</span>
                        </Link>
                    </div>
                ))}
            </div>
        </div>
    </div>
}

function StepOne({openCounter}:any) {


    const [patients, setPatients] = useState([]);
    const [exactMatch, setExactMatch] = useState([]);

    const [psInput , setPsInput] = useState<string>('');

    const psNumberIsChanged = (val:string, unmasked:string) =>{

        console.log(val, unmasked);
        setPsInput(val);

    }


    const [patientCnic , setPatientCnic] = useState<string>('');

    const patientCnicIsChanged = (val:string, unmasked:string) =>{

        console.log(val, unmasked);
        setPatientCnic(val);

    }

    const [patientName , setPatientName] = useState<string>('');
    const [patientContact , setPatientContact] = useState<string>('');
    const [patientAge , setPatientAge] = useState<string>('');
    const [patientGender , setPatientGender] = useState<string>('');

    useEffect(() => {
        fetchPatientsFromApi();
    }, [
        psInput,
        patientCnic,
        patientName,
        patientContact,
        patientAge,
        patientGender
    ])


    const fetchPatientsFromApi = async () => {
        try {
            const response = await fetch('/api/patients', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mr_number: psInput,
                    cnic_number: patientCnic,
                    patient_name: patientName,
                    patient_contact: patientContact,
                    patient_age: patientAge,
                    patient_gender: patientGender,
                }),
            });
            
            if (response.ok) {
                const data = await response.json();
                setPatients(data.data.possible);
                setExactMatch(data.data.exact);
            }
        } catch (error) {
            console.error('Error fetching patients:', error);
        }
    }

    const createPatientInApi = async () => {
        try {
            const response = await fetch(apiPatientsStore().url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cnic: patientCnic,
                    name: patientName,
                    contact: patientContact,
                    age: patientAge,
                    gender: patientGender,
                }),
            });

            if (response.ok) {
                const data = await response.json();
                // Handle the response data as needed
                console.log('Patient created successfully:', data);
                window.location.href = counterSelectDepartment({
                    pYear: data.data.year,
                    pMonth: data.data.month,
                    number: data.data.number
                }).url;
                
            }
        } catch (error) {
            console.error('Error creating patient:', error);
        }
    };

    useEffect(() => {
        console.log(patients);
    }, [patients])


    return <div className='h-full w-full grid grid-cols-2 divide-x divide-[#06df72]'>
        <div className='flex flex-col p-4 pr-8'>
            <div className='flex flex-col w-full space-y-4'>
                <h3 className='text-3xl mb-2 font-bold'>Select / Create Patient</h3>
                <div className="grid gap-2">
                    <Label htmlFor="mr_number">MR Number</Label>
                    <MaskInput
                        id="mr_number"
                        type="text"
                        name="mr_number"
                        required
                        autoFocus
                        tabIndex={100}
                        autoComplete="false"
                        mask="aa/9999/99/999999"
                        placeholder="--/----/--/------"
                        value={psInput} onValueChange={({ masked, unmasked }) => psNumberIsChanged(masked, unmasked)}
                    />
                    {/* <InputError message={errors.email} /> */}
                </div>
                <div className="grid gap-2">
                    <Label htmlFor="cnic_number">CNIC Number</Label>
                    <MaskInput
                        id="cnic_number"
                        type="text"
                        name="cnic_number"
                        required
                        autoFocus
                        tabIndex={2}
                        autoComplete="false"
                        mask="99999-99999999-9"
                        placeholder='----- ------- -'
                        value={patientCnic} onValueChange={({ masked, unmasked }) => patientCnicIsChanged(masked, unmasked)}
                    />
                    {/* <InputError message={errors.email} /> */}
                </div>
                <div className="grid gap-2">
                    <Label htmlFor="patient_name">Patient Name</Label>
                    <Input
                        id="patient_name"
                        type="text"
                        name="patient_name"
                        required
                        autoFocus
                        tabIndex={3}
                        autoComplete="false"
                        placeholder='Patient name'
                        value={patientName} onChange={(e) => setPatientName(e.target.value)}
                    />
                    {/* <InputError message={errors.email} /> */}
                </div>
                <div className="grid gap-2">
                    <Label htmlFor="patient_contact">Patient Contact</Label>
                    <MaskInput
                        id="patient_contact"
                        type="text"
                        name="patient_contact"
                        required
                        autoFocus
                        tabIndex={3}
                        autoComplete="false"
                        value={patientContact} 
                        mask="+99-9999-9999999"
                        placeholder="+92-0000-0000000"
                        onValueChange={({ masked, unmasked }) => setPatientContact(masked)}
                    />
                    {/* <InputError message={errors.email} /> */}
                </div>
                <div className="grid gap-2">
                    <Label htmlFor="patient_age">Patient Age</Label>
                    <Input
                        id="patient_age"
                        type="number"
                        name="patient_age"
                        required
                        autoFocus
                        tabIndex={3}
                        autoComplete="false"
                        placeholder='Patient age'
                        value={patientAge} onChange={(e) => setPatientAge(e.target.value)}
                    />
                    {/* <InputError message={errors.email} /> */}
                </div>
                <div className='grid gap-2'>
                    <Label htmlFor="patient_gender">Patient Gender</Label>
                    <div className='flex flex-row space-x-4'>
                        <Label htmlFor="patient_gender_m">
                            <RadioInput
                                id="patient_gender_m"
                                type="radio"
                                name="patient_gender"
                                required
                                autoFocus
                                tabIndex={4}
                                autoComplete="false"
                                value={'m'}
                                className='mr-2'
                                checked={patientGender === 'm'} onChange={(e) => setPatientGender(e.target.value)}
                            />
                            Male</Label>
                        <Label htmlFor="patient_gender_f">
                            <RadioInput
                                id="patient_gender_f"
                                type="radio"
                                name="patient_gender"
                                required
                                autoFocus
                                tabIndex={4}
                                autoComplete="false"
                                value={'f'}
                                className='mr-2'
                                checked={patientGender === 'f'} onChange={(e) => setPatientGender(e.target.value)}
                            />
                            Female</Label>
                        <Label htmlFor="patient_gender_t">
                            <RadioInput
                                id="patient_gender_t"
                                type="radio"
                                name="patient_gender"
                                required
                                autoFocus
                                tabIndex={4}
                                autoComplete="false"
                                value={'t'}
                                className='mr-2'
                                checked={patientGender === 't'} onChange={(e) => setPatientGender(e.target.value)}
                            />
                            Transgender</Label>
                    </div>
                    {/* <InputError message={errors.email} /> */}
                </div>
            </div>
        </div>
        <div className='flex flex-col p-4 pr-8 space-y-4'>
            <div className='flex flex-col w-full space-y-4'>

                {exactMatch.length > 0 && <>
                    
                    <h3>Exact Match found</h3>

                    {exactMatch.map((p) => <Link href={counterSelectDepartment({
                        pYear: p.year,
                        pMonth: p.month,
                        number: p.number
                    }).url} className='bg-[#1c398e] hover:bg-[#06df72] text-white hover:text-[#1c398e] rounded-xl p-1 flex flex-row'>
                        <PatientMiniCard patient={p} className='w-full' />
                    </Link>)}
                
                </>}

                {patients.length > 0 && <>
                
                    <h3>Possible Matches</h3>

                    {patients.map((p, i) => <Link href={counterSelectDepartment({
                        pYear: p.year,
                        pMonth: p.month,
                        number: p.number
                    }).url} className='bg-[#1c398e] hover:bg-[#06df72] text-white hover:text-[#1c398e] rounded-xl p-1 flex flex-row'>
                        <PatientMiniCard patient={p} className='w-full' />
                    </Link>)}

                </>}

                {(
                    patientName &&
                    patientContact
                    
                    ) && <div className='bg-[#1c398e] hover:bg-[#06df72] text-white hover:text-[#1c398e] rounded-xl p-2 flex flex-col space-y-4 cursor-default'>
                    <PatientMiniCard patient={{name: patientName, gender: patientGender, ps_number: psInput, contact: patientContact, cnic: patientCnic, age: patientAge}} className='w-full' />
                    <div className='items-right justify-end'>
                        <Button onClick={() => createPatientInApi()}>
                            <span>Create New Patient</span>
                        </Button>
                    </div>
                </div>}



            </div>
        </div>
    </div>
}