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
import PublicBookingShell from '@/elements/appointments/public-booking-shell';
import { store } from '@/routes/public-appointments';
import { Head, useForm, usePage } from '@inertiajs/react';
import { type FormEvent } from 'react';

type ServiceGroup = {
    department: string;
    services: { id: number; name: string }[];
};

type BookAppointmentProps = {
    serviceGroups: ServiceGroup[];
    preferredTimes: Record<string, string>;
};

const today = () => new Date().toISOString().slice(0, 10);

export default function BookAppointment() {
    const { serviceGroups, preferredTimes } =
        usePage<BookAppointmentProps>().props;

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        contact: '',
        gender: '',
        age_years: '',
        service_id: '',
        preferred_date: today(),
        preferred_time: 'morning',
        notes: '',
        website: '',
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post(store().url, { preserveScroll: true });
    };

    return (
        <PublicBookingShell
            title="Request an appointment"
            description="Tell us when you would like to visit. Our reception team will call you to confirm the time."
        >
            <Head title="Book an appointment" />
            <form onSubmit={submit} className="grid gap-5">
                <div className="grid gap-2">
                    <Label htmlFor="name">Patient name</Label>
                    <Input
                        id="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        autoComplete="name"
                        required
                    />
                    <InputError message={errors.name} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="contact">Phone number</Label>
                    <Input
                        id="contact"
                        type="tel"
                        inputMode="tel"
                        placeholder="03xx xxxxxxx"
                        value={data.contact}
                        onChange={(e) => setData('contact', e.target.value)}
                        autoComplete="tel"
                        required
                    />
                    <InputError message={errors.contact} />
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div className="grid gap-2">
                        <Label htmlFor="gender">Gender</Label>
                        <Select
                            value={data.gender}
                            onValueChange={(value) => setData('gender', value)}
                        >
                            <SelectTrigger id="gender">
                                <SelectValue placeholder="Select" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="m">Male</SelectItem>
                                <SelectItem value="f">Female</SelectItem>
                                <SelectItem value="t">Transgender</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError message={errors.gender} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="age_years">Age (years)</Label>
                        <Input
                            id="age_years"
                            type="number"
                            min={0}
                            max={120}
                            value={data.age_years}
                            onChange={(e) =>
                                setData('age_years', e.target.value)
                            }
                        />
                        <InputError message={errors.age_years} />
                    </div>
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="service_id">Service (optional)</Label>
                    <Select
                        value={data.service_id}
                        onValueChange={(value) => setData('service_id', value)}
                    >
                        <SelectTrigger id="service_id">
                            <SelectValue placeholder="Not sure — reception will advise" />
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

                <div className="grid grid-cols-2 gap-4">
                    <div className="grid gap-2">
                        <Label htmlFor="preferred_date">Preferred date</Label>
                        <Input
                            id="preferred_date"
                            type="date"
                            min={today()}
                            value={data.preferred_date}
                            onChange={(e) =>
                                setData('preferred_date', e.target.value)
                            }
                            required
                        />
                        <InputError message={errors.preferred_date} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="preferred_time">Preferred time</Label>
                        <Select
                            value={data.preferred_time}
                            onValueChange={(value) =>
                                setData('preferred_time', value)
                            }
                        >
                            <SelectTrigger id="preferred_time">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {Object.entries(preferredTimes).map(
                                    ([value, label]) => (
                                        <SelectItem key={value} value={value}>
                                            {label}
                                        </SelectItem>
                                    ),
                                )}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.preferred_time} />
                    </div>
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="notes">Reason for visit (optional)</Label>
                    <textarea
                        id="notes"
                        rows={3}
                        maxLength={500}
                        value={data.notes}
                        onChange={(e) => setData('notes', e.target.value)}
                        className="rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50"
                    />
                    <InputError message={errors.notes} />
                </div>

                <div className="hidden" aria-hidden="true">
                    <label htmlFor="website">Website</label>
                    <input
                        id="website"
                        tabIndex={-1}
                        autoComplete="off"
                        value={data.website}
                        onChange={(e) => setData('website', e.target.value)}
                    />
                </div>
                <InputError message={errors.website} />

                <Button type="submit" disabled={processing} className="w-full">
                    {processing ? 'Sending…' : 'Request appointment'}
                </Button>
                <p className="text-center text-xs text-muted-foreground">
                    Your details are used only to arrange this appointment.
                </p>
            </form>
        </PublicBookingShell>
    );
}
