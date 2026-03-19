import type { Meta, StoryObj } from '@storybook/react-vite';
import ReceaveableCard from '@/elements/receaveables/receaveable-card';
import { mockReceaveable, mockReceaveablePaid } from '@/storybook-mocks';

const meta: Meta<typeof ReceaveableCard> = {
    title: 'Elements/Receivable/ReceaveableCard',
    component: ReceaveableCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ReceaveableCard>;

export const Pending: Story = {
    args: { receaveable: mockReceaveable },
};

export const Paid: Story = {
    args: { receaveable: mockReceaveablePaid },
};

export const Clickable: Story = {
    args: {
        receaveable: mockReceaveable,
        onClick: () => alert('Receivable clicked'),
    },
};
