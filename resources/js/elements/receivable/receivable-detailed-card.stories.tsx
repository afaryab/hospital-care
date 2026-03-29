import ReceivableDetailedCard from '@/elements/receivable/receivable-detailed-card';
import { mockReceaveable, mockReceaveablePaid } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ReceivableDetailedCard> = {
    title: 'Elements/Receivable/ReceivableDetailedCard',
    component: ReceivableDetailedCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ReceivableDetailedCard>;

export const Pending: Story = {
    args: { receivable: mockReceaveable },
};

export const Paid: Story = {
    args: { receivable: mockReceaveablePaid },
};
