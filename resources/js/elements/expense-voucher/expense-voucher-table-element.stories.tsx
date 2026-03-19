import type { Meta, StoryObj } from '@storybook/react-vite';
import ExpenseVoucherTableElement from '@/elements/expense-voucher/expense-voucher-table-element';
import { mockExpenseVoucher, mockExpenseVoucherPending } from '@/storybook-mocks';

const meta: Meta<typeof ExpenseVoucherTableElement> = {
    title: 'Elements/ExpenseVoucher/ExpenseVoucherTableElement',
    component: ExpenseVoucherTableElement,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof ExpenseVoucherTableElement>;

export const Paid: Story = {
    args: { voucher: mockExpenseVoucher },
};

export const Pending: Story = {
    args: { voucher: mockExpenseVoucherPending },
};
