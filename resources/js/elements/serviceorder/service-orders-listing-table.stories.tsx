import ServiceOrdersListingTable from '@/elements/serviceorder/service-orders-listing-table';
import { mockServiceOrders } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ServiceOrdersListingTable> = {
    title: 'Elements/ServiceOrder/ServiceOrdersListingTable',
    component: ServiceOrdersListingTable,
    tags: ['autodocs'],
    parameters: { layout: 'fullscreen' },
};

export default meta;
type Story = StoryObj<typeof ServiceOrdersListingTable>;

export const Default: Story = {
    args: { serviceOrders: mockServiceOrders },
};

export const WithSelect: Story = {
    args: {
        serviceOrders: mockServiceOrders,
        onSelect: (so) => alert(`Selected: ${so.so_number}`),
    },
};

export const Empty: Story = {
    args: { serviceOrders: [] },
};
