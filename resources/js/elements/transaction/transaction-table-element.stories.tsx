import type { Meta, StoryObj } from '@storybook/react-vite';
import TransactionTableElement from '@/elements/transaction/transaction-table-element';
import { mockTransaction, mockTransactionExpense } from '@/storybook-mocks';

const meta: Meta<typeof TransactionTableElement> = {
    title: 'Elements/Transaction/TransactionTableElement',
    component: TransactionTableElement,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof TransactionTableElement>;

export const Income: Story = {
    args: { transaction: mockTransaction },
};

export const Expense: Story = {
    args: { transaction: mockTransactionExpense },
};
