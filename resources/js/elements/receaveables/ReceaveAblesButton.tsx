import { useState } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { receaveablesPayment } from '@/routes';
import { Panel, PaymentMethod, Receaveable } from '@/types';
import { Form, usePage } from '@inertiajs/react';

interface ReceaveAblesButtonProps {
    receaveable: Receaveable;
    paymentMethods?: PaymentMethod[];
    panelCompanies?: Panel[];
    onConfirm?: (amountCollected: number, note?: string) => void;
}

export default function ReceaveAblesButton({
    receaveable,
    paymentMethods,
    panelCompanies,
    onConfirm,
}: ReceaveAblesButtonProps) {
    const sharedProps = usePage<{
        paymentMethods?: PaymentMethod[];
        panelCompanies?: Panel[];
    }>().props;

    const methods = paymentMethods ?? sharedProps.paymentMethods ?? [];
    const panels = panelCompanies ?? sharedProps.panelCompanies ?? [];

    const [amountToCollect] = useState<string>(
        String(receaveable.amount ?? ''),
    );
    const [amountToReceave, setAmountToReceave] = useState<string>(
        String(receaveable.amount ?? ''),
    );
    const [paymentMethod, setPaymentMethod] = useState<string>(
        methods[0]?.slug ?? 'CASH',
    );
    const [panelId, setPanelId] = useState<string>('');
    const [note, setNote] = useState('');

    const requiresPanel =
        methods.find((method) => method.slug === paymentMethod)?.payables ===
        'panel';

    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button size="sm" variant="default">
                    Collect
                </Button>
            </DialogTrigger>

            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Collect receivable</DialogTitle>
                    <DialogDescription>
                        Record a payment for this receivable.
                    </DialogDescription>
                </DialogHeader>

                <Form {...receaveablesPayment.form()} className="space-y-4">
                    {({ processing, errors }) => (
                        <>
                            <div className="text-sm">
                                <div className="font-medium text-white dark:text-white">
                                    {receaveable.patient.name}
                                </div>
                                <div className="text-xs text-muted-foreground">
                                    Orignal amount: {receaveable.orignal_amount}
                                </div>
                                <div className="text-xs text-muted-foreground">
                                    Remaining: {receaveable.amount}
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="patient" required>
                                    Patient
                                </Label>
                                <Input
                                    id="patient"
                                    name="patient"
                                    type="text"
                                    value={`${receaveable.patient.year}-${receaveable.patient.month}-${receaveable.patient.number} - ${receaveable.patient.name}`}
                                    disabled
                                />
                                <Input
                                    id="patient_id"
                                    name="patient_id"
                                    type="hidden"
                                    value={`${receaveable.patient.id}`}
                                />
                                <Input
                                    id="receaveable_id"
                                    name="receaveable_id"
                                    type="hidden"
                                    value={`${receaveable.id}`}
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="pending-amount" required>
                                    Pending Amount
                                </Label>
                                <Input
                                    id="pending_amount"
                                    name="pending_amount"
                                    type="number"
                                    min={0}
                                    value={amountToCollect}
                                    disabled
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="amount_to_collect" required>
                                    Amount to collect
                                </Label>
                                <Input
                                    id="amount_to_collect"
                                    name="amount_to_collect"
                                    type="number"
                                    value={amountToReceave}
                                    onChange={(event) =>
                                        setAmountToReceave(event.target.value)
                                    }
                                    required
                                    min={0}
                                />
                                <InputError
                                    message={errors?.amount_to_collect}
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="payment_method">
                                    Payment Method
                                </Label>
                                <Select
                                    value={paymentMethod}
                                    onValueChange={setPaymentMethod}
                                    name="payment_method"
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select payment method" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {methods.map((method) => (
                                            <SelectItem
                                                key={method.id}
                                                value={method.slug}
                                            >
                                                {method.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.payment_method} />
                            </div>

                            {requiresPanel && (
                                <div className="grid gap-2">
                                    <Label htmlFor="panel_id" required>
                                        Panel Company
                                    </Label>
                                    <Select
                                        value={panelId}
                                        onValueChange={setPanelId}
                                        name="panel_id"
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select panel company" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {panels.map((company) => (
                                                <SelectItem
                                                    key={company.id}
                                                    value={String(company.id)}
                                                >
                                                    {company.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.panel_id} />
                                </div>
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="receaveable_note">
                                    Note (optional)
                                </Label>
                                <textarea
                                    id="receaveable_note"
                                    name="receaveable_note"
                                    className="flex min-h-[80px] w-full min-w-0 rounded-md border border-input bg-white px-3 py-2 text-sm text-neutral-700 shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:bg-neutral-950 dark:text-white dark:aria-invalid:ring-destructive/40"
                                    value={note}
                                    onChange={(event) =>
                                        setNote(event.target.value)
                                    }
                                />
                                <InputError message={errors.receaveable_note} />
                            </div>

                            <DialogFooter className="mt-4">
                                <DialogClose asChild>
                                    <Button variant="secondary">Cancel</Button>
                                </DialogClose>
                                <Button type="submit" disabled={processing}>
                                    {processing && <Spinner />}
                                    Confirm collection
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
