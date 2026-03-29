import ClosingDetailedCard from '@/elements/closing/closing-detailed-card';
import { mockClosing, mockClosingClosed } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ClosingDetailedCard> = {
    title: 'Elements/Closing/ClosingDetailedCard',
    component: ClosingDetailedCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ClosingDetailedCard>;

export const Open: Story = {
    args: { closing: mockClosing },
};

export const Closed: Story = {
    args: { closing: mockClosingClosed },
};
