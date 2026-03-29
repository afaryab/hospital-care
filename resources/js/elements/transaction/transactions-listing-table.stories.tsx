import type { Meta, StoryObj } from '@storybook/react-vite';
import TransactionsListingTable from '@/elements/transaction/transactions-listing-table';
import { mockTransactions } from '@/storybook-mocks';

const meta: Meta<typeof TransactionsListingTable> = {
    title: 'Elements/Transaction/TransactionsListingTable',
    component: TransactionsListingTable,
    tags: ['autodocs'],
    parameters: { layout: 'fullscreen' },
};

export default meta;
type Story = StoryObj<typeof TransactionsListingTable>;

export const Default: Story = {
    args: { transactions: mockTransactions },
};

export const WithSelect: Story = {
    args: {
        transactions: mockTransactions,
        onSelect: (t) => alert(`Selected: ${t.tr_number}`),
    },
};

export const Empty: Story = {
    args: { transactions: [] },
};
