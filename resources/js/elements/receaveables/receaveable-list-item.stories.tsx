import ReceaveableListItem from '@/elements/receaveables/receaveable-list-item';
import { mockReceaveable, mockReceaveablePaid } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ReceaveableListItem> = {
    title: 'Elements/Receivable/ReceaveableListItem',
    component: ReceaveableListItem,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof ReceaveableListItem>;

export const Pending: Story = {
    args: { receaveable: mockReceaveable },
};

export const Paid: Story = {
    args: { receaveable: mockReceaveablePaid },
};

export const Selected: Story = {
    args: { receaveable: mockReceaveable, selected: true },
};
