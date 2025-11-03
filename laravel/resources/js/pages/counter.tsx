import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import PatientHistoryMiniTree from '@/elements/history/patient-history-mini-tree';
import FindOrSelectPatient from '@/elements/patient/find-or-select-patient';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { AlertTriangle } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Counter',
        href: '',
    }
];

export default function Counter() {


    const [isModalOpen, setIsModalOpen] = useState(false);
  
    const handlePatientSelected = (patient: any) => {
        console.log('Selected patient:', patient);
        // Handle selected patient
    };

    const handleNewPatientCreated = (patient: any) => {
        console.log('New patient created:', patient);
        // Handle new patient
    };
    const defaultPatientData = {
    patientName: "John Doe",
    patientId: "PS/2025/10/000001",
    departments: [
      {
        id: 'emergency',
        name: 'Emergency',
        icon: <AlertTriangle className="w-4 h-4" />,
        color: 'text-red-600',
        treatments: [
          { 
            id: 'uerco389rcnojw8qr4', 
            name: 'Emergency Treatment #1', 
            date: '2025-11-01', 
            doctor: 'Dr. Smith', 
            status: 'completed' 
          }
        ]
      }
      // ... more departments
    ]
  };


    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Counter" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                        <div>
                            <div className="absolute inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                                <button
                                    onClick={() => setIsModalOpen(true)}
                                    className="rounded-lg bg-white px-6 py-3 text-sm font-medium text-gray-900 shadow-lg transition-all hover:bg-gray-50 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700"
                                >
                                    Find or Select Patient
                                </button>
                            </div>
                        </div>
                        <FindOrSelectPatient
                            isOpen={isModalOpen}
                            onClose={() => setIsModalOpen(false)}
                            onPatientSelected={handlePatientSelected}
                            onNewPatientCreated={handleNewPatientCreated}
                            />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                </div>
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="col-span-2 relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                        <PatientHistoryMiniTree patientData={defaultPatientData} />
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
