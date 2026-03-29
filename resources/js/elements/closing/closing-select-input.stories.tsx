import type { Meta, StoryObj } from '@storybook/react-vite';
import ClosingSelectInput, { ClosingSelectOption } from '@/elements/closing/closing-select-input';
import { mockClosings } from '@/storybook-mocks';

const options: ClosingSelectOption[] = mockClosings.map((c) => ({
    value: c.id.toString(),
    label: `${c.ct_number} — ${c.status}`,
    closing: c,
}));

const meta: Meta<typeof ClosingSelectInput> = {
    title: 'Elements/Closing/ClosingSelectInput',
    component: ClosingSelectInput,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof ClosingSelectInput>;

export const Default: Story = {
    args: { options },
};

export const Disabled: Story = {
    args: { options, disabled: true },
};
