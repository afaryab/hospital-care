import { counterSelectDepartmentService } from "@/routes";
import { Link } from "@inertiajs/react";
import clsx from "clsx";



export default function DepartmentMiniCard({ department, patient, className}:{any}) {
    return <div key={department.id} className={clsx('flex flex-col items-left justify-start bg-white dark:bg-neutral-950 dark:text-white border border-gray-200', className)}>
        <Link href={counterSelectDepartmentService({
            pYear: patient.year,
            pMonth: patient.month,
            number: patient.number,
            departmentKey: department.slug
        }).url} className='h-full w-32 flex flex-col items-center justify-center'>
            <img src={department.image} alt={department.name} className='w-12 h-12 object-contain'/>
            <span className='text-center text-sm mt-2 max-w-28'>{department.name}</span>
        </Link>
    </div>
}