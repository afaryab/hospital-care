import TransactionCard from '@/elements/transaction/transaction-card';
import { mockTransaction, mockTransactionExpense } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof TransactionCard> = {
    title: 'Elements/Transaction/TransactionCard',
    component: TransactionCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof TransactionCard>;

export const Income: Story = {
    args: { transaction: mockTransaction },
};

export const Expense: Story = {
    args: { transaction: mockTransactionExpense },
};

export const Clickable: Story = {
    args: {
        transaction: mockTransaction,
        onClick: () => alert('Transaction clicked'),
    },
};
