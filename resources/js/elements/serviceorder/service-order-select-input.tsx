import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ServiceOrder } from '@/types';

export type ServiceOrderSelectOption = {
    value: string;
    label: string;
    serviceOrder?: ServiceOrder;
};

type ServiceOrderSelectInputProps = {
    options: ServiceOrderSelectOption[];
    value?: string;
    placeholder?: string;
    onValueChange?: (value: string) => void;
    disabled?: boolean;
    searchable?: boolean;
};

const ServiceOrderSelectInput: React.FC<ServiceOrderSelectInputProps> = ({
    options,
    value,
    placeholder = 'Select service order',
    onValueChange,
    disabled = false,
    searchable = true,
}) => (
    <Select value={value} onValueChange={onValueChange} disabled={disabled}>
        <SelectTrigger>
            <SelectValue placeholder={placeholder} />
        </SelectTrigger>
        <SelectContent searchable={searchable} searchPlaceholder="Search service orders...">
            {options.map((option) => (
                <SelectItem key={option.value} value={option.value} textValue={option.label}>
                    {option.label}
                </SelectItem>
            ))}
        </SelectContent>
    </Select>
);

export default ServiceOrderSelectInput;
