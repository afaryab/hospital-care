import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';

interface RegisterProps {
    isFirstSignup?: boolean;
}

export default function Register({ isFirstSignup = false }: RegisterProps) {
    return (
        <AuthLayout
            title={
                isFirstSignup ? 'Create the first account' : 'Create an account'
            }
            description={
                isFirstSignup
                    ? 'This first sign up becomes the administrator account.'
                    : 'Enter your details below to create your account'
            }
        >
            <Head title="Register" />
            <Form
                {...store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            {isFirstSignup && (
                                <p className="text-sm text-muted-foreground">
                                    This account will be assigned administrator
                                    access.
                                </p>
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="name"
                                    name="name"
                                    placeholder="Full name"
                                />
                                <InputError
                                    message={errors.name}
                                    className="mt-2"
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">
                                    Email address (optional)
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    tabIndex={2}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="email@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="mobile">Mobile number</Label>
                                <Input
                                    id="mobile"
                                    type="tel"
                                    required
                                    tabIndex={3}
                                    autoComplete="tel"
                                    name="mobile"
                                    placeholder="+92-3xx-xxxxxxx"
                                />
                                <InputError message={errors.mobile} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="gender">Gender</Label>
                                <select
                                    id="gender"
                                    name="gender"
                                    required
                                    tabIndex={4}
                                    className="rounded border px-3 py-2"
                                >
                                    <option value="">Select gender</option>
                                    <option value="m">Male</option>
                                    <option value="f">Female</option>
                                    <option value="t">Other</option>
                                </select>
                                <InputError message={errors.gender} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="dob">Date of birth</Label>
                                <Input
                                    id="dob"
                                    type="date"
                                    required
                                    tabIndex={5}
                                    name="dob"
                                />
                                <InputError message={errors.dob} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    tabIndex={6}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Password"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    Confirm password
                                </Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    tabIndex={7}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Confirm password"
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                tabIndex={8}
                                data-test="register-user-button"
                            >
                                {processing && <Spinner />}
                                Create account
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink href={login()} tabIndex={9}>
                                Log in
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
