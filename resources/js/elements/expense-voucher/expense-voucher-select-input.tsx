import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ExpenseVoucher } from '@/types';

export type ExpenseVoucherSelectOption = {
    value: string;
    label: string;
    voucher?: ExpenseVoucher;
};

type ExpenseVoucherSelectInputProps = {
    options: ExpenseVoucherSelectOption[];
    value?: string;
    placeholder?: string;
    onValueChange?: (value: string) => void;
    disabled?: boolean;
    searchable?: boolean;
};

const ExpenseVoucherSelectInput: React.FC<ExpenseVoucherSelectInputProps> = ({
    options,
    value,
    placeholder = 'Select voucher',
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
            searchPlaceholder="Search vouchers..."
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

export default ExpenseVoucherSelectInput;
