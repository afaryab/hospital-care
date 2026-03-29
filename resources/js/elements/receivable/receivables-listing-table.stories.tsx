import type { Meta, StoryObj } from '@storybook/react-vite';
import ReceivablesListingTable from '@/elements/receivable/receivables-listing-table';
import { mockReceaveables } from '@/storybook-mocks';

const meta: Meta<typeof ReceivablesListingTable> = {
    title: 'Elements/Receivable/ReceivablesListingTable',
    component: ReceivablesListingTable,
    tags: ['autodocs'],
    parameters: { layout: 'fullscreen' },
};

export default meta;
type Story = StoryObj<typeof ReceivablesListingTable>;

export const Default: Story = {
    args: { receaveables: mockReceaveables },
};

export const WithSelect: Story = {
    args: {
        receaveables: mockReceaveables,
        onSelect: (r) => alert(`Selected receivable #${r.id}`),
    },
};

export const Empty: Story = {
    args: { receaveables: [] },
};
