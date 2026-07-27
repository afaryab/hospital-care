import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { clsx } from 'clsx';
import { useState } from 'react';

function nowLocal(): string {
    const d = new Date();
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

type Disposition = 'discharged' | 'referred';

interface DischargeDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    submitting?: boolean;
    onConfirm: (payload: {
        outcome: Disposition;
        outcome_at: string;
        referral_to?: string;
        outcome_notes?: string;
    }) => void;
}

export function DischargeDialog({
    open,
    onOpenChange,
    submitting = false,
    onConfirm,
}: DischargeDialogProps) {
    const [disposition, setDisposition] = useState<Disposition>('discharged');
    const [outcomeAt, setOutcomeAt] = useState(nowLocal);
    const [referralTo, setReferralTo] = useState('');
    const [notes, setNotes] = useState('');

    const canConfirm =
        disposition !== 'referred' || referralTo.trim().length > 0;

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Discharge Patient</DialogTitle>
                    <DialogDescription>
                        Record how this Emergency visit ended before finalizing.
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4">
                    <div className="grid grid-cols-2 gap-2">
                        {(
                            [
                                ['discharged', 'Discharged Home'],
                                ['referred', 'Referred to Other Hospital'],
                            ] as const
                        ).map(([value, label]) => (
                            <button
                                key={value}
                                type="button"
                                onClick={() => setDisposition(value)}
                                className={clsx(
                                    'rounded-xl border px-3 py-2.5 text-sm font-semibold transition-colors',
                                    disposition === value
                                        ? 'border-slate-800 bg-slate-800 text-white'
                                        : 'border-slate-200 text-slate-600 hover:bg-slate-50',
                                )}
                            >
                                {label}
                            </button>
                        ))}
                    </div>

                    <div>
                        <Label htmlFor="discharge-time" required>
                            Time of Discharge
                        </Label>
                        <input
                            id="discharge-time"
                            type="datetime-local"
                            value={outcomeAt}
                            onChange={(e) => setOutcomeAt(e.target.value)}
                            className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            required
                        />
                    </div>

                    {disposition === 'referred' && (
                        <div>
                            <Label htmlFor="referred-to" required>
                                Referred To
                            </Label>
                            <input
                                id="referred-to"
                                value={referralTo}
                                onChange={(e) => setReferralTo(e.target.value)}
                                placeholder="Hospital / facility name"
                                className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                    )}

                    <div>
                        <Label htmlFor="discharge-notes">
                            Notes (optional)
                        </Label>
                        <textarea
                            id="discharge-notes"
                            value={notes}
                            onChange={(e) => setNotes(e.target.value)}
                            rows={2}
                            className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Button
                        disabled={!canConfirm || submitting}
                        onClick={() =>
                            onConfirm({
                                outcome: disposition,
                                outcome_at: outcomeAt,
                                referral_to:
                                    disposition === 'referred'
                                        ? referralTo
                                        : undefined,
                                outcome_notes: notes || undefined,
                            })
                        }
                    >
                        {submitting ? 'Discharging…' : 'Confirm Discharge'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

interface DeathConfirmDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    submitting?: boolean;
    onConfirm: (payload: {
        outcome_at: string;
        outcome_notes?: string;
    }) => void;
    onCancel: () => void;
}

export function DeathConfirmDialog({
    open,
    onOpenChange,
    submitting = false,
    onConfirm,
    onCancel,
}: DeathConfirmDialogProps) {
    const [timeOfDeath, setTimeOfDeath] = useState(nowLocal);
    const [notes, setNotes] = useState('');

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirm Code Black</DialogTitle>
                    <DialogDescription>
                        Setting triage to Code Black declares this patient dead
                        and will finalize the record. Confirm the time of death
                        to proceed.
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4">
                    <div>
                        <Label htmlFor="time-of-death" required>
                            Do you declare this patient dead at
                        </Label>
                        <input
                            id="time-of-death"
                            type="datetime-local"
                            value={timeOfDeath}
                            onChange={(e) => setTimeOfDeath(e.target.value)}
                            className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            required
                        />
                    </div>
                    <div>
                        <Label htmlFor="death-notes">
                            Cause / Notes (optional)
                        </Label>
                        <textarea
                            id="death-notes"
                            value={notes}
                            onChange={(e) => setNotes(e.target.value)}
                            rows={2}
                            className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="secondary" onClick={onCancel}>
                        Cancel
                    </Button>
                    <Button
                        variant="destructive"
                        disabled={submitting}
                        onClick={() =>
                            onConfirm({
                                outcome_at: timeOfDeath,
                                outcome_notes: notes || undefined,
                            })
                        }
                    >
                        {submitting ? 'Confirming…' : 'Confirm Death'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
