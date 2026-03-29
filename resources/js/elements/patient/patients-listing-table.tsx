import { Badge } from '@/components/ui/badge';
import { Patient } from '@/types';

type PatientsListingTableProps = {
    patients: Patient[];
    onSelect?: (patient: Patient) => void;
    emptyMessage?: string;
};

const genderBadgeVariant = (_gender: Patient['gender']) => 'outline' as const;

const PatientsListingTable: React.FC<PatientsListingTableProps> = ({
    patients,
    onSelect,
    emptyMessage = 'No patients found.',
}) => {
    if (!patients.length) {
        return (
            <p className="py-6 text-center text-sm text-muted-foreground">
                {emptyMessage}
            </p>
        );
    }

    return (
        <div className="overflow-x-auto rounded-lg border">
            <table className="w-full text-sm">
                <thead className="bg-muted/50 text-xs text-muted-foreground uppercase">
                    <tr>
                        <th className="px-4 py-2 text-left">Patient</th>
                        <th className="px-4 py-2 text-left">PS Number</th>
                        <th className="px-4 py-2 text-left">Age</th>
                        <th className="px-4 py-2 text-left">Contact</th>
                        <th className="px-4 py-2 text-left">Gender</th>
                    </tr>
                </thead>
                <tbody className="divide-y">
                    {patients.map((patient) => (
                        <tr
                            key={patient.id}
                            className={
                                onSelect
                                    ? 'cursor-pointer transition-colors hover:bg-accent'
                                    : ''
                            }
                            onClick={() => onSelect?.(patient)}
                        >
                            <td className="px-4 py-2 font-medium">
                                {patient.name}
                            </td>
                            <td className="px-4 py-2 font-mono text-xs">
                                {patient.ps_number}
                            </td>
                            <td className="px-4 py-2">
                                {patient.age ?? '-'} yrs
                            </td>
                            <td className="px-4 py-2 font-mono text-xs">
                                {patient.contact || '-'}
                            </td>
                            <td className="px-4 py-2">
                                <Badge
                                    variant={genderBadgeVariant(patient.gender)}
                                    className="text-xs"
                                >
                                    {patient.gender?.toUpperCase()}
                                </Badge>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default PatientsListingTable;
