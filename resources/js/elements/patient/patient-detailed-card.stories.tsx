import PatientDetailedCard from '@/elements/patient/patient-detailed-card';
import { mockPatient, mockPatientFemale } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof PatientDetailedCard> = {
    title: 'Elements/Patient/PatientDetailedCard',
    component: PatientDetailedCard,
    tags: ['autodocs'],
    parameters: { layout: 'centered' },
};

export default meta;
type Story = StoryObj<typeof PatientDetailedCard>;

export const Default: Story = {
    args: { patient: mockPatient },
};

export const Female: Story = {
    args: { patient: mockPatientFemale },
};
