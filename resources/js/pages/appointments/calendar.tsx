import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { appointmentCancel, appointmentsCalendar, home } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import { useMemo, useState } from 'react';
import { toast } from 'sonner';

type Appointment = {
    id: number;
    appointment_number: string;
    patient?: { name: string; ps_number: string };
    service?: { name: string };
    doctor?: { name: string };
    scheduled_at: string;
    priority_mode: 'priority' | 'medium' | 'standard';
    status: 'scheduled' | 'checked_in' | 'no_show' | 'cancelled';
};

type AppointmentsCalendarProps = {
    appointments: Appointment[];
    month: string;
    [key: string]: unknown;
};

const priorityBadge: Record<string, string> = {
    priority: 'bg-red-100 text-red-700',
    medium: 'bg-amber-100 text-amber-700',
    standard: 'bg-slate-100 text-slate-700',
};

const statusBadge: Record<string, string> = {
    scheduled: 'bg-blue-100 text-blue-700',
    checked_in: 'bg-emerald-100 text-emerald-700',
    no_show: 'bg-red-100 text-red-700',
    cancelled: 'bg-slate-200 text-slate-600',
};

function displayName(appointment: Appointment): string {
    if (
        appointment.status === 'scheduled' &&
        appointment.priority_mode === 'medium'
    ) {
        return 'Reserved Appointment';
    }
    return appointment.patient?.name ?? 'Unknown Patient';
}

export default function AppointmentsCalendar() {
    const { appointments, month } = usePage<AppointmentsCalendarProps>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: home().url },
        { title: 'Appointments', href: appointmentsCalendar().url },
    ];

    const [selectedDate, setSelectedDate] = useState<string | null>(null);

    const byDate = useMemo(() => {
        const map: Record<string, Appointment[]> = {};
        for (const appointment of appointments) {
            const date = appointment.scheduled_at.slice(0, 10);
            if (!map[date]) {
                map[date] = [];
            }
            map[date].push(appointment);
        }
        return map;
    }, [appointments]);

    const [year, monthNum] = month.split('-').map(Number);
    const firstOfMonth = new Date(year, monthNum - 1, 1);
    const daysInMonth = new Date(year, monthNum, 0).getDate();
    const startWeekday = firstOfMonth.getDay();

    const cells: (string | null)[] = [
        ...Array(startWeekday).fill(null),
        ...Array.from({ length: daysInMonth }, (_, i) => {
            const day = String(i + 1).padStart(2, '0');
            const m = String(monthNum).padStart(2, '0');
            return `${year}-${m}-${day}`;
        }),
    ];

    const goToMonth = (delta: number) => {
        const next = new Date(year, monthNum - 1 + delta, 1);
        const nextMonth = `${next.getFullYear()}-${String(next.getMonth() + 1).padStart(2, '0')}`;
        router.get(
            appointmentsCalendar().url,
            { month: nextMonth },
            { preserveState: true },
        );
    };

    const cancelAppointment = (appointment: Appointment) => {
        router.post(
            appointmentCancel(appointment.id).url,
            {},
            {
                preserveScroll: true,
                onSuccess: () => toast.success('Appointment cancelled.'),
                onError: () => toast.error('Could not cancel appointment.'),
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Appointments" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-4 text-gray-800 dark:bg-neutral-950 dark:text-white">
                    <div className="flex items-center justify-between">
                        <h3 className="text-2xl font-bold">
                            {firstOfMonth.toLocaleString('default', {
                                month: 'long',
                                year: 'numeric',
                            })}
                        </h3>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                onClick={() => goToMonth(-1)}
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                onClick={() => goToMonth(1)}
                            >
                                Next
                            </Button>
                        </div>
                    </div>

                    <div className="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-slate-500">
                        {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(
                            (d) => (
                                <div key={d}>{d}</div>
                            ),
                        )}
                    </div>

                    <div className="grid grid-cols-7 gap-1">
                        {cells.map((date, idx) => (
                            <div
                                key={idx}
                                className={clsx(
                                    'min-h-24 rounded-lg border p-1 text-xs dark:border-neutral-800',
                                    !date &&
                                        'border-transparent bg-transparent',
                                )}
                            >
                                {date && (
                                    <>
                                        <div className="mb-1 font-semibold text-slate-500">
                                            {Number(date.slice(8, 10))}
                                        </div>
                                        <div className="flex flex-col gap-1">
                                            {(byDate[date] ?? [])
                                                .slice(0, 3)
                                                .map((appointment) => (
                                                    <button
                                                        key={appointment.id}
                                                        onClick={() =>
                                                            setSelectedDate(
                                                                date,
                                                            )
                                                        }
                                                        className={clsx(
                                                            'truncate rounded px-1 py-0.5 text-left',
                                                            priorityBadge[
                                                                appointment
                                                                    .priority_mode
                                                            ],
                                                        )}
                                                    >
                                                        {displayName(
                                                            appointment,
                                                        )}
                                                    </button>
                                                ))}
                                            {(byDate[date]?.length ?? 0) >
                                                3 && (
                                                <button
                                                    onClick={() =>
                                                        setSelectedDate(date)
                                                    }
                                                    className="text-left text-slate-400 hover:underline"
                                                >
                                                    +
                                                    {(byDate[date]?.length ??
                                                        0) - 3}{' '}
                                                    more
                                                </button>
                                            )}
                                        </div>
                                    </>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {selectedDate && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                    onClick={() => setSelectedDate(null)}
                >
                    <div
                        className="max-h-[80vh] w-full max-w-lg overflow-y-auto rounded-xl bg-white p-6 dark:bg-neutral-900"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="mb-4 flex items-center justify-between">
                            <h4 className="text-lg font-semibold">
                                Appointments on {selectedDate}
                            </h4>
                            <button
                                onClick={() => setSelectedDate(null)}
                                className="text-slate-400 hover:text-slate-600"
                            >
                                ✕
                            </button>
                        </div>
                        <div className="flex flex-col gap-3">
                            {(byDate[selectedDate] ?? []).map((appointment) => (
                                <div
                                    key={appointment.id}
                                    className="rounded-lg border p-3 dark:border-neutral-800"
                                >
                                    <div className="mb-1 flex items-center gap-2">
                                        <span
                                            className={clsx(
                                                'rounded px-2 py-0.5 text-xs font-semibold',
                                                priorityBadge[
                                                    appointment.priority_mode
                                                ],
                                            )}
                                        >
                                            {appointment.priority_mode}
                                        </span>
                                        <span
                                            className={clsx(
                                                'rounded px-2 py-0.5 text-xs font-semibold',
                                                statusBadge[appointment.status],
                                            )}
                                        >
                                            {appointment.status}
                                        </span>
                                    </div>
                                    <div className="font-semibold">
                                        {displayName(appointment)}
                                    </div>
                                    <div className="text-sm text-slate-500">
                                        {appointment.service?.name}
                                        {appointment.doctor?.name
                                            ? ` — ${appointment.doctor.name}`
                                            : ''}
                                    </div>
                                    <div className="text-xs text-slate-400">
                                        {appointment.appointment_number}
                                    </div>
                                    {appointment.status === 'scheduled' && (
                                        <Button
                                            variant="outline"
                                            className="mt-2 text-red-600"
                                            onClick={() =>
                                                cancelAppointment(appointment)
                                            }
                                        >
                                            Cancel Appointment
                                        </Button>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
