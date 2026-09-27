import { Button } from '@/components/ui/button';
import PublicBookingShell from '@/elements/appointments/public-booking-shell';
import { create } from '@/routes/public-appointments';
import { Head, Link, usePage } from '@inertiajs/react';
import { CheckCircle2 } from 'lucide-react';

type AppointmentRequestedProps = {
    reference: string;
    status: string;
    preferredDate: string;
    preferredTime: string;
};

export default function AppointmentRequested() {
    const { reference, status, preferredDate, preferredTime } =
        usePage<AppointmentRequestedProps>().props;

    return (
        <PublicBookingShell
            title="Request received"
            description="Our reception team will call you to confirm your appointment."
        >
            <Head title="Appointment requested" />
            <div className="flex flex-col items-center gap-4 text-center">
                <CheckCircle2 className="h-12 w-12 text-emerald-500" />
                <dl className="grid w-full grid-cols-2 gap-x-4 gap-y-2 rounded-lg border p-4 text-left text-sm">
                    <dt className="text-muted-foreground">Status</dt>
                    <dd className="font-medium">{status}</dd>
                    <dt className="text-muted-foreground">Preferred date</dt>
                    <dd className="font-medium">{preferredDate}</dd>
                    <dt className="text-muted-foreground">Preferred time</dt>
                    <dd className="font-medium">{preferredTime}</dd>
                    <dt className="text-muted-foreground">Reference</dt>
                    <dd className="font-mono text-xs break-all">{reference}</dd>
                </dl>
                <p className="text-xs text-muted-foreground">
                    Keep this reference if you need to contact us about your
                    request.
                </p>
                <Button asChild variant="outline">
                    <Link href={create().url}>Request another appointment</Link>
                </Button>
            </div>
        </PublicBookingShell>
    );
}
