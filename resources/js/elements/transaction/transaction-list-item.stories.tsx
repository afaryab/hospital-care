import TransactionListItem from '@/elements/transaction/transaction-list-item';
import { mockTransaction, mockTransactionExpense } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof TransactionListItem> = {
    title: 'Elements/Transaction/TransactionListItem',
    component: TransactionListItem,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof TransactionListItem>;

export const Income: Story = {
    args: { transaction: mockTransaction },
};

export const Expense: Story = {
    args: { transaction: mockTransactionExpense },
};

export const Selected: Story = {
    args: { transaction: mockTransaction, selected: true },
};
