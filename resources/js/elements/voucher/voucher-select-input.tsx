import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ExpenseVoucher } from '@/types';

export type VoucherSelectOption = {
    value: string;
    label: string;
    voucher?: ExpenseVoucher;
};

type VoucherSelectInputProps = {
    options: VoucherSelectOption[];
    value?: string;
    placeholder?: string;
    onValueChange?: (value: string) => void;
    disabled?: boolean;
    searchable?: boolean;
};

const VoucherSelectInput: React.FC<VoucherSelectInputProps> = ({
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

export default VoucherSelectInput;
