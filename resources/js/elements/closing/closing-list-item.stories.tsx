import type { Meta, StoryObj } from '@storybook/react-vite';
import ClosingListItem from '@/elements/closing/closing-list-item';
import { mockClosing, mockClosingClosed } from '@/storybook-mocks';

const meta: Meta<typeof ClosingListItem> = {
    title: 'Elements/Closing/ClosingListItem',
    component: ClosingListItem,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof ClosingListItem>;

export const Open: Story = {
    args: { closing: mockClosing },
};

export const Closed: Story = {
    args: { closing: mockClosingClosed },
};

export const Selected: Story = {
    args: { closing: mockClosing, selected: true },
};
