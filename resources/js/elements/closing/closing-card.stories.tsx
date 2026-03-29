import type { Meta, StoryObj } from '@storybook/react-vite';
import ClosingCard from '@/elements/closing/closing-card';
import { mockClosing, mockClosingClosed } from '@/storybook-mocks';

const meta: Meta<typeof ClosingCard> = {
    title: 'Elements/Closing/ClosingCard',
    component: ClosingCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ClosingCard>;

export const Open: Story = {
    args: { closing: mockClosing },
};

export const Closed: Story = {
    args: { closing: mockClosingClosed },
};

export const Clickable: Story = {
    args: {
        closing: mockClosing,
        onClick: () => alert('Closing clicked'),
    },
};
