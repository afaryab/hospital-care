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
import { apiPatientsSearch, apiPatientsStore, counter, counterSelectDepartment, counterSelectDepartmentService, counterSelectPatient, counterView, home, patientsRegisterPsNumberDepartment, printTransaction, downloadTransaction, transactionStore } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/react';
import { AlertTriangle } from 'lucide-react';
import { useEffect, useState } from 'react';
import { clsx } from 'clsx';
import { patient } from '@/actions/App/Http/Controllers/WebController';
import BulletsWrapper from '@/elements/bullets-wrapper';
import PatientMiniCard from '@/elements/patient/mini-card';
import PatientTransactionsHistoryCard from '@/elements/patient/transactions-history-card';
import DepartmentMiniCard from '@/elements/department/mini-card';

export default function Counter() {

    const {selectedPatient, departments, departmentKey, openCounter, services, recesitation, existingServiceOrders} = usePage().props;

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

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Counter" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72] dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white dark:bg-neutral-950 text-gray-800 dark:text-white">
                    <BulletsWrapper bullets={bullets}>
                        {step === 1 && <StepOne openCounter={openCounter} />}
                        {step === 2 && <StepTwo openCounter={openCounter} patient={selectedPatient} departments={departments} />}
                        {step === 3 && <StepThree recesitation={recesitation} existingServiceOrders={existingServiceOrders} openCounter={openCounter} patient={selectedPatient} departments={departments} departmentKey={departmentKey} services={services} />}
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}



