import type { Meta, StoryObj } from '@storybook/react-vite';
import ServiceOrderTableElement from '@/elements/serviceorder/service-order-table-element';
import { mockServiceOrder, mockServiceOrderLab } from '@/storybook-mocks';

const meta: Meta<typeof ServiceOrderTableElement> = {
    title: 'Elements/ServiceOrder/ServiceOrderTableElement',
    component: ServiceOrderTableElement,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof ServiceOrderTableElement>;

export const OPD: Story = {
    args: { serviceOrder: mockServiceOrder },
};

export const Lab: Story = {
    args: { serviceOrder: mockServiceOrderLab },
};
