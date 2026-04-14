// @ts-nocheck
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { RadioInput } from '@/components/ui/input-radio';
import { Label } from '@/components/ui/label';
import { MaskInput } from '@/components/ui/mask-input';
import { Spinner } from '@/components/ui/spinner';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { AdvancedTagSelect } from '@/components/ui/tag-select';
import BulletsWrapper from '@/elements/bullets-wrapper';
import DepartmentMiniCard from '@/elements/department/mini-card';
import PatientMiniCard from '@/elements/patient/mini-card';
import PatientHistorySideBar from '@/elements/patient/transactions-history-card';
import AppLayout from '@/layouts/app-layout';
import {
    apiPatientsStore,
    counter,
    counterSelectDepartment,
    counterSelectDepartmentService,
    counterSelectPatient,
    counterView,
    home,
    transactionStore,
} from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import { LoaderCircle } from 'lucide-react';
import { lazy, Suspense, useCallback, useEffect, useRef, useState } from 'react';
import { toast } from 'sonner';
const CreatePatientPolicy = lazy(
    () => import('@/policy/create-patient-policy'),
);

export default function CounterIncome() {
    const {
        selectedPatient,
        departments,
        departmentKey,
        openCounter,
        services,
        providers,
        recesitation,
        existingServiceOrders,
        panelCompanies,
    } = usePage().props;

    const step = !selectedPatient ? 1 : !departmentKey ? 2 : 3;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Counter',
            href: openCounter
                ? counterView({
                      ctYear: openCounter.year,
                      ctMonth: openCounter.month,
                      ctNumber: openCounter.number,
                  }).url
                : counter().url,
        },
    ];

    const bullets = [
        {
            title: openCounter && openCounter.ct_number,
            url:
                openCounter &&
                counterView({
                    ctYear: openCounter.year,
                    ctMonth: openCounter.month,
                    ctNumber: openCounter.number,
                }).url,
            active: step === 1,
        },
    ];

    if (selectedPatient?.name) {
        bullets.push({
            title: selectedPatient && selectedPatient?.ps_number,
            url:
                selectedPatient &&
                counterSelectDepartment({
                    pYear: selectedPatient.year,
                    pMonth: selectedPatient.month,
                    number: selectedPatient.number,
                }).url,
            active: step === 2,
        });
        breadcrumbs.push({
            title: selectedPatient?.name,
            href:
                selectedPatient &&
                counterSelectDepartment({
                    pYear: selectedPatient.year,
                    pMonth: selectedPatient.month,
                    number: selectedPatient.number,
                }).url,
        });
    } else {
        bullets.push({
            title: 'No patient selected',
            url: counterSelectPatient({
                ctYear: openCounter.year,
                ctMonth: openCounter.month,
                ctNumber: openCounter.number,
            }).url,
            active: step === 2,
        });
        breadcrumbs.push({
            title: 'Select Patient',
            href: counterSelectPatient({
                ctYear: openCounter.year,
                ctMonth: openCounter.month,
                ctNumber: openCounter.number,
            }).url,
        });
    }
    if (selectedPatient?.name && departmentKey != '') {
        bullets.push({
            title: departmentKey != '' && `Departments (${departmentKey})`,
            url:
                selectedPatient &&
                departmentKey != '' &&
                counterSelectDepartmentService({
                    pYear: selectedPatient.year,
                    pMonth: selectedPatient.month,
                    number: selectedPatient.number,
                    departmentKey: departmentKey as string,
                }).url,
            active: step === 3,
        });
        breadcrumbs.push({
            title:
                departmentKey != ''
                    ? `Departments (${departmentKey})`
                    : 'Select Department',
            href:
                selectedPatient && departmentKey != ''
                    ? counterSelectDepartmentService({
                          pYear: selectedPatient.year,
                          pMonth: selectedPatient.month,
                          number: selectedPatient.number,
                          departmentKey: departmentKey as string,
                      }).url
                    : '#',
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Counter" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-2 text-gray-800 dark:bg-neutral-950 dark:text-white">
                    <BulletsWrapper bullets={bullets}>
                        {step === 1 && (
                            <SelectPatient openCounter={openCounter} />
                        )}
                        {step === 2 && (
                            <SelectDepartment
                                openCounter={openCounter}
                                patient={selectedPatient}
                                departments={departments}
                            />
                        )}
                        {step === 3 && (
                            <CollectPayment
                                recesitation={recesitation}
                                existingServiceOrders={existingServiceOrders}
                                openCounter={openCounter}
                                patient={selectedPatient}
                                departments={departments}
                                departmentKey={departmentKey}
                                services={services}
                                providers={providers}
                                panelCompanies={panelCompanies ?? []}
                            />
                        )}
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}

function CollectPayment({
    recesitation,
    existingServiceOrders,
    openCounter,
    patient,
    departments,
    departmentKey,
    services,
    providers,
    panelCompanies,
}: any) {
    const [selectedServices, setSelectedServices] = useState<string[]>([]);
    const [mriNumber, setMriNumber] = useState<string>('');
    const [paymentMethod, setPaymentMethod] = useState<string>('CASH');
    const [panelCompany, setPanelCompany] = useState<string>('');
    const [amountPaid, setAmountPaid] = useState<number>(0);
    const [validationErrors, setValidationErrors] = useState<any>({});
    const [serviceProviders, setServiceProviders] = useState<any>({});
    const [selectedServiceOrder, setSelectedServiceOrder] = useState<string>();
    const [processing, setProcessing] = useState<boolean>(false);

    console.log(services);

    const [formData, setFormData] = useState<any>({
        total: 0,
        items: [],
    });

    const calculateChange = () => {
        console.log(
            'Calculating change with amountPaid:',
            amountPaid,
            'and total:',
            formData.total,
        );
        return amountPaid - formData.total;
    };

    const validatedInput = (billData: any) => {
        // Patient ID must be set
        if (!billData.patient_id) {
            validationErrors.patient_id = ['Patient ID is required.'];
            setValidationErrors(validationErrors);
            return false;
        }

        // Check each item if sevice have providers then provider must be selected
        for (const item of billData.items) {
            const service = services.find((s: any) => s.id == item.service_id);
            if (service && service.have_service_provider && !item.provider_id) {
                validationErrors[`provider_id_${item.service_id}`] = [
                    'Provider is required for this service.',
                ];
                setValidationErrors(validationErrors);
                return false;
            }
        }
        return true;
    };

    const generateBill = async () => {
        // Clear previous validation errors
        setValidationErrors({});
        setProcessing(true);

        if (recesitation && selectedServiceOrder === '') {
            toast.error('Please enter MRI number for recesitation services.');
            setProcessing(false);
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
                    total: item.total || item.quantity * item.charges,
                    provider_id: serviceProviders[item.serviceId] || null,
                })),
                total_amount: formData.total,
                payment_method: paymentMethod,
                panel_company: paymentMethod === 'PANEL' ? panelCompany : null,
                amount_paid: amountPaid ? amountPaid : 0,
                change_amount: calculateChange(),
            };

            if (!validatedInput(billData)) {
                toast.error('Please fix the validation errors before generating the bill.');
                setProcessing(false);
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
                    setProcessing(false);
                },
                onError: (errors) => {
                    console.error('Validation errors:', errors);
                    setValidationErrors(errors);

                    // Show a general error message
                    const errorMessages = Object.values(errors).flat();
                    setProcessing(false);
                },
                onFinish: () => {
                    console.log('Request completed');
                },
            });
        } catch (error) {
            console.error('Error generating bill:', error);
            toast.error('Error generating bill. Please try again.');
        }
    };

    const updateItemQuantityAndCharges = (
        serviceId: string,
        quantity: number,
        charges: number,
    ) => {
        setFormData((prevData: any) => {
            const updatedItems = prevData.items.map((item: any) => {
                if (item.serviceId === serviceId) {
                    return {
                        ...item,
                        quantity: quantity,
                        charges: charges,
                        total: quantity * charges,
                    };
                }
                return item;
            });

            const newTotal = updatedItems.reduce(
                (sum: number, item: any) => sum + (item.total || 0),
                0,
            );

            return {
                ...prevData,
                items: updatedItems,
                total: newTotal,
            };
        });
    };

    const updateServiceProvider = (serviceId: string, providerId: string) => {
        setServiceProviders((prev: any) => ({
            ...prev,
            [serviceId]: providerId,
        }));
    };

    const flattenObject = (obj: any, keysToInclude: any[] = []) => {
        if (typeof obj !== 'object' || obj === null) {
            return [];
        }

        // Object keys foreach loop
        let items: any[] = [];
        if (keysToInclude.length > 0) {
            keysToInclude.forEach((key: any) => {
                const item = obj[key];
                items = items.concat(item);
            });
            return items;
        }
        Object.keys(obj).forEach((key) => {
            const item = obj[key];
            items = items.concat(item);
        });
        return items;
    };

    useEffect(() => {
        let totalCharges = 0;

        const customerSelectedServicesCartArray = selectedServices.map(
            (serviceId: any) => {
                const sl = services.find((s: any) => s.id == serviceId);
                const itemCharges = sl?.charges || 0;
                const itemQuantity = 1;
                const itemTotal = itemQuantity * itemCharges;
                totalCharges += itemTotal;
                console.log(sl);

                // Flat providers array
                const providerUsers = flattenObject(
                    providers,
                    sl?.service_provider_types || [],
                );

                console.log('Providers for service ', serviceId, providerUsers);

                return {
                    serviceId: sl?.id || '',
                    name: sl?.name || '',
                    quantity: itemQuantity,
                    charges: itemCharges,
                    providerId:
                        providerUsers.find((p: any) => p.serviceId == serviceId)
                            ?.id || '',
                    total: itemTotal,
                };
            },
            {},
        );

        const newFormData = {
            total: totalCharges,
            items: customerSelectedServicesCartArray,
        };

        setFormData(newFormData);
    }, [selectedServices]);

    const [department, setDepartment] = useState<any>({
        id: '',
        name: '',
        slug: '',
    });

    const [isRecesitation, setIsRecestitation] = useState<boolean>(false);

    useEffect(() => {
        console.log(departmentKey);

        // If depatmentKey is recesitation type then remove RECES- prefix
        let departmentKeyCleaned = departmentKey;
        if (departmentKey && departmentKey.startsWith('RECES-')) {
            setIsRecestitation(true);
            departmentKeyCleaned = departmentKey.replace('RECES-', '');
        }

        const dept = departments.find(
            (d: any) => d.slug === departmentKeyCleaned,
        );
        setDepartment(dept);
    }, [departmentKey]);

    const [changeAmount, setChangeAmount] = useState<any>(0);

    useEffect(() => {
        console.log('Recesitation:', isRecesitation);
    }, [isRecesitation]);

    useEffect(() => {
        setChangeAmount(calculateChange());
    }, [amountPaid, formData.total]);

    return (
        <div className="flex h-full w-full flex-col space-y-4">
            <div className="flex h-full w-full flex-row space-x-6">
                <div className="flex-1">
                    <h3 className="mb-2 text-3xl font-bold">Add Bill</h3>
                    <div className="mb-2 grid w-full flex-1 grid-cols-4 gap-4">
                        <DepartmentMiniCard
                            department={department}
                            recestitation={isRecesitation}
                            patient={patient}
                            className="flex h-full w-full flex-col items-center justify-center rounded-xl border"
                        />
                        <PatientMiniCard
                            patient={patient}
                            className="col-span-3 w-full"
                        />
                    </div>
                    <div className="mb-2 rounded-xl border p-4 dark:border-neutral-950">
                        {recesitation && (
                            <div className="mb-2 grid gap-2">
                                <Label htmlFor="service">MRI #</Label>
                                <Select
                                    name="mri_number"
                                    defaultValue={selectedServiceOrder}
                                    onValueChange={setSelectedServiceOrder}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select MRI number" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {existingServiceOrders.map(
                                            (order: any) => (
                                                <SelectItem value={order.id}>
                                                    {order.so_number +
                                                        ` - ` +
                                                        order.service.name}
                                                </SelectItem>
                                            ),
                                        )}
                                    </SelectContent>
                                </Select>
                            </div>
                        )}
                        <div className="mb-2 grid gap-2">
                            <Label htmlFor="featured_services">
                                Featured Services
                            </Label>
                            <div className="grid grid-cols-2 gap-3 md:grid-cols-3">
                                {services
                                    .filter(
                                        (service: any) => service.is_featured,
                                    )
                                    .map((service: any) => (
                                        <button
                                            key={service.id}
                                            onClick={() =>
                                                setSelectedServices((prev) =>
                                                    prev.includes(service.id)
                                                        ? prev.filter(
                                                              (id) =>
                                                                  id !==
                                                                  service.id,
                                                          )
                                                        : [...prev, service.id],
                                                )
                                            }
                                            className={clsx(
                                                'rounded-lg border-2 p-3 text-left transition-all',
                                                selectedServices.includes(
                                                    service.id,
                                                )
                                                    ? 'border-green-500 bg-green-50 dark:bg-green-950'
                                                    : 'border-gray-200 hover:border-gray-300 dark:border-neutral-700',
                                            )}
                                        >
                                            <div className="text-sm font-semibold">
                                                {service.name}
                                            </div>
                                        </button>
                                    ))}
                            </div>
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="service">Service</Label>
                            <AdvancedTagSelect
                                options={services.map((service: any) => ({
                                    value: service.id,
                                    label: service.name,
                                }))}
                                value={selectedServices}
                                onValueChange={setSelectedServices}
                                placeholder="Select services..."
                                disabled={
                                    department?.have_composit_services &&
                                    selectedServices.length > 0
                                }
                            />
                            {/* <InputError message={errors.email} /> */}
                        </div>
                    </div>
                    <div className="mb-2 rounded-xl border p-4 dark:border-neutral-950">
                        <div className="grid gap-2">
                            <table className="w-full border text-left">
                                <tbody>
                                    <tr className="rounded-tl-xl rounded-tr-xl border-b dark:bg-neutral-950 dark:text-white">
                                        <td className="p-2 text-left">
                                            Product
                                        </td>
                                        <td className="p-2 text-right">
                                            Provider
                                        </td>
                                        {/* <td className="p-2 text-right">QTY</td> */}
                                        <td className="p-2 text-right">
                                            Total
                                        </td>
                                    </tr>
                                    {formData.items.length > 0 ? (
                                        formData.items.map((item: any) => {
                                            const service = services.find(
                                                (s: any) =>
                                                    s.id == item.serviceId,
                                            );
                                            return (
                                                <BillItemsEditableTableRow
                                                    key={item.serviceId}
                                                    service_name={item.name}
                                                    serviceid={item.serviceId}
                                                    quantity={item.quantity}
                                                    charges={item.charges}
                                                    service={service}
                                                    selectedProvider={
                                                        serviceProviders[
                                                            item.serviceId
                                                        ] || ''
                                                    }
                                                    providers={flattenObject(
                                                        providers,
                                                        service?.service_provider_types ||
                                                            [],
                                                    )}
                                                    onUpdate={
                                                        updateItemQuantityAndCharges
                                                    }
                                                    onProviderUpdate={
                                                        updateServiceProvider
                                                    }
                                                    validationErrors={
                                                        validationErrors
                                                    }
                                                />
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td
                                                colSpan={4}
                                                className="rounded-br-xl rounded-bl-xl border p-4 text-center text-gray-500 dark:border-neutral-950 dark:text-white"
                                            >
                                                No services selected.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                            <div className="mt-4 flex justify-end">
                                <div className="grid w-full grid-cols-1 gap-4 md:grid-cols-2">
                                    <div></div>

                                    <div className="flex flex-col space-y-2">
                                        <div>
                                            <Label htmlFor="total_amount">
                                                Total Amount
                                            </Label>
                                            <Input
                                                id="total_amount"
                                                type="text"
                                                name="total_amount"
                                                className="text-right font-semibold"
                                                value={`${formData.total.toFixed(2)}/- only`}
                                                readOnly
                                            />
                                            <InputError
                                                message={
                                                    validationErrors.total_amount
                                                }
                                            />
                                        </div>
                                        <div>
                                            <Label htmlFor="payment_method">
                                                Payment Method
                                            </Label>
                                            <Select
                                                value={paymentMethod}
                                                onValueChange={setPaymentMethod}
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select payment method" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="CASH">
                                                        Cash
                                                    </SelectItem>
                                                    <SelectItem value="CARD">
                                                        Card
                                                    </SelectItem>
                                                    <SelectItem value="CHEQUE">
                                                        Cheque
                                                    </SelectItem>
                                                    <SelectItem value="BANK_TRANSFER">
                                                        Bank Transfer
                                                    </SelectItem>
                                                    <SelectItem value="PANEL">
                                                        INSURANCE
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                            <InputError
                                                message={
                                                    validationErrors.payment_method
                                                }
                                            />
                                        </div>

                                        {paymentMethod === 'PANEL' && (
                                            <div>
                                                <Label htmlFor="panel_company">
                                                    Panel Company
                                                </Label>
                                                <Select
                                                    value={panelCompany}
                                                    onValueChange={
                                                        setPanelCompany
                                                    }
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select panel company" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {panelCompanies.map(
                                                            (company: any) => (
                                                                <SelectItem
                                                                    key={
                                                                        company.id
                                                                    }
                                                                    value={
                                                                        company.id
                                                                    }
                                                                >
                                                                    {
                                                                        company.name
                                                                    }
                                                                </SelectItem>
                                                            ),
                                                        )}
                                                    </SelectContent>
                                                </Select>
                                                <InputError
                                                    message={
                                                        validationErrors.panel_company
                                                    }
                                                />
                                            </div>
                                        )}

                                        {(paymentMethod === 'CASH' ||
                                            paymentMethod === 'CARD' ||
                                            paymentMethod === 'CHEQUE' ||
                                            paymentMethod ===
                                                'BANK_TRANSFER') && (
                                            <div>
                                                <Label htmlFor="amount_paid">
                                                    Amount Paid
                                                </Label>
                                                <Input
                                                    id="amount_paid"
                                                    type="number"
                                                    name="amount_paid"
                                                    className="text-right"
                                                    value={
                                                        amountPaid === 0
                                                            ? ''
                                                            : amountPaid
                                                    }
                                                    onChange={(e) =>
                                                        setAmountPaid(
                                                            parseFloat(
                                                                e.target.value,
                                                            ),
                                                        )
                                                    }
                                                    min={0}
                                                    step={0.01}
                                                    placeholder="0.00"
                                                />
                                                <InputError
                                                    message={
                                                        validationErrors.amount_paid
                                                    }
                                                />
                                            </div>
                                        )}

                                        <div className="cursor-not-allowed">
                                            <Label htmlFor="change_amount">
                                                {changeAmount > 0
                                                    ? `Change`
                                                    : 'Pending Receivable'}
                                            </Label>
                                            <Input
                                                id="change_amount"
                                                type="text"
                                                name="change_amount"
                                                className="cursor-not-allowed bg-green-50 text-right font-semibold"
                                                value={`${changeAmount.toFixed(2)}/- only`}
                                                readOnly
                                            />
                                        </div>

                                        <div className="pt-4">
                                            <Button
                                                variant={'default'}
                                                onClick={generateBill}
                                                disabled={
                                                    formData.items.length ===
                                                        0 ||
                                                    amountPaid < 0 ||
                                                    processing
                                                }
                                                className={clsx(
                                                    formData.items.length ===
                                                        0 ||
                                                        amountPaid < 0 ||
                                                        processing
                                                        ? 'cursor-not-allowed opacity-50'
                                                        : '',
                                                )}
                                            >
                                                {processing && (
                                                    <LoaderCircle className="h-4 w-4 animate-spin" />
                                                )}
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
    );
}

function SelectDepartment({ openCounter, patient, departments }: any) {
    return (
        <div className="flex h-full w-full flex-row space-y-4">
            <PatientHistorySideBar patient={patient} className="w-1/4" />
            <div className="flex h-full w-full flex-1 flex-col space-y-4 px-4">
                <PatientMiniCard patient={patient} className="w-full" />
                <h3 className="mb-2 text-3xl font-bold">Departments</h3>
                <div className="grid w-full grid-cols-2 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                    {departments.map((department: any) => (
                        <div
                            key={department.id}
                            className="flex flex-col items-center justify-center"
                        >
                            <Link
                                href={
                                    counterSelectDepartmentService({
                                        pYear: patient.year,
                                        pMonth: patient.month,
                                        number: patient.number,
                                        departmentKey: department.slug,
                                    }).url
                                }
                                className="flex h-32 w-32 flex-col items-center justify-center rounded-xl border"
                            >
                                <img
                                    src={department.image}
                                    alt={department.name}
                                    className="h-12 w-12 object-contain"
                                />
                                <span className="mt-2 max-w-28 text-center text-sm">
                                    {department.name}
                                </span>
                            </Link>
                        </div>
                    ))}
                </div>
                <h3 className="mb-2 text-3xl font-bold">Recesitation</h3>
                <div className="grid w-full grid-cols-1 gap-4 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                    {departments
                        .filter(
                            (department: any) =>
                                department.have_composit_services,
                        )
                        .map((department: any) => (
                            <div
                                key={department.id}
                                className="flex flex-col items-center justify-center"
                            >
                                <Link
                                    href={
                                        counterSelectDepartmentService({
                                            pYear: patient.year,
                                            pMonth: patient.month,
                                            number: patient.number,
                                            departmentKey: `RECES-${department.slug}`,
                                        }).url
                                    }
                                    className="flex h-32 w-32 flex-col items-center justify-center rounded-xl border"
                                >
                                    <img
                                        src={department.image}
                                        alt={department.name}
                                        className="h-12 w-12 object-contain"
                                    />
                                    <span className="mt-2 max-w-28 text-center text-sm">
                                        {department.name}
                                    </span>
                                </Link>
                            </div>
                        ))}
                </div>
            </div>
        </div>
    );
}

