import type { Meta, StoryObj } from '@storybook/react-vite';
import ServiceOrderSelectInput, { ServiceOrderSelectOption } from '@/elements/serviceorder/service-order-select-input';
import { mockServiceOrders } from '@/storybook-mocks';

const options: ServiceOrderSelectOption[] = mockServiceOrders.map((so) => ({
    value: so.id?.toString() ?? so.so_number,
    label: `${so.so_number} — ${so.type}`,
    serviceOrder: so,
}));

const meta: Meta<typeof ServiceOrderSelectInput> = {
    title: 'Elements/ServiceOrder/ServiceOrderSelectInput',
    component: ServiceOrderSelectInput,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ServiceOrderSelectInput>;

export const Default: Story = {
    args: { options },
};

export const Disabled: Story = {
    args: { options, disabled: true },
};
