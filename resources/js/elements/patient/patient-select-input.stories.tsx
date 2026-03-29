import type { Meta, StoryObj } from '@storybook/react-vite';
import PatientSelectInput, { PatientSelectOption } from '@/elements/patient/patient-select-input';
import { mockPatients } from '@/storybook-mocks';

const options: PatientSelectOption[] = mockPatients.map((p) => ({
    value: p.id.toString(),
    label: `${p.name} (${p.ps_number})`,
    patient: p,
}));

const meta: Meta<typeof PatientSelectInput> = {
    title: 'Elements/Patient/PatientSelectInput',
    component: PatientSelectInput,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof PatientSelectInput>;

export const Default: Story = {
    args: { options },
};

export const Disabled: Story = {
    args: { options, disabled: true },
};
