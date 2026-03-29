import type { Meta, StoryObj } from '@storybook/react-vite';
import PatientCard from '@/elements/patient/patient-card';
import { mockPatient, mockPatientFemale } from '@/storybook-mocks';

const meta: Meta<typeof PatientCard> = {
    title: 'Elements/Patient/PatientCard',
    component: PatientCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof PatientCard>;

export const Default: Story = {
    args: { patient: mockPatient },
};

export const Female: Story = {
    args: { patient: mockPatientFemale },
};

export const Clickable: Story = {
    args: {
        patient: mockPatient,
        onClick: () => alert('Patient clicked'),
    },
};
