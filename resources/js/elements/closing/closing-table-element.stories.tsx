import ClosingTableElement from '@/elements/closing/closing-table-element';
import { mockClosing, mockClosingClosed } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof ClosingTableElement> = {
    title: 'Elements/Closing/ClosingTableElement',
    component: ClosingTableElement,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof ClosingTableElement>;

export const Open: Story = {
    args: { closing: mockClosing },
};

export const Closed: Story = {
    args: { closing: mockClosingClosed },
};
