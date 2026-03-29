import PatientAdvancedSearch, {
    PatientSearchFilters,
} from '@/elements/patient/patient-advanced-search';
import type { Meta, StoryObj } from '@storybook/react-vite';
import { useState } from 'react';

const meta: Meta<typeof PatientAdvancedSearch> = {
    title: 'Elements/Patient/PatientAdvancedSearch',
    component: PatientAdvancedSearch,
    tags: ['autodocs'],
    parameters: { layout: 'padded' },
};

export default meta;
type Story = StoryObj<typeof PatientAdvancedSearch>;

function ControlledSearch() {
    const [filters, setFilters] = useState<PatientSearchFilters>({});
    return (
        <PatientAdvancedSearch
            filters={filters}
            onChange={setFilters}
            onSearch={() => alert(`Search: ${JSON.stringify(filters)}`)}
            onReset={() => setFilters({})}
        />
    );
}

export const Default: Story = {
    render: () => <ControlledSearch />,
};

function PrefilledSearch() {
    const [filters, setFilters] = useState<PatientSearchFilters>({
        name: 'Ahmed',
        ps_number: 'PS/2026',
    });
    return (
        <PatientAdvancedSearch
            filters={filters}
            onChange={setFilters}
            onSearch={() => alert('Search triggered')}
            onReset={() => setFilters({})}
        />
    );
}

export const WithPrefilled: Story = {
    render: () => <PrefilledSearch />,
};