function StepThree({recesitation, existingServiceOrders, openCounter, patient, departments, departmentKey, services}:any) {

    const [selectedServices, setSelectedServices] = useState<string[]>([]);
    const [mriNumber, setMriNumber] = useState<string>('');
    const [paymentMethod, setPaymentMethod] = useState<string>('CASH');
    const [amountPaid, setAmountPaid] = useState<number>(0);
    const [validationErrors, setValidationErrors] = useState<any>({});
    const [serviceProviders, setServiceProviders] = useState<any>({});
    const [selectedServiceOrder, setSelectedServiceOrder] = useState<string>();

    const [formData, setFormData] = useState<any>({
        total: 0,
        items: []
    });

    const calculateChange = () => {
        return Math.max(0, amountPaid - formData.total);
    };

    const validatedInput = (billData:any) => {
        // Patient ID must be set
        if(!billData.patient_id){
            validationErrors.patient_id = ['Patient ID is required.'];
            setValidationErrors(validationErrors);
            return false;
        }

        // Check each item if sevice have providers then provider must be selected
        for(const item of billData.items){
            const service = services.find((s:any) => s.id == item.service_id);
            if(service && service.have_service_provider && !item.provider_id){
                validationErrors[`provider_id_${item.service_id}`] = ['Provider is required for this service.'];
                setValidationErrors(validationErrors);
                return false;
            }
        }
        return true;
    }

    const generateBill = async () => {
        // Clear previous validation errors
        setValidationErrors({});

        if(recesitation && selectedServiceOrder === ''){
            alert('Please enter MRI number for recesitation services.');
            return;
        }
        
        try {
            const billData = {
                patient_id: patient.id,
                patient_year: patient.year,
                patient_month: patient.month,
                patient_number: patient.number,
                department_key: departmentKey,
                service_order_id: selectedServiceOrder || null,
                income_or_expense: 'INCOME',
                items: formData.items.map((item: any) => ({
                    service_id: item.serviceId,
                    service_name: item.name,
                    quantity: item.quantity,
                    unit_price: item.charges,
                    total: item.total || (item.quantity * item.charges),
                    provider_id: serviceProviders[item.serviceId] || null
                })),
                total_amount: formData.total,
                payment_method: paymentMethod,
                amount_paid: amountPaid,
                change_amount: calculateChange()
            };

            if(!validatedInput(billData)){  
                alert('Please fix the validation errors before generating the bill.');
                return;
            }

            router.post(transactionStore().url, billData, {
                onSuccess: (response) => {
                    console.log('Bill generated successfully:', response);
                    
                    // Create a simple success message with PDF options
                    // const now = response.url;
                    // const year = ;
                    // const month = String(now.getMonth() + 1).padStart(2, '0');
                    // const day = String(now.getDate()).padStart(2, '0');
                    // // Use current timestamp as transaction number if not available
                    // const number = now.getTime();
                    
                    // setTimeout(() => {
                    //     window.open(printTransaction.url({year, month, day, number}), '_blank', 'width=800,height=600,scrollbars=yes');
                    // }, 1000);
                    
                    setValidationErrors({});
                },
                onError: (errors) => {
                    console.error('Validation errors:', errors);
                    setValidationErrors(errors);
                    
                    // Show a general error message
                    const errorMessages = Object.values(errors).flat();
                    alert(`Please fix the following errors:\n${errorMessages.join('\n')}`);
                },
                onFinish: () => {
                    console.log('Request completed');
                }
            });
        } catch (error) {
            console.error('Error generating bill:', error);
            alert('Error generating bill. Please try again.');
        }
    };

    const updateItemQuantityAndCharges = (serviceId: string, quantity: number, charges: number) => {
        setFormData((prevData: any) => {
            const updatedItems = prevData.items.map((item: any) => {
                if (item.serviceId === serviceId) {
                    return {
                        ...item,
                        quantity: quantity,
                        charges: charges,
                        total: quantity * charges
                    };
                }
                return item;
            });

            const newTotal = updatedItems.reduce((sum: number, item: any) => sum + (item.total || 0), 0);

            return {
                ...prevData,
                items: updatedItems,
                total: newTotal
            };
        });
    };

    const updateServiceProvider = (serviceId: string, providerId: string) => {
        setServiceProviders((prev: any) => ({
            ...prev,
            [serviceId]: providerId
        }));
    };

    useEffect(() => {
        console.log(selectedServices);

        let totalCharges = 0;
        
        const customerSelectedServicesCartArray = selectedServices.map((serviceId:any) => {
            
            const sl = services.find((s:any) => s.id == serviceId);
            const itemCharges = sl?.charges || 0;
            const itemQuantity = 1;
            const itemTotal = itemQuantity * itemCharges;
            totalCharges += itemTotal;
            
            return {
                serviceId: sl?.id || '',
                name: sl?.name || '',
                quantity: itemQuantity,
                charges: itemCharges,
                total: itemTotal
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
            <div className='flex-1'>
                <h3 className='text-3xl mb-2 font-bold'>Add Bill</h3>
                <div className='flex-1 grid grid-cols-4 gap-4 w-full mb-2 '>
                    {departments.filter((department:any) => department.slug === departmentKey).map((department:any) => (
                        <DepartmentMiniCard department={department} patient={patient} className='h-full w-full border rounded-xl flex flex-col items-center justify-center' />
                    ))}
                    <PatientMiniCard patient={patient} className='col-span-3 w-full'/>
                </div>
                <div className='p-4 border dark:border-neutral-950 rounded-xl mb-2'>
                    {recesitation && <div className="grid gap-2 mb-2">
                        <Label htmlFor="service">MRI #</Label>
                        <Select name="mri_number" defaultValue={selectedServiceOrder} onValueChange={setSelectedServiceOrder}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select MRI number" />
                            </SelectTrigger>
                            <SelectContent>
                                {existingServiceOrders.map((order:any) => (<SelectItem value={order.id}>{order.so_number + ` - `+ order.service.name}</SelectItem>))}
                            </SelectContent>
                        </Select>
                    </div>}
                    <div className="grid gap-2">
                        <Label htmlFor="service">Service</Label>
                        <AdvancedTagSelect
                            options={services.map((service:any) => ({value: service.id, label: service.name}))}
                            value={selectedServices}
                            onValueChange={setSelectedServices}
                            placeholder="Select services..."
                        />
                        {/* <InputError message={errors.email} /> */}
                    </div>
                </div>
                <div className='p-4 border dark:border-neutral-950 rounded-xl mb-2'>
                    <div className="grid gap-2">
                        <table className="w-full text-left border">
                            <tbody>
                                <tr className="border-b dark:bg-neutral-950 dark:text-white rounded-tl-xl rounded-tr-xl">
                                    <td className="p-2 text-left">Product</td>
                                    <td className="p-2 text-right">Provider</td>
                                    {/* <td className="p-2 text-right">QTY</td> */}
                                    <td className="p-2 text-right">Total</td>
                                </tr>
                                {formData.items.length > 0 ? formData.items.map((item:any) => {
                                    const service = services.find((s:any) => s.id == item.serviceId);
                                    return (<BillItemsEditableTableRow 
                                        key={item.serviceId}
                                        service_name={item.name}
                                        serviceid={item.serviceId}
                                        quantity={item.quantity}
                                        charges={item.charges}
                                        service={service}
                                        selectedProvider={serviceProviders[item.serviceId] || ''}
                                        onUpdate={updateItemQuantityAndCharges}
                                        onProviderUpdate={updateServiceProvider}
                                        validationErrors={validationErrors}
                                    />);
                                }) : (
                                    <tr>
                                        <td colSpan={4} className="p-4 text-center text-gray-500 border dark:text-white dark:border-neutral-950 rounded-bl-xl rounded-br-xl">
                                            No services selected.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                        <div className="mt-4 flex justify-end">
                            <div className="w-full grid grid-cols-2 grid-col-1s gap-4">
                                <div>
                                    <Label htmlFor="payment_method">Payment Method</Label>
                                    <Select value={paymentMethod} onValueChange={setPaymentMethod}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select payment method" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="CASH">Cash</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError message={validationErrors.payment_method?.[0]} />
                                </div>

                                <div>

                                    <div>
                                        <Label htmlFor="total_amount">Total Amount</Label>
                                        <Input
                                            id="total_amount"
                                            type="text"
                                            name="total_amount"
                                            className="text-right font-semibold"
                                            value={`${formData.total.toFixed(2)}/- only`}
                                            readOnly
                                        />
                                        <InputError message={validationErrors.total_amount?.[0]} />
                                    </div>

                                    <div>
                                        <Label htmlFor="amount_paid">Amount Paid</Label>
                                        <Input
                                            id="amount_paid"
                                            type="number"
                                            name="amount_paid"
                                            className="text-right"
                                            value={amountPaid}
                                            onChange={(e) => setAmountPaid(parseFloat(e.target.value))}
                                            min={0}
                                            step={0.01}
                                            placeholder="0.00"
                                        />
                                        <InputError message={validationErrors.amount_paid?.[0]} />
                                    </div>

                                    <div>
                                        <Label htmlFor="change_amount">Change</Label>
                                        <Input
                                            id="change_amount"
                                            type="text"
                                            name="change_amount"
                                            className="text-right font-semibold bg-green-50"
                                            value={`${calculateChange().toFixed(2)}/- only`}
                                            readOnly
                                        />
                                    </div>

                                    <div className="pt-4">
                                        <Button 
                                            variant={'default'}
                                            onClick={generateBill}
                                            disabled={formData.items.length === 0 || amountPaid <= 0}
                                        >
                                            Generate Bill
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
}

function StepTwo({openCounter, patient, departments}:any) {
    return <div className='flex flex-row h-full w-full space-y-4'>
        <PatientTransactionsHistoryCard patient={patient} className='w-1/4' />
        <div className='flex-1 flex flex-col h-full w-full space-y-4 px-4'>
            <PatientMiniCard patient={patient} className='w-full'/>
            <h3 className='text-3xl mb-2 font-bold'>Departments</h3>
            <div className='grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 w-full'>
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
            <h3 className='text-3xl mb-2 font-bold'>Recesitation</h3>
            <div className='grid grid-cols-1 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 w-full'>
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
                    <Label htmlFor="patient_name" required={true}>Patient Name</Label>
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
                    <Label htmlFor="patient_contact" required={true}>Patient Contact</Label>
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
                    <Label htmlFor="patient_age" required={true}>Patient Age</Label>
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
                    <Label htmlFor="patient_gender" required={true}>Patient Gender</Label>
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
                    }).url} className='flex flex-row'>
                        <PatientMiniCard patient={p} className='w-full' />
                    </Link>)}
                
                </>}

                {patients.length > 0 && <>
                
                    <h3>Possible Matches</h3>

                    {patients.map((p, i) => <Link href={counterSelectDepartment({
                        pYear: p.year,
                        pMonth: p.month,
                        number: p.number
                    }).url} className='flex flex-row'>
                        <PatientMiniCard patient={p} className='w-full' />
                    </Link>)}

                </>}

                {(
                    patientName &&
                    patientContact &&
                    patientAge &&
                    patientGender
                    
                    ) && <div className='bg-[#06df72] dark:bg-[#0a0a0a] hover:bg-[#06df72] dark:bg-[#262626] text-white hover:text-[#1c398e] rounded-xl p-2 flex flex-col space-y-4 cursor-default'>
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


function BillItemsEditableTableRow({
    service_name,
    serviceid,
    quantity,
    charges,
    service,
    selectedProvider,
    onUpdate,
    onProviderUpdate,
    validationErrors
}:any) {

    const [q, setQ] = useState<number>(quantity);
    const [c, setC] = useState<number>(charges);
    const [sPErrors, setSPErrors] = useState<any>({});

    const handleQuantityChange = (newQuantity: number) => {
        setQ(newQuantity);
        onUpdate(serviceid, newQuantity, c);
    };

    const handleChargesChange = (newCharges: number) => {
        setC(newCharges);
        onUpdate(serviceid, q, newCharges);
    };

    const handleProviderChange = (providerId: string) => {
        onProviderUpdate(serviceid, providerId);
    };

    useEffect(() => {
        setQ(quantity);
        setC(charges);
    }, [quantity, charges]);


    return (
        <>
            <tr className="border-b border-neutral-950 dark:bg-neutral-700 dark:text-white">
                <td className="p-2">{service_name}</td>
                <td className="p-2 text-right">
                    {service?.available_providers && service.available_providers.length > 0 ? (<>
                        <Select value={selectedProvider} onValueChange={handleProviderChange}>
                            <SelectTrigger className="w-full">
                                <SelectValue placeholder="Select provider" />
                            </SelectTrigger>
                            <SelectContent>
                                {service.available_providers.map((provider: any) => (
                                    <SelectItem key={provider.id} value={provider.id.toString()}>
                                        {provider.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {!selectedProvider && (
                            <InputError message="Please select a provider." />
                        )}
                    </>) : (
                        <span className="text-gray-400 text-sm">N/A</span>
                    )}
                </td>
                {/* <td className="p-2 felx justify-end">
                    <Input 
                        type="number" 
                        name={`quantity_${serviceid}`} 
                        className='w-16' 
                        value={q} 
                        onChange={(e) => handleQuantityChange(parseInt(e.target.value) || 0)} 
                        min={1} 
                    />
                </td> */}
                <td className="p-2 text-right">
                    <Input 
                        type="number" 
                        name={`charges_${serviceid}`} 
                        className='w-24 text-right inline-block' 
                        value={c} 
                        onChange={(e) => handleChargesChange(parseFloat(e.target.value) || 0)} 
                        min={0} 
                        step={0.01}
                    />
                </td>
            </tr>
            {(validationErrors[`items.${serviceid}`] || validationErrors[`items.${serviceid}.provider_id`]) && (
                <tr className="flex">
                    <td colSpan={4} className="w-full p-2">
                        <div className="text-red-500 text-sm space-y-1">
                            {validationErrors[`items.${serviceid}`]?.map((error: string, index: number) => (
                                <div key={index}>{error}</div>
                            ))}
                            {validationErrors[`items.${serviceid}.provider_id`]?.map((error: string, index: number) => (
                                <div key={index}>{error}</div>
                            ))}
                        </div>
                    </td>
                </tr>
            )}
        </>
    );
}
