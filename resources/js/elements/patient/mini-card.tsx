import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { RadioInput } from '@/components/ui/input-radio';
import { Label } from '@/components/ui/label';
import { MaskInput } from '@/components/ui/mask-input';
import { apiPatientsEdit } from '@/routes';
import { Patient } from '@/types';
import { Link, router } from '@inertiajs/react';
import { LucideAlertTriangle, LucidePhoneIncoming } from 'lucide-react';
import React from 'react';

interface PatientMiniCardProps {
    patient: Patient;
    className?: string;
    tempAge?: string | number;
    tempGender?: string;
    tempContact?: string;
    tempCnic?: string;
    link?: string;
}

const PatientEditPopup: React.FC<{
    patient: Patient;
    link?: string;
    onClose: () => void;
    tempAge?: string | number;
    tempGender?: string;
    tempContact?: string;
    tempCnic?: string;
}> = ({
    patient,
    link = '',
    onClose,
    tempAge,
    tempGender,
    tempContact,
    tempCnic,
}) => {
    const InitialGender = patient.gender
        ? patient.gender
        : tempGender
          ? tempGender
          : '';

    const [patientCnic, setPatientCnic] = React.useState(
        patient.cnic ? patient.cnic : tempCnic ? tempCnic : '',
    );
    const [patientContact, setPatientContact] = React.useState(
        patient.contact ? patient.contact : tempContact ? tempContact : '',
    );
    const [patientAge, setPatientAge] = React.useState(
        patient.age ? patient.age : tempAge ? tempAge : '',
    );
    const [patientGender, setPatientGender] = React.useState(InitialGender);
    console.log({ patient, tempAge, tempGender, tempContact, tempCnic });
    const ContinueToTheLink = () => {
        router.visit(link);
    };

    const handleSubmit = async (e: React.FormEvent) => {
        try {
            const response = await fetch(apiPatientsEdit(patient.id).url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    cnic: patientCnic,
                    contact: patientContact,
                    age: patientAge,
                    gender: patientGender,
                }),
            });

            if (response.ok) {
                const data = await response.json();
                // Handle the response data as needed
                console.log('Patient created successfully:', data);
                if (link) {
                    ContinueToTheLink();
                } else {
                    onClose();
                }
            }
        } catch (error) {
            console.error('Error creating patient:', error);
        }

        // Perform form submission logic here, e.g., send data to the server
        // After successful submission, you might want to close the popup
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div className="w-full max-w-lg rounded-lg bg-white p-6 dark:bg-neutral-900">
                {/* Form fields for editing patient details */}
                <div className="flex flex-col">
                    <div className="flex w-full flex-col space-y-4">
                        <h3 className="mb-2 text-3xl font-bold">
                            Kindly collect missing information
                        </h3>
                        <div className="grid gap-2">
                            <Label htmlFor="mr_number">MR Number</Label>
                            <MaskInput
                                id="mr_number"
                                type="text"
                                name="mr_number"
                                tabIndex={100}
                                autoComplete="false"
                                mask="aa/9999/99/999999"
                                placeholder="--/----/--/------"
                                value={patient.ps_number}
                                disabled
                            />
                            {/* <InputError message={errors.email} /> */}
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="cnic_number">CNIC Number</Label>
                            <MaskInput
                                id="cnic_number"
                                type="text"
                                name="cnic_number"
                                tabIndex={1}
                                autoComplete="false"
                                mask="99999-9999999-9"
                                placeholder="----- ------- -"
                                value={patientCnic}
                                onValueChange={({
                                    masked,
                                    unmasked,
                                }: {
                                    masked: string;
                                    unmasked: string;
                                }) => setPatientCnic(masked)}
                            />
                            {/* <InputError message={errors.email} /> */}
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="patient_name" required={true}>
                                Patient Name
                            </Label>
                            <Input
                                id="patient_name"
                                type="text"
                                name="patient_name"
                                tabIndex={2}
                                autoComplete="false"
                                placeholder="Patient name"
                                value={patient.name}
                                disabled
                            />
                            {/* <InputError message={errors.email} /> */}
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="patient_contact" required={true}>
                                Patient Contact
                            </Label>
                            <MaskInput
                                id="patient_contact"
                                type="text"
                                name="patient_contact"
                                required
                                autoFocus
                                tabIndex={3}
                                autoComplete="false"
                                value={
                                    patientContact === ''
                                        ? '+92-'
                                        : patientContact
                                }
                                onValueChange={({
                                    masked,
                                    unmasked,
                                }: {
                                    masked: string;
                                    unmasked: string;
                                }) => setPatientContact(masked)}
                                mask="+99-999-9999999"
                                placeholder="+92-000-0000000"
                            />
                            {/* <InputError message={errors.email} /> */}
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="patient_age" required={true}>
                                Patient Age
                            </Label>
                            <Input
                                id="patient_age"
                                type="number"
                                name="patient_age"
                                required
                                tabIndex={4}
                                autoComplete="false"
                                placeholder="Patient age"
                                value={patientAge}
                                onChange={(e) => setPatientAge(e.target.value)}
                            />
                            {/* <InputError message={errors.email} /> */}
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="gender" required={true}>
                                Patient Gender
                            </Label>
                            <div className="flex flex-row space-x-4">
                                <Label htmlFor="gender_m">
                                    <RadioInput
                                        id="gender_m"
                                        type="radio"
                                        name="gender"
                                        required
                                        tabIndex={5}
                                        autoComplete="false"
                                        value={'m'}
                                        className="mr-2"
                                        checked={patientGender === 'm'}
                                        onChange={(e) =>
                                            setPatientGender(
                                                e.target
                                                    .value as Patient['gender'],
                                            )
                                        }
                                    />
                                    Male
                                </Label>
                                <Label htmlFor="gender_f">
                                    <RadioInput
                                        id="gender_f"
                                        type="radio"
                                        name="gender"
                                        required
                                        tabIndex={5}
                                        autoComplete="false"
                                        value={'f'}
                                        className="mr-2"
                                        checked={patientGender === 'f'}
                                        onChange={(e) =>
                                            setPatientGender(
                                                e.target
                                                    .value as Patient['gender'],
                                            )
                                        }
                                    />
                                    Female
                                </Label>
                                <Label htmlFor="gender_t">
                                    <RadioInput
                                        id="gender_t"
                                        type="radio"
                                        name="gender"
                                        required
                                        tabIndex={5}
                                        autoComplete="false"
                                        value={'t'}
                                        className="mr-2"
                                        checked={patientGender === 't'}
                                        onChange={(e) =>
                                            setPatientGender(
                                                e.target
                                                    .value as Patient['gender'],
                                            )
                                        }
                                    />
                                    Transgender
                                </Label>
                            </div>
                            {/* <InputError message={errors.email} /> */}
                        </div>
                    </div>
                </div>
                {/* Add other fields as necessary */}
                <div className="mt-5 flex justify-end space-x-2">
                    <Button variant="destructive" onClick={ContinueToTheLink}>
                        Skip for now and continue
                    </Button>
                    <Button variant="secondary" onClick={onClose}>
                        Cancel
                    </Button>
                    <Button type="submit" onClick={handleSubmit}>
                        Update and Continue
                    </Button>
                </div>
            </div>
        </div>
    );
};

