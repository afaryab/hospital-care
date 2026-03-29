import ExpenseVouchersListingTable from '@/elements/expense-voucher/expense-vouchers-listing-table';
import { mockExpenseVouchers } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ExpenseVouchersListingTable> = {
    title: 'Elements/ExpenseVoucher/ExpenseVouchersListingTable',
    component: ExpenseVouchersListingTable,
    tags: ['autodocs'],
    parameters: { layout: 'fullscreen' },
};

export default meta;
type Story = StoryObj<typeof ExpenseVouchersListingTable>;

export const Default: Story = {
    args: { vouchers: mockExpenseVouchers },
};

export const WithSelect: Story = {
    args: {
        vouchers: mockExpenseVouchers,
        onSelect: (v) => alert(`Selected: ${v.vc_number}`),
    },
};

export const Empty: Story = {
    args: { vouchers: [] },
};
