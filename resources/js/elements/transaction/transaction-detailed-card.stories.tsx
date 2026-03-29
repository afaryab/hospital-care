import type { Meta, StoryObj } from '@storybook/react-vite';
import TransactionDetailedCard from '@/elements/transaction/transaction-detailed-card';
import { mockTransaction, mockTransactionExpense } from '@/storybook-mocks';

const meta: Meta<typeof TransactionDetailedCard> = {
    title: 'Elements/Transaction/TransactionDetailedCard',
    component: TransactionDetailedCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof TransactionDetailedCard>;

export const Income: Story = {
    args: { transaction: mockTransaction },
};

export const Expense: Story = {
    args: { transaction: mockTransactionExpense },
};
