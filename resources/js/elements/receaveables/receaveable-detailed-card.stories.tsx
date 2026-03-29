import type { Meta, StoryObj } from '@storybook/react-vite';
import ReceaveableDetailedCard from '@/elements/receaveables/receaveable-detailed-card';
import { mockReceaveable, mockReceaveablePaid } from '@/storybook-mocks';

const meta: Meta<typeof ReceaveableDetailedCard> = {
    title: 'Elements/Receaveables/ReceaveableDetailedCard',
    component: ReceaveableDetailedCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ReceaveableDetailedCard>;

export const Pending: Story = {
    args: { receaveable: mockReceaveable },
};

export const Paid: Story = {
    args: { receaveable: mockReceaveablePaid },
};
