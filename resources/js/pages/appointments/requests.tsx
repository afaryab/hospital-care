import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import TablePagination from '@/components/ui/table-pagination';
import AppLayout from '@/layouts/app-layout';
import { appointmentRequests, appointmentsCalendar, home } from '@/routes';
import { confirm, reject } from '@/routes/appointment-requests';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import { useState } from 'react';

type MatchingPatient = {
    id: number;
    name: string;
    ps_number: string;
    gender: string;
    age: number | null;
};

type AppointmentRequestRow = {
    id: number;
    name: string;
    contact: string;
    gender: string | null;
    age_years: number | null;
    service_id: number | null;
    preferred_date: string;
    preferred_time: string;
    preferred_time_label: string;
    notes: string | null;
    status: 'pending' | 'confirmed' | 'rejected';
    rejection_reason: string | null;
    created_at: string;
    service: { id: number; name: string } | null;
    patient: { id: number; name: string; ps_number: string } | null;
    appointment: { appointment_number: string; scheduled_at: string } | null;
    handler: { name: string } | null;
    matching_patients: MatchingPatient[];
};

type ServiceGroup = {
    department: string;
    services: { id: number; name: string }[];
};

type RequestsPageProps = {
    requests: {
        data: AppointmentRequestRow[];
        current_page: number;
        last_page: number;
    };
    status: string;
    serviceGroups: ServiceGroup[];
    pendingCount: number;
};

const SLOT_START: Record<string, string> = {
    morning: '09:00',
    afternoon: '12:00',
    evening: '16:00',
};

const GENDER: Record<string, string> = {
    m: 'Male',
    f: 'Female',
    t: 'Transgender',
};

