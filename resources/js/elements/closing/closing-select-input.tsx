import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Closing } from '@/types';

export type ClosingSelectOption = {
    value: string;
    label: string;
    closing?: Closing;
};

type ClosingSelectInputProps = {
    options: ClosingSelectOption[];
    value?: string;
    placeholder?: string;
    onValueChange?: (value: string) => void;
    disabled?: boolean;
    searchable?: boolean;
};

const ClosingSelectInput: React.FC<ClosingSelectInputProps> = ({
    options,
    value,
    placeholder = 'Select closing',
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
            searchPlaceholder="Search closings..."
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

export default ClosingSelectInput;
