import type { Meta, StoryObj } from '@storybook/react-vite';
import ClosingsListingTable from '@/elements/closing/closings-listing-table';
import { mockClosings } from '@/storybook-mocks';

const meta: Meta<typeof ClosingsListingTable> = {
    title: 'Elements/Closing/ClosingsListingTable',
    component: ClosingsListingTable,
    tags: ['autodocs'],
    parameters: { layout: 'fullscreen' },
};

export default meta;
type Story = StoryObj<typeof ClosingsListingTable>;

export const Default: Story = {
    args: { closings: mockClosings },
};

export const WithSelect: Story = {
    args: {
        closings: mockClosings,
        onSelect: (c) => alert(`Selected: ${c.ct_number}`),
    },
};

export const Empty: Story = {
    args: { closings: [] },
};
