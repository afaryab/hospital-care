import type { Meta, StoryObj } from '@storybook/react-vite';
import ServiceOrderDetailedCard from '@/elements/serviceorder/service-order-detailed-card';
import { mockServiceOrder, mockServiceOrderLab } from '@/storybook-mocks';

const meta: Meta<typeof ServiceOrderDetailedCard> = {
    title: 'Elements/ServiceOrder/ServiceOrderDetailedCard',
    component: ServiceOrderDetailedCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ServiceOrderDetailedCard>;

export const OPD: Story = {
    args: { serviceOrder: mockServiceOrder },
};

export const Lab: Story = {
    args: { serviceOrder: mockServiceOrderLab },
};
