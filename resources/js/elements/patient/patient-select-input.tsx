import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Patient } from '@/types';

export type PatientSelectOption = {
    value: string;
    label: string;
    patient?: Patient;
};

type PatientSelectInputProps = {
    options: PatientSelectOption[];
    value?: string;
    placeholder?: string;
    onValueChange?: (value: string) => void;
    disabled?: boolean;
    searchable?: boolean;
};

const PatientSelectInput: React.FC<PatientSelectInputProps> = ({
    options,
    value,
    placeholder = 'Select patient',
    onValueChange,
    disabled = false,
    searchable = true,
}) => (
    <Select value={value} onValueChange={onValueChange} disabled={disabled}>
        <SelectTrigger>
            <SelectValue placeholder={placeholder} />
        </SelectTrigger>
        <SelectContent
            searchable={searchable}
            searchPlaceholder="Search patients..."
        >
            {options.map((option) => (
                <SelectItem
                    key={option.value}
                    value={option.value}
                    textValue={option.label}
                >
                    {option.label}
                </SelectItem>
            ))}
        </SelectContent>
    </Select>
);

export default PatientSelectInput;
