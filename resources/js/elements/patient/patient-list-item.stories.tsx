import PatientListItem from '@/elements/patient/patient-list-item';
import { mockPatient, mockPatientFemale } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof PatientListItem> = {
    title: 'Elements/Patient/PatientListItem',
    component: PatientListItem,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof PatientListItem>;

export const Default: Story = {
    args: { patient: mockPatient },
};

export const Selected: Story = {
    args: { patient: mockPatient, selected: true },
};

export const Clickable: Story = {
    args: {
        patient: mockPatientFemale,
        onClick: () => alert('Selected'),
    },
};
