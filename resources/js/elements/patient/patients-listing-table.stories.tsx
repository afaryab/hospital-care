import PatientsListingTable from '@/elements/patient/patients-listing-table';
import { mockPatients } from '@/storybook-mocks';
import type { Meta, StoryObj } from '@storybook/react-vite';

const meta: Meta<typeof PatientsListingTable> = {
    title: 'Elements/Patient/PatientsListingTable',
    component: PatientsListingTable,
    tags: ['autodocs'],
    parameters: { layout: 'fullscreen' },
};

export default meta;
type Story = StoryObj<typeof PatientsListingTable>;

export const Default: Story = {
    args: { patients: mockPatients },
};

export const WithSelect: Story = {
    args: {
        patients: mockPatients,
        onSelect: (p) => alert(`Selected: ${p.name}`),
    },
};

export const Empty: Story = {
    args: { patients: [] },
};
