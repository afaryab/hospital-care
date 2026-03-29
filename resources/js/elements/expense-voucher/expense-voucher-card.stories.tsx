import ExpenseVoucherCard from '@/elements/expense-voucher/expense-voucher-card';
import {
    mockExpenseVoucher,
    mockExpenseVoucherPending,
} from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ExpenseVoucherCard> = {
    title: 'Elements/ExpenseVoucher/ExpenseVoucherCard',
    component: ExpenseVoucherCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ExpenseVoucherCard>;

export const Paid: Story = {
    args: { voucher: mockExpenseVoucher },
};

export const Pending: Story = {
    args: { voucher: mockExpenseVoucherPending },
};

export const Clickable: Story = {
    args: {
        voucher: mockExpenseVoucher,
        onClick: () => alert('Voucher clicked'),
    },
};