function PendingRequestCard({
    request,
    serviceGroups,
}: {
    request: AppointmentRequestRow;
    serviceGroups: ServiceGroup[];
}) {
    const [patientChoice, setPatientChoice] = useState<string>(
        request.matching_patients[0]
            ? String(request.matching_patients[0].id)
            : 'new',
    );
    const [serviceId, setServiceId] = useState<string>(
        request.service_id ? String(request.service_id) : '',
    );
    const [scheduledAt, setScheduledAt] = useState(
        `${request.preferred_date}T${SLOT_START[request.preferred_time] ?? '09:00'}`,
    );
    const [rejecting, setRejecting] = useState(false);
    const [reason, setReason] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [busy, setBusy] = useState(false);

    const submitConfirm = () => {
        setBusy(true);
        router.post(
            confirm(request.id).url,
            {
                patient_id:
                    patientChoice === 'new' ? null : Number(patientChoice),
                service_id: serviceId ? Number(serviceId) : null,
                scheduled_at: scheduledAt.replace('T', ' '),
            },
            {
                preserveScroll: true,
                onError: setErrors,
                onFinish: () => setBusy(false),
            },
        );
    };

    const submitReject = () => {
        setBusy(true);
        router.post(
            reject(request.id).url,
            { rejection_reason: reason },
            {
                preserveScroll: true,
                onError: setErrors,
                onFinish: () => setBusy(false),
            },
        );
    };

    return (
        <div className="grid gap-4 rounded-xl border bg-white p-4 shadow-sm lg:grid-cols-[1fr_1.4fr] dark:bg-neutral-950">
            <div className="space-y-1 text-sm">
                <div className="text-base font-semibold">{request.name}</div>
                <div>{request.contact}</div>
                <div className="text-muted-foreground">
                    {[
                        request.gender ? GENDER[request.gender] : null,
                        request.age_years !== null
                            ? `${request.age_years} yrs`
                            : null,
                    ]
                        .filter(Boolean)
                        .join(' · ') || 'No age or gender given'}
                </div>
                <div>
                    Wants:{' '}
                    <strong>{request.service?.name ?? 'Not specified'}</strong>
                </div>
                <div>
                    Preferred: <strong>{request.preferred_date}</strong>,{' '}
                    {request.preferred_time_label}
                </div>
                {request.notes && (
                    <div className="rounded-md bg-neutral-50 p-2 text-muted-foreground dark:bg-neutral-900">
                        “{request.notes}”
                    </div>
                )}
            </div>

            <div className="grid gap-3">
                <div className="grid gap-2">
                    <Label>Patient</Label>
                    <Select
                        value={patientChoice}
                        onValueChange={setPatientChoice}
                    >
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {request.matching_patients.map((patient) => (
                                <SelectItem
                                    key={patient.id}
                                    value={String(patient.id)}
                                >
                                    {patient.ps_number} — {patient.name}
                                    {patient.age !== null
                                        ? ` (${patient.age} yrs)`
                                        : ''}
                                </SelectItem>
                            ))}
                            <SelectItem value="new">
                                Register as a new patient
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    {request.matching_patients.length > 0 && (
                        <p className="text-xs text-muted-foreground">
                            {request.matching_patients.length} existing
                            patient(s) share this phone number.
                        </p>
                    )}
                    <InputError message={errors.patient_id} />
                </div>

                <div className="grid gap-3 sm:grid-cols-2">
                    <div className="grid gap-2">
                        <Label>Service</Label>
                        <Select value={serviceId} onValueChange={setServiceId}>
                            <SelectTrigger>
                                <SelectValue placeholder="Choose service" />
                            </SelectTrigger>
                            <SelectContent>
                                {serviceGroups.map((group) => (
                                    <SelectGroup key={group.department}>
                                        <SelectLabel>
                                            {group.department}
                                        </SelectLabel>
                                        {group.services.map((service) => (
                                            <SelectItem
                                                key={service.id}
                                                value={String(service.id)}
                                            >
                                                {service.name}
                                            </SelectItem>
                                        ))}
                                    </SelectGroup>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.service_id} />
                    </div>
                    <div className="grid gap-2">
                        <Label>Appointment time</Label>
                        <Input
                            type="datetime-local"
                            value={scheduledAt}
                            onChange={(e) => setScheduledAt(e.target.value)}
                        />
                        <InputError message={errors.scheduled_at} />
                    </div>
                </div>

                <InputError message={errors.request} />

                {rejecting ? (
                    <div className="flex flex-wrap items-end gap-2">
                        <div className="grid flex-1 gap-2">
                            <Label>Reason for rejecting</Label>
                            <Input
                                value={reason}
                                onChange={(e) => setReason(e.target.value)}
                                placeholder="e.g. Could not reach patient"
                            />
                            <InputError message={errors.rejection_reason} />
                        </div>
                        <Button
                            variant="destructive"
                            disabled={busy}
                            onClick={submitReject}
                        >
                            Reject request
                        </Button>
                        <Button
                            variant="outline"
                            onClick={() => setRejecting(false)}
                        >
                            Back
                        </Button>
                    </div>
                ) : (
                    <div className="flex flex-wrap gap-2">
                        <Button
                            disabled={busy || !serviceId}
                            onClick={submitConfirm}
                        >
                            Confirm &amp; book
                        </Button>
                        <Button
                            variant="outline"
                            onClick={() => setRejecting(true)}
                        >
                            Reject
                        </Button>
                    </div>
                )}
            </div>
        </div>
    );
}

export default function AppointmentRequests() {
    const { requests, status, serviceGroups, pendingCount } =
        usePage<RequestsPageProps>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: home().url },
        { title: 'Appointments', href: appointmentsCalendar().url },
        { title: 'Requests', href: appointmentRequests().url },
    ];

    const tabs = [
        { value: 'pending', label: `Pending (${pendingCount})` },
        { value: 'confirmed', label: 'Confirmed' },
        { value: 'rejected', label: 'Rejected' },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Appointment requests" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white p-4 dark:bg-neutral-950">
                    <div>
                        <h1 className="text-lg font-semibold">
                            Appointment requests
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Requests submitted from the public booking page.
                            Call the patient, then confirm to book the
                            appointment.
                        </p>
                    </div>
                    <div className="flex gap-1">
                        {tabs.map((tab) => (
                            <Link
                                key={tab.value}
                                href={
                                    appointmentRequests({
                                        query: { status: tab.value },
                                    }).url
                                }
                                className={clsx(
                                    'rounded-md px-3 py-1.5 text-sm',
                                    status === tab.value
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-neutral-100 dark:bg-neutral-800',
                                )}
                            >
                                {tab.label}
                            </Link>
                        ))}
                    </div>
                </div>

                <div className="grid gap-3">
                    {requests.data.length === 0 && (
                        <div className="rounded-xl bg-white p-10 text-center text-sm text-muted-foreground dark:bg-neutral-950">
                            No {status} requests.
                        </div>
                    )}
                    {requests.data.map((request) =>
                        request.status === 'pending' ? (
                            <PendingRequestCard
                                key={request.id}
                                request={request}
                                serviceGroups={serviceGroups}
                            />
                        ) : (
                            <div
                                key={request.id}
                                className="flex flex-wrap items-center justify-between gap-2 rounded-xl border bg-white p-4 text-sm dark:bg-neutral-950"
                            >
                                <div>
                                    <div className="font-semibold">
                                        {request.name}
                                    </div>
                                    <div className="text-muted-foreground">
                                        {request.contact} · requested{' '}
                                        {request.preferred_date}
                                    </div>
                                </div>
                                <div className="text-right">
                                    {request.status === 'confirmed' ? (
                                        <div>
                                            {
                                                request.appointment
                                                    ?.appointment_number
                                            }{' '}
                                            · {request.patient?.ps_number}
                                        </div>
                                    ) : (
                                        <div>
                                            Rejected: {request.rejection_reason}
                                        </div>
                                    )}
                                    <div className="text-muted-foreground">
                                        by {request.handler?.name ?? '—'}
                                    </div>
                                </div>
                            </div>
                        ),
                    )}
                </div>

                {requests.last_page > 1 && (
                    <TablePagination
                        currentPage={requests.current_page}
                        lastPage={requests.last_page}
                        makeHref={(page) => `?status=${status}&page=${page}`}
                    />
                )}
            </div>
        </AppLayout>
    );
}
