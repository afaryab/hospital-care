import { patientsRegisterPsNumberDepartmentService } from '@/routes';
import { Patient } from '@/types';
import { Link } from '@inertiajs/react';
import clsx from 'clsx';
import React from 'react';

interface PatientTreatmentsHistoryCardProps {
    patient: Patient;
    className?: string;
    departmentKey: string;
}

const PatientTreatmentsHistoryCard: React.FC<
    PatientTreatmentsHistoryCardProps
> = ({ patient, className, departmentKey }) => {
    console.log(
        patient,
        departmentKey,
        patient.treatments.filter(
            (treatment) => treatment.type === departmentKey,
        ),
    );
    return (
        <div className={clsx('bg-white', className)}>
            {patient.treatments.length === 0 ? (
                <p className="text-gray-500">No treatments found</p>
            ) : (
                <div className="space-y-3">
                    {patient.treatments
                        .filter((treatment) => treatment.type === departmentKey)
                        .map((treatment) => (
                            <Link
                                key={treatment.so_number}
                                href={patientsRegisterPsNumberDepartmentService(
                                    {
                                        year: patient.year,
                                        month: patient.month,
                                        number: patient.number,
                                        departmentKey:
                                            treatment.departmentKey ?? '',
                                        serviceNumber:
                                            treatment.serviceNumber ?? '',
                                    },
                                )}
                                className="flex flex-col items-center justify-between rounded-md border p-3 md:flex-row"
                            >
                                <div className="w-full">
                                    <p>
                                        {new Date(
                                            treatment.created_at!,
                                        ).toLocaleDateString()}
                                    </p>
                                    <p>
                                        {new Date(
                                            treatment.created_at!,
                                        ).toLocaleTimeString()}
                                    </p>
                                </div>
                                <div className="w-full text-right"></div>
                            </Link>
                        ))}
                </div>
            )}
        </div>
    );
};

export default PatientTreatmentsHistoryCard;
