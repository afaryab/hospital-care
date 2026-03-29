import TransactionSelectInput, {
    TransactionSelectOption,
} from '@/elements/transaction/transaction-select-input';
import { mockTransactions } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const options: TransactionSelectOption[] = mockTransactions.map((t) => ({
    value: t.id.toString(),
    label: `${t.tr_number} — PKR ${t.amount}`,
    transaction: t,
}));

const meta: Meta<typeof TransactionSelectInput> = {
    title: 'Elements/Transaction/TransactionSelectInput',
    component: TransactionSelectInput,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof TransactionSelectInput>;

export const Default: Story = {
    args: { options },
};

export const Disabled: Story = {
    args: { options, disabled: true },
};
