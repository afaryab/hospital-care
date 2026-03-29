import ServiceOrderCard from '@/elements/serviceorder/service-order-card';
import { mockServiceOrder, mockServiceOrderLab } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ServiceOrderCard> = {
    title: 'Elements/ServiceOrder/ServiceOrderCard',
    component: ServiceOrderCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ServiceOrderCard>;

export const OPD: Story = {
    args: { serviceOrder: mockServiceOrder },
};

export const Lab: Story = {
    args: { serviceOrder: mockServiceOrderLab },
};

export const Clickable: Story = {
    args: {
        serviceOrder: mockServiceOrder,
        onClick: () => alert('Service order clicked'),
    },
};
