import { useState } from 'react';

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
import { Receaveable } from '@/types';

interface ReceaveAblesButtonProps {
	receaveable: Receaveable
	onConfirm?: (amountCollected: number, note?: string) => void;
}

export default function ReceaveAblesButton({
	receaveable,
	onConfirm,
}: ReceaveAblesButtonProps) {
	const [amountToCollect, setAmountToCollect] = useState<string>(
		String(receaveable.amount ?? ''),
	);
	const [note, setNote] = useState('');

	const handleConfirm = () => {
		const numericAmount = Number(amountToCollect);

		if (Number.isNaN(numericAmount)) {
			return;
		}

		onConfirm?.(numericAmount, note.trim() || undefined);
	};

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

				<div className="space-y-4">
					<div className="text-sm">
						<div className="font-medium text-white dark:text-white">
							{receaveable.patient.name}
						</div>
						<div className="text-xs text-muted-foreground">
							Total receivable: {receaveable.amount}
						</div>
					</div>

                    <div className="grid gap-2">
						<Label htmlFor="patient" required>
							Patient
						</Label>
						<Input
							id="patient"
							type="text"
							value={`${receaveable.patient.year}-${receaveable.patient.month}-${receaveable.patient.number} - ${receaveable.patient.name}`}
                            disabled
						/>
					</div>

                    <div className="grid gap-2">
						<Label htmlFor="pending-amount" required>
							Pending Amount
						</Label>
						<Input
							id="pending-amount"
							type="number"
							min={0}
							value={amountToCollect}
                            disabled
						/>
					</div>

					<div className="grid gap-2">
						<Label htmlFor="receaveable-amount" required>
							Amount to collect
						</Label>
						<Input
							id="receaveable-amount"
							type="number"
							min={0}
							value={amountToCollect}
							onChange={(event) =>
								setAmountToCollect(event.target.value)
							}
						/>
					</div>

					<div className="grid gap-2">
						<Label htmlFor="receaveable-note">
							Note (optional)
						</Label>
						<textarea
							id="receaveable-note"
							className="border-input placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex min-h-[80px] w-full min-w-0 rounded-md border bg-white px-3 py-2 text-sm text-neutral-700 shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-neutral-950 dark:text-white"
							value={note}
							onChange={(event) => setNote(event.target.value)}
						/>
					</div>
				</div>

				<DialogFooter className="mt-4">
					<DialogClose asChild>
						<Button variant="secondary">Cancel</Button>
					</DialogClose>

					<DialogClose asChild>
						<Button onClick={handleConfirm}>
							Confirm collection
						</Button>
					</DialogClose>
				</DialogFooter>
			</DialogContent>
		</Dialog>
	);
}

