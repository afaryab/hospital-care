// Components
import { counterStore, home } from '@/routes';
import { Form, Head, usePage } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
} from '@/components/ui/select';
import AuthLayout from '@/layouts/auth-layout';
import { SelectValue } from '@radix-ui/react-select';
import clsx from 'clsx';
import { useState } from 'react';

export default function CounterOpen({ status }: { status?: string }) {
    const { recptions } = usePage().props as any;

    const [openingBalance, setOpeningBalance] = useState<number | string>(0);
    const [receptionId, setReceptionId] = useState<number | null>(null);

    return (
        <AuthLayout
            title="Open Counter"
            description="Enter the details to open a new counter"
        >
            <Head title="Open Counter" />

            {status && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <div className="space-y-6">
                <Form {...counterStore.form()}>
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="opening_balance">
                                    Opening Balance
                                </Label>
                                <Input
                                    id="opening_balance"
                                    type="number"
                                    name="opening_balance"
                                    autoComplete="off"
                                    autoFocus
                                    placeholder="0.00"
                                    step=".01"
                                    value={openingBalance}
                                    onChange={(e) =>
                                        setOpeningBalance(e.target.value)
                                    }
                                />
                                <InputError message={errors.opening_balance} />
                            </div>

                            <div className="mt-6 grid gap-2">
                                <Label htmlFor="reception_id">Reception</Label>
                                <Select
                                    value={receptionId?.toString() || ''}
                                    name="reception_id"
                                    onValueChange={(value) =>
                                        setReceptionId(
                                            value ? parseInt(value) : null,
                                        )
                                    }
                                >
                                    <SelectTrigger id="reception">
                                        <SelectValue placeholder="Select Reception" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {recptions.map(
                                            (reception: {
                                                id: number;
                                                name: string;
                                            }) => (
                                                <SelectItem
                                                    key={reception.id}
                                                    value={reception.id.toString()}
                                                >
                                                    {reception.name}
                                                </SelectItem>
                                            ),
                                        )}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.reception_id} />
                            </div>

                            <div className="my-6 flex items-center justify-start">
                                <Button
                                    className={clsx(
                                        'w-full',
                                        processing
                                            ? 'cursor-not-allowed opacity-50'
                                            : '',
                                    )}
                                    disabled={processing}
                                    data-test="email-password-reset-link-button"
                                >
                                    {processing && (
                                        <LoaderCircle className="h-4 w-4 animate-spin" />
                                    )}
                                    Open Counter
                                </Button>
                            </div>
                        </>
                    )}
                </Form>

                <div className="space-x-1 text-center text-sm text-muted-foreground">
                    <span>Or, return to</span>
                    <TextLink href={home()}>home page</TextLink>
                </div>
            </div>
        </AuthLayout>
    );
}