const PatientMiniCard: React.FC<PatientMiniCardProps> = ({
    patient,
    link = '',
    className = '',
    tempAge = undefined,
    tempGender = undefined,
    tempContact = undefined,
    tempCnic = undefined,
}) => {
    const [showPopup, setShowPopup] = React.useState(false);

    const updatePatient = () => {
        setShowPopup(true);
    };

    if (
        patient.age === null ||
        patient.age === undefined ||
        patient.contact === null ||
        patient.contact === undefined ||
        patient.contact === ''
    ) {
        return (
            <div
                className={
                    'space-y-2 rounded-md border-1 p-2' + ' ' + className
                }
            >
                <PatientMiniCardInner patient={patient} />
                <div className="items-right justify-end">
                    <Button onClick={() => updatePatient()}>
                        <span>Set missing Information and continue</span>
                    </Button>
                </div>
                {showPopup && (
                    <PatientEditPopup
                        patient={patient}
                        tempAge={tempAge}
                        tempGender={tempGender}
                        tempContact={tempContact}
                        tempCnic={tempCnic}
                        onClose={() => setShowPopup(false)}
                        link={link}
                    />
                )}
            </div>
        );
    }

    if (!link) {
        return <PatientMiniCardInner patient={patient} className={className} />;
    }
    return (
        <Link href={link} className="flex flex-row">
            <PatientMiniCardInner patient={patient} className="w-full" />
        </Link>
    );
};

const PatientMiniCardInner: React.FC<PatientMiniCardProps> = ({
    patient,
    className = '',
}) => {
    const { ps_number, name, gender, contact, cnic, age } = patient;

    const pendingReceaveablesTotal = patient?.receaveables
        ? patient?.receaveables?.reduce(
              (total, receaveable) => total + Number(receaveable.amount),
              0,
          )
        : 0;

    return (
        <div
            className={`rounded-lg bg-white p-4 shadow-md dark:bg-neutral-950 dark:text-white ${className}`}
        >
            <div className="flex items-center space-x-4">
                <div className="shrink-0">
                    <div className="h-12 w-12 bg-gray-50">
                        {gender ? (
                            <img
                                src={
                                    gender == 'm'
                                        ? '/img/male-blue.png'
                                        : gender == 'f'
                                          ? '/img/female-blue.png'
                                          : gender == 't'
                                            ? '/img/transgender-blue.png'
                                            : '/img/avatar.png'
                                }
                                alt={name}
                                className="h-full w-full object-cover"
                            />
                        ) : (
                            <div className="flex h-full w-full items-center justify-center bg-blue-100 text-blue-600">
                                <span className="text-xl font-semibold">
                                    {name.charAt(0).toUpperCase()}
                                </span>
                            </div>
                        )}
                    </div>
                </div>

                <div className="min-w-0 flex-1">
                    <h3 className="truncate text-lg font-semibold">{name}</h3>

                    <div className="mt-2 space-y-1">
                        <span>
                            {ps_number ? ps_number : 'Not Assigned Yet'}
                        </span>
                    </div>
                </div>
                {pendingReceaveablesTotal > 0 && (
                    <div className="flex flex-col space-y-2 text-right">
                        <div className="text-center">
                            <span className="text-red-500">
                                <LucideAlertTriangle
                                    size={18}
                                    className="mr-2 inline-block"
                                />
                                Receaveables
                            </span>
                        </div>
                        <div className="text-center">
                            <span className="text-2xl font-bold">
                                {pendingReceaveablesTotal}
                            </span>
                        </div>
                    </div>
                )}
                <div className="flex flex-col space-y-2 text-right">
                    <div className="flex flex-row items-end text-sm">
                        <span className="text-blue-500">
                            <LucidePhoneIncoming size={16} />
                        </span>
                        <span className="ml-2">{contact}</span>
                    </div>
                    <div className="text-right text-sm">
                        <span>Age: </span>
                        <span className="font-bold">{age ? age : 'N/A'}</span>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PatientMiniCard;
