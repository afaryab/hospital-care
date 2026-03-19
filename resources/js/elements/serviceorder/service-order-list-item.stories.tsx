import type { Meta, StoryObj } from '@storybook/react-vite';
import ServiceOrderListItem from '@/elements/serviceorder/service-order-list-item';
import { mockServiceOrder, mockServiceOrderLab } from '@/storybook-mocks';

const meta: Meta<typeof ServiceOrderListItem> = {
    title: 'Elements/ServiceOrder/ServiceOrderListItem',
    component: ServiceOrderListItem,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof ServiceOrderListItem>;

export const OPD: Story = {
    args: { serviceOrder: mockServiceOrder },
};

export const Lab: Story = {
    args: { serviceOrder: mockServiceOrderLab },
};

export const Selected: Story = {
    args: { serviceOrder: mockServiceOrder, selected: true },
};
