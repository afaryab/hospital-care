import type { Meta, StoryObj } from '@storybook/react-vite';
import PatientTableElement from '@/elements/patient/patient-table-element';
import { mockPatient, mockPatientFemale } from '@/storybook-mocks';

const meta: Meta<typeof PatientTableElement> = {
    title: 'Elements/Patient/PatientTableElement',
    component: PatientTableElement,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof PatientTableElement>;

export const Default: Story = {
    args: { patient: mockPatient },
};

export const Female: Story = {
    args: { patient: mockPatientFemale },
};
