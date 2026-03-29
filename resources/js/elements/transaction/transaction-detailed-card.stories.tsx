import TransactionDetailedCard from '@/elements/transaction/transaction-detailed-card';
import { mockTransaction, mockTransactionExpense } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

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
