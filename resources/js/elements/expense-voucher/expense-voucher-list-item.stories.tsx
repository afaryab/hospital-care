import ExpenseVoucherListItem from '@/elements/expense-voucher/expense-voucher-list-item';
import {
    mockExpenseVoucher,
    mockExpenseVoucherPending,
} from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ExpenseVoucherListItem> = {
    title: 'Elements/ExpenseVoucher/ExpenseVoucherListItem',
    component: ExpenseVoucherListItem,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof ExpenseVoucherListItem>;

export const Paid: Story = {
    args: { voucher: mockExpenseVoucher },
};

export const Pending: Story = {
    args: { voucher: mockExpenseVoucherPending },
};

export const Selected: Story = {
    args: { voucher: mockExpenseVoucher, selected: true },
};
