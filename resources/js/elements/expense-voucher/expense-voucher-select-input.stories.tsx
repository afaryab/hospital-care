import ExpenseVoucherSelectInput, {
    ExpenseVoucherSelectOption,
} from '@/elements/expense-voucher/expense-voucher-select-input';
import { mockExpenseVouchers } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const options: ExpenseVoucherSelectOption[] = mockExpenseVouchers.map((v) => ({
    value: v.id.toString(),
    label: `${v.vc_number} — PKR ${v.amount}`,
    voucher: v,
}));

const meta: Meta<typeof ExpenseVoucherSelectInput> = {
    title: 'Elements/ExpenseVoucher/ExpenseVoucherSelectInput',
    component: ExpenseVoucherSelectInput,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ExpenseVoucherSelectInput>;

export const Default: Story = {
    args: { options },
};

export const Disabled: Story = {
    args: { options, disabled: true },
};
