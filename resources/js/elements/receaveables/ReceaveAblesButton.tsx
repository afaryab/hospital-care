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
import { Form, Head } from '@inertiajs/react';
import { receaveablesPayment } from '@/routes';
import { Spinner } from '@/components/ui/spinner';

interface ReceaveAblesButtonProps {
	receaveable: Receaveable
	onConfirm?: (amountCollected: number, note?: string) => void;
}

export default function ReceaveAblesButton({
	receaveable,
	onConfirm,
}: ReceaveAblesButtonProps) {
	const [amountToCollect] = useState<string>(
		String(receaveable.amount ?? ''),
	);
	const [amountToReceave, setAmountToReceave] = useState<string>(
		String(receaveable.amount ?? ''),
	);
	const [note, setNote] = useState('');

	console.log('Receaveable in button:', receaveable);

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

				<Form
					{...receaveablesPayment.form()} 
					className="space-y-4"
					>
						{({ processing, errors }) => (
							<>
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
								</div>

								<div className="grid gap-2">
									<Label htmlFor="receaveable_note">
										Note (optional)
									</Label>
									<textarea
										id="receaveable_note"
										name="receaveable_note"
										className="border-input placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex min-h-[80px] w-full min-w-0 rounded-md border bg-white px-3 py-2 text-sm text-neutral-700 shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-neutral-950 dark:text-white"
										value={note}
										onChange={(event) => setNote(event.target.value)}
									/>
								</div>

								<DialogFooter className="mt-4">
									<DialogClose asChild>
										<Button variant="secondary">Cancel</Button>
									</DialogClose>
									<Button type="submit" disabled={processing} >
										{processing && <Spinner />}
										Confirm collection
									</Button>
								</DialogFooter>
							</>)}
				</Form>
			</DialogContent>
		</Dialog>
	);
}

