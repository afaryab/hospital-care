// Components
import { counter, counterClosePost } from '@/routes';
import { Form, Head, usePage } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

export default function ForgotPassword({ status }: { status?: string }) {
    const { openCounter } = usePage<{ openCounter: any }>().props;

    return (
        <AuthLayout
            title={`Counter ${openCounter?.ct_number} `}
            description="You are about to close the counter."
        >
            <Head title={`Counter ${openCounter?.ct_number} `} />

            {status && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <div className="space-y-6">
                <Form {...counterClosePost.form()}>
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="counter_number">
                                    Counter Statement Number
                                </Label>
                                <Input
                                    id="counter_number"
                                    type="text"
                                    name="counter_number"
                                    disabled={true}
                                    value={openCounter.ct_number}
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="closing_amount">
                                    Closing Amount
                                </Label>
                                <Input
                                    id="closing_amount"
                                    type="number"
                                    name="closing_amount"
                                    disabled={true}
                                    value={openCounter.closing_amount}
                                />
                            </div>
                            <div className="my-6 flex items-center justify-start">
                                <Button
                                    className="w-full"
                                    disabled={processing}
                                    data-test="email-password-reset-link-button"
                                >
                                    {processing && (
                                        <LoaderCircle className="h-4 w-4 animate-spin" />
                                    )}
                                    Close Counter
                                </Button>
                            </div>
                        </>
                    )}
                </Form>

                <div className="space-x-1 text-center text-sm text-muted-foreground">
                    <span>Or, return to</span>
                    <TextLink href={counter()}>Counter</TextLink>
                </div>
            </div>
        </AuthLayout>
    );
}
