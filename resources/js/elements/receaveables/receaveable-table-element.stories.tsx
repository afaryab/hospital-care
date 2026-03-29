import type { Meta, StoryObj } from '@storybook/react-vite';
import ReceaveableTableElement from '@/elements/receaveables/receaveable-table-element';
import { mockReceaveable, mockReceaveablePaid } from '@/storybook-mocks';

const meta: Meta<typeof ReceaveableTableElement> = {
    title: 'Elements/Receivable/ReceaveableTableElement',
    component: ReceaveableTableElement,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof ReceaveableTableElement>;

export const Pending: Story = {
    args: { receaveable: mockReceaveable },
};

export const Paid: Story = {
    args: { receaveable: mockReceaveablePaid },
};
