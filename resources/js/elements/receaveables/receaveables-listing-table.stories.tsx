import type { Meta, StoryObj } from '@storybook/react-vite';
import ReceaveablesListingTable from '@/elements/receaveables/receaveables-listing-table';
import { mockReceaveables } from '@/storybook-mocks';

const meta: Meta<typeof ReceaveablesListingTable> = {
    title: 'Elements/Receivable/ReceaveablesListingTable',
    component: ReceaveablesListingTable,
    tags: ['autodocs'],
    parameters: { layout: 'fullscreen' },
};

export default meta;
type Story = StoryObj<typeof ReceaveablesListingTable>;

export const Default: Story = {
    args: { receaveables: mockReceaveables },
};

export const WithSelect: Story = {
    args: {
        receaveables: mockReceaveables,
        onSelect: (r) => alert(`Selected: ${r.transaction?.tr_number}`),
    },
};

export const Empty: Story = {
    args: { receaveables: [] },
};
