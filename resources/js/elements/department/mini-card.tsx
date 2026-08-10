import { counterSelectDepartmentService } from '@/routes';
import { Patient, ServiceDepartment } from '@/types';
import { Link } from '@inertiajs/react';
import clsx from 'clsx';

export default function DepartmentMiniCard({
    department,
    recestitation,
    patient,
    className,
}: {
    department: ServiceDepartment;
    recestitation: boolean;
    patient: Patient;
    className?: string;
}) {
    console.log('Recestitation prop in DepartmentMiniCard:', recestitation);
    return (
        <div
            key={department.id}
            className={clsx(
                'items-left flex flex-col justify-start border border-gray-200 bg-white dark:bg-neutral-950 dark:text-white',
                className,
            )}
        >
            <Link
                href={
                    counterSelectDepartmentService({
                        pYear: patient.year,
                        pMonth: patient.month,
                        number: patient.number,
                        departmentKey:
                            department.slug + (recestitation ? '-RECES' : ''),
                    }).url
                }
                className="flex h-full w-32 flex-col items-center justify-center"
            >
                <img
                    src={department.image_url}
                    alt={department.name}
                    className="h-12 w-12 object-contain"
                />
                <span className="mt-2 max-w-28 text-center text-sm">
                    {department.name}&nbsp;
                    {recestitation && (
                        <span className="mt-1 rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-800">
                            Recesitation
                        </span>
                    )}
                </span>
            </Link>
        </div>
    );
}
