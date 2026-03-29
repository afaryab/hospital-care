import ReceivableListItem from '@/elements/receivable/receivable-list-item';
import { mockReceaveable, mockReceaveablePaid } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ReceivableListItem> = {
    title: 'Elements/Receivable/ReceivableListItem',
    component: ReceivableListItem,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof ReceivableListItem>;

export const Pending: Story = {
    args: { receivable: mockReceaveable },
};

export const Paid: Story = {
    args: { receivable: mockReceaveablePaid },
};

export const Selected: Story = {
    args: { receivable: mockReceaveable, selected: true },
};

export const Clickable: Story = {
    args: {
        receivable: mockReceaveable,
        onClick: () => alert('Receivable clicked'),
    },
};
