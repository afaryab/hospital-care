import type { Meta, StoryObj } from '@storybook/react-vite';
import ReceivableTableElement from '@/elements/receivable/receivable-table-element';
import { mockReceaveable, mockReceaveablePaid } from '@/storybook-mocks';

const meta: Meta<typeof ReceivableTableElement> = {
    title: 'Elements/Receivable/ReceivableTableElement',
    component: ReceivableTableElement,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ReceivableTableElement>;

export const Pending: Story = {
    args: { receivable: mockReceaveable },
};

export const Paid: Story = {
    args: { receivable: mockReceaveablePaid },
};