import AlertError from '@/components/alert-error';
import { apiFetch } from '@/lib/api-fetch';

// Search params type — only committed values trigger the API
type PatientSearchParams = {
    mr_number: string;
    cnic_number: string;
    patient_name: string;
    patient_contact: string;
    patient_age: string;
    patient_gender: string;
    mri_number: string;
    file_number: string;
};

const EMPTY_SEARCH: PatientSearchParams = {
    mr_number: '',
    cnic_number: '',
    patient_name: '',
    patient_contact: '',
    patient_age: '',
    patient_gender: '',
    mri_number: '',
    file_number: '',
};

// CNIC mask: 99999-9999999-9 → 15 chars when complete
const CNIC_COMPLETE_LENGTH = 15;
// Contact mask: +99-999-9999999 → 15 chars when complete
const CONTACT_COMPLETE_LENGTH = 15;

function SelectPatient({ openCounter }: any) {
    const [patients, setPatients] = useState([]);
    const [exactMatch, setExactMatch] = useState([]);
    const [isLoading, setIsLoading] = useState(false);
    const [apiError, setApiError] = useState<string | string[] | null>(null);
    const [hasSearched, setHasSearched] = useState(false);

    // Committed search params — API fires only when this changes
    const [searchParams, setSearchParams] = useState<PatientSearchParams>(EMPTY_SEARCH);

    // Raw field states (for form binding)
    const [psInput, setPsInput] = useState('');
    const [patientCnic, setPatientCnic] = useState('');
    const [mriNumber, setMriNumber] = useState('');
    const [fileNumber, setFileNumber] = useState('');

    // Single map for all debounce timers; cleared on unmount
    const debounceTimers = useRef<Record<string, ReturnType<typeof setTimeout>>>({});

    useEffect(() => {
        return () => {
            Object.values(debounceTimers.current).forEach(clearTimeout);
        };
    }, []);

    // Form state for patient creation
    const [formData, setFormDataState] = useState({ cnic: '', name: '', contact: '', age: '', gender: '' });
    const [formErrors, setFormErrors] = useState<Record<string, string[]>>({});
    const [creating, setCreating] = useState(false);
    const setData = (field: string, val: string) => setFormDataState((d) => ({ ...d, [field]: val }));
    const clearErrors = () => setFormErrors({});

    // Helper: update a single search param only if its value has changed
    const commitParam = useCallback((key: keyof PatientSearchParams, value: string) => {
        setSearchParams((p) => {
            if (p[key] === value) return p;
            return { ...p, [key]: value };
        });
    }, []);

    // Helper: debounce-then-commit a search param
    const debouncedCommit = useCallback(
        (field: string, key: keyof PatientSearchParams, value: string, delay = 400) => {
            if (debounceTimers.current[field]) clearTimeout(debounceTimers.current[field]);
            debounceTimers.current[field] = setTimeout(() => commitParam(key, value), delay);
        },
        [commitParam],
    );

    // Fire API whenever committed search params change
    useEffect(() => {
        const hasAnyInput = Object.values(searchParams).some((v) => v !== '');
        if (hasAnyInput) {
            setHasSearched(true);
        }
        fetchPatientsFromApi(searchParams, hasAnyInput);
        setApiError(null);
    }, [searchParams]);

    const fetchPatientsFromApi = useCallback(async (params: PatientSearchParams, hasInput: boolean) => {
        if (!hasInput) {
            setPatients([]);
            setExactMatch([]);
            setIsLoading(false);
            return;
        }
        setIsLoading(true);
        try {
            const response = await apiFetch('/api/patients', {
                method: 'POST',
                body: JSON.stringify(params),
            });
            if (response.ok) {
                const res = await response.json();
                setPatients(res.data.possible);
                setExactMatch(res.data.exact);
            } else {
                const err = await response.json().catch(() => ({}));
                setApiError(err.message || 'Failed to fetch patients.');
            }
        } catch (error: any) {
            setApiError(error?.message || 'Network error fetching patients.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    // ── Commit helpers ────────────────────────────────────────────────────────

    // MR Number: debounced commit (was: every keystroke → API on each char)
    const onMrNumberChange = (masked: string) => {
        setPsInput(masked);
        debouncedCommit('mr', 'mr_number', masked);
    };

    // CNIC: commit only when complete (15 chars), clear when emptied
    const onCnicChange = (masked: string, unmasked: string) => {
        setPatientCnic(masked);
        if (masked.length === CNIC_COMPLETE_LENGTH) {
            commitParam('cnic_number', masked);
        } else if (masked === '') {
            commitParam('cnic_number', '');
        }
    };

    // Name: live debounced search (was: on-blur only)
    const onNameChange = (val: string) => {
        setData('name', val);
        const next = val.trim().length >= 2 ? val.trim() : '';
        debouncedCommit('name', 'patient_name', next);
    };

    // Contact: commit only when complete (11+ digits), clear when below 5
    const onContactChange = (masked: string) => {
        setData('contact', masked);
        const digits = masked.replace(/\D/g, '').length;
        if (digits >= 11) {
            commitParam('patient_contact', masked);
        } else if (digits < 5) {
            commitParam('patient_contact', '');
        }
    };

    // Age: commit on blur
    const onAgeBlur = () => {
        commitParam('patient_age', formData.age);
    };

    // Gender: commit immediately on selection
    const onGenderChange = (val: string) => {
        setData('gender', val);
        commitParam('patient_gender', val);
    };

    // MRI Number: debounced commit (was: every keystroke → API on each char)
    const onMriNumberChange = (val: string) => {
        setMriNumber(val);
        debouncedCommit('mri', 'mri_number', val);
    };

    // FILE Number: debounced commit (was: every keystroke → API on each char)
    const onFileNumberChange = (val: string) => {
        setFileNumber(val);
        debouncedCommit('file', 'file_number', val);
    };

    // Patient creation via fetch (JSON) — Inertia post() sends X-Inertia header, not Accept: application/json
    const handleCreatePatient = async () => {
        setCreating(true);
        setFormErrors({});
        try {
            const response = await apiFetch(apiPatientsStore().url, {
                method: 'POST',
                body: JSON.stringify({
                    name: formData.name,
                    contact: formData.contact,
                    cnic: formData.cnic || null,
                    age: formData.age,
                    gender: formData.gender,
                }),
            });

            const res = await response.json().catch(() => ({}));

            if (response.status === 201) {
                // Created — redirect to counter department selection
                const patient = res.data;
                window.location.href = counterSelectDepartment({
                    pYear: patient.year,
                    pMonth: patient.month,
                    number: patient.number,
                }).url;
            } else if (response.status === 409) {
                // Duplicate found — surface in results
                if (res.data) {
                    setExactMatch(res.data.exact ?? []);
                    setPatients(res.data.possible ?? []);
                }
                toast.warning(res.message || 'Possible duplicate patient found.');
            } else if (response.status === 422) {
                // Validation errors
                setFormErrors(res.errors ?? {});
            } else {
                setApiError(res.message || 'Failed to create patient.');
            }
        } catch (error: any) {
            setApiError(error?.message || 'Network error creating patient.');
        } finally {
            setCreating(false);
        }
    };

    return (
        <div className="grid h-full w-full grid-cols-2 divide-x divide-[#06df72]">
            {/* ── Left column: search / create form ── */}
            <div className="flex flex-col overflow-y-auto p-4 pr-8">
                <div className="flex w-full flex-col space-y-4">
                    <h3 className="mb-2 text-3xl font-bold">Select / Create Patient</h3>

                    {apiError && (
                        <AlertError
                            errors={[Array.isArray(apiError) ? apiError.join(' ') : apiError]}
                            className="mb-2"
                        />
                    )}

                    {/* MR Number */}
                    <div className="grid gap-1">
                        <Label htmlFor="mr_number">MR Number</Label>
                        <MaskInput
                            id="mr_number"
                            type="text"
                            name="mr_number"
                            tabIndex={1}
                            autoFocus
                            autoComplete="false"
                            mask="aa/9999/99/999999"
                            placeholder="--/----/--/------"
                            value={psInput}
                            onValueChange={({ masked }) => onMrNumberChange(masked)}
                        />
                    </div>

                    {/* MRI Number */}
                    <div className="grid gap-1">
                        <Label htmlFor="mri_number">MRI Number (Service Order)</Label>
                        <Input
                            id="mri_number"
                            type="text"
                            name="mri_number"
                            tabIndex={2}
                            autoComplete="false"
                            placeholder="e.g. OPD/2025/03/0001"
                            value={mriNumber}
                            onChange={(e) => onMriNumberChange(e.target.value)}
                        />
                    </div>

                    {/* FILE Number */}
                    <div className="grid gap-1">
                        <Label htmlFor="file_number">File Number (SO Short)</Label>
                        <Input
                            id="file_number"
                            type="text"
                            name="file_number"
                            tabIndex={3}
                            autoComplete="false"
                            placeholder="Short file number"
                            value={fileNumber}
                            onChange={(e) => onFileNumberChange(e.target.value)}
                        />
                    </div>

                    {/* CNIC */}
                    <div className="grid gap-1">
                        <Label htmlFor="cnic_number">CNIC Number</Label>
                        <MaskInput
                            id="cnic_number"
                            type="text"
                            name="cnic_number"
                            tabIndex={4}
                            autoComplete="false"
                            mask="99999-9999999-9"
                            placeholder="----- ------- -"
                            value={patientCnic}
                            onValueChange={({ masked, unmasked }) => onCnicChange(masked, unmasked)}
                        />
                    </div>

                    {/* Patient Name — live debounced search */}
                    <div className="grid gap-1">
                        <Label htmlFor="patient_name" required={true}>Patient Name</Label>
                        <Input
                            id="patient_name"
                            type="text"
                            name="patient_name"
                            tabIndex={5}
                            autoComplete="false"
                            placeholder="Patient name"
                            value={formData.name}
                            onChange={(e) => onNameChange(e.target.value)}
                        />
                        <InputError message={formErrors.name?.[0]} />
                    </div>

                    {/* Contact — search when complete */}
                    <div className="grid gap-1">
                        <Label htmlFor="patient_contact" required={true}>Patient Contact</Label>
                        <MaskInput
                            id="patient_contact"
                            type="text"
                            name="patient_contact"
                            tabIndex={6}
                            autoComplete="false"
                            value={formData.contact === '' ? '+92-' : formData.contact}
                            mask="+99-999-9999999"
                            placeholder="+92-000-0000000"
                            onValueChange={({ masked }) => onContactChange(masked)}
                        />
                        <InputError message={formErrors.contact?.[0]} />
                    </div>

                    {/* Age — search on blur */}
                    <div className="grid gap-1">
                        <Label htmlFor="patient_age" required={true}>Patient Age</Label>
                        <Input
                            id="patient_age"
                            type="number"
                            name="patient_age"
                            tabIndex={7}
                            autoComplete="false"
                            placeholder="Patient age"
                            value={formData.age}
                            onChange={(e) => setData('age', e.target.value)}
                            onBlur={onAgeBlur}
                        />
                        <InputError message={formErrors.age?.[0]} />
                    </div>

                    {/* Gender — search immediately */}
                    <div className="grid gap-1">
                        <Label htmlFor="patient_gender" required={true}>Patient Gender</Label>
                        <div className="flex flex-row space-x-4">
                            {(['m', 'f', 't'] as const).map((val) => {
                                const labels = { m: 'Male', f: 'Female', t: 'Transgender' };
                                return (
                                    <Label key={val} htmlFor={`patient_gender_${val}`}>
                                        <RadioInput
                                            id={`patient_gender_${val}`}
                                            type="radio"
                                            name="patient_gender"
                                            tabIndex={8}
                                            autoComplete="false"
                                            value={val}
                                            className="mr-2"
                                            checked={formData.gender === val}
                                            onChange={(e) => onGenderChange(e.target.value)}
                                        />
                                        {labels[val]}
                                    </Label>
                                );
                            })}
                        </div>
                        <InputError message={formErrors.gender?.[0]} />
                    </div>

                    <Suspense fallback={<div className="text-xs text-gray-400">Loading policy…</div>}>
                        <CreatePatientPolicy className="text-xs text-gray-500" />
                    </Suspense>
                </div>
            </div>

            {/* ── Right column: results ── */}
            <div className="flex flex-col overflow-y-auto p-4 pr-8">
                {/* Loading bar — fixed height slot so layout never shifts */}
                <div className="mb-3 flex h-6 items-center">
                    {isLoading ? (
                        <div className="flex items-center gap-2 text-sm text-gray-500">
                            <Spinner className="size-4" />
                            <span>Searching patients…</span>
                        </div>
                    ) : null}
                </div>

                <div className="flex w-full flex-col space-y-4">
                    {!isLoading && exactMatch.length > 0 && (
                        <>
                            <h3 className="text-sm font-semibold text-teal-700">
                                Exact Match ({exactMatch.length})
                            </h3>
                            {exactMatch.map((p: any) => (
                                <PatientMiniCard
                                    key={p.id}
                                    patient={p}
                                    tempAge={formData.age}
                                    tempGender={formData.gender}
                                    tempContact={formData.contact}
                                    tempCnic={formData.cnic}
                                    className="w-full border-l-4 border-teal-500"
                                    link={counterSelectDepartment({ pYear: p.year, pMonth: p.month, number: p.number }).url}
                                />
                            ))}
                        </>
                    )}

                    {!isLoading && patients.length > 0 && (
                        <>
                            <h3 className="text-sm font-semibold text-orange-600">
                                Possible Matches ({patients.length})
                            </h3>
                            {patients.map((p: any) => (
                                <PatientMiniCard
                                    key={p.id}
                                    patient={p}
                                    tempAge={formData.age}
                                    tempGender={formData.gender}
                                    tempContact={formData.contact}
                                    tempCnic={formData.cnic}
                                    className="w-full"
                                    link={counterSelectDepartment({ pYear: p.year, pMonth: p.month, number: p.number }).url}
                                />
                            ))}
                        </>
                    )}

                    {!isLoading && hasSearched && exactMatch.length === 0 && patients.length === 0 && (
                        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-10 text-center text-gray-400">
                            <p className="text-sm font-medium">No patients found</p>
                            <p className="mt-1 text-xs">Fill in the name below and click &quot;Create New Patient&quot; to register a new patient.</p>
                        </div>
                    )}

                    {!hasSearched && !isLoading && (
                        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-10 text-center text-gray-300">
                            <p className="text-sm">Start typing to search for existing patients</p>
                        </div>
                    )}

                    {formData.name && (
                        <div className="flex cursor-default flex-col space-y-4 rounded-xl bg-[#1c398e] p-2 hover:bg-[#06df72] dark:bg-[#0a0a0a] dark:bg-[#262626]">
                            <PatientMiniCard
                                patient={{
                                    name: formData.name,
                                    gender: formData.gender,
                                    ps_number: psInput,
                                    contact: formData.contact,
                                    cnic: formData.cnic,
                                    age: formData.age,
                                }}
                                className="w-full"
                            />
                            <div className="items-right justify-end">
                                {!(formData.age && formData.gender) ? (
                                    <Button disabled title="Age and gender are required to create a patient">
                                        <span>Age &amp; gender required</span>
                                    </Button>
                                ) : (
                                    <Button onClick={handleCreatePatient} disabled={creating}>
                                        {creating && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                        <span>Create New Patient</span>
                                    </Button>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
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
    validationErrors,
    providers,
}: any) {
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

    console.log(providers);

    return (
        <>
            <tr className="border-b border-neutral-950 dark:bg-neutral-700 dark:text-white">
                <td className="p-2">{service_name}</td>
                <td className="p-2 text-right">
                    {service?.service_provider_types &&
                    service.service_provider_types.length > 0 ? (
                        <>
                            <Select
                                value={selectedProvider}
                                onValueChange={handleProviderChange}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Select provider" />
                                </SelectTrigger>
                                <SelectContent>
                                    {providers.map((provider: any) => (
                                        <SelectItem
                                            key={provider.id}
                                            value={provider.id.toString()}
                                        >
                                            {provider.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {!selectedProvider && (
                                <InputError message="Please select a provider." />
                            )}
                        </>
                    ) : (
                        <span className="text-sm text-gray-400">N/A</span>
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
                        className="inline-block w-24 text-right"
                        value={parseInt(c.toString()) == 0 ? '' : c}
                        onChange={(e) =>
                            handleChargesChange(parseFloat(e.target.value) || 0)
                        }
                        min={0}
                        step={0.01}
                    />
                </td>
            </tr>
            {(validationErrors[`items.${serviceid}`] ||
                validationErrors[`items.${serviceid}.provider_id`]) && (
                <tr className="flex">
                    <td colSpan={4} className="w-full p-2">
                        <div className="space-y-1 text-sm text-red-500">
                            {validationErrors[`items.${serviceid}`]?.map(
                                (error: string, index: number) => (
                                    <div key={index}>{error}</div>
                                ),
                            )}
                            {validationErrors[
                                `items.${serviceid}.provider_id`
                            ]?.map((error: string, index: number) => (
                                <div key={index}>{error}</div>
                            ))}
                        </div>
                    </td>
                </tr>
            )}
        </>
    );
}
