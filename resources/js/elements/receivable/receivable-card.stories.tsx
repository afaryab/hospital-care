import type { Meta, StoryObj } from '@storybook/react-vite';
import ReceivableCard from '@/elements/receivable/receivable-card';
import { mockReceaveable, mockReceaveablePaid } from '@/storybook-mocks';

const meta: Meta<typeof ReceivableCard> = {
    title: 'Elements/Receivable/ReceivableCard',
    component: ReceivableCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ReceivableCard>;

export const Pending: Story = {
    args: { receivable: mockReceaveable },
};

export const Paid: Story = {
    args: { receivable: mockReceaveablePaid },
};

export const Clickable: Story = {
    args: {
        receivable: mockReceaveable,
        onClick: () => alert('Receivable clicked'),
    },
};
