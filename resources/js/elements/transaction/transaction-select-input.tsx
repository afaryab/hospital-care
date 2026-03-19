import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Transaction } from '@/types';

export type TransactionSelectOption = {
    value: string;
    label: string;
    transaction?: Transaction;
};

type TransactionSelectInputProps = {
    options: TransactionSelectOption[];
    value?: string;
    placeholder?: string;
    onValueChange?: (value: string) => void;
    disabled?: boolean;
    searchable?: boolean;
};

const TransactionSelectInput: React.FC<TransactionSelectInputProps> = ({
    options,
    value,
    placeholder = 'Select transaction',
    onValueChange,
    disabled = false,
    searchable = true,
}) => (
    <Select value={value} onValueChange={onValueChange} disabled={disabled}>
        <SelectTrigger>
            <SelectValue placeholder={placeholder} />
        </SelectTrigger>
        <SelectContent searchable={searchable} searchPlaceholder="Search transactions...">
            {options.map((option) => (
                <SelectItem key={option.value} value={option.value} textValue={option.label}>
                    {option.label}
                </SelectItem>
            ))}
        </SelectContent>
    </Select>
);

export default TransactionSelectInput;
