import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import BulletsWrapper from '@/elements/bullets-wrapper';
import SelectCounter from '@/elements/counter/select-counter';
import { ExpenseCategoryOption } from '@/elements/expense-category/select-expense-category';
import AppLayout from '@/layouts/app-layout';
import { apiFetch } from '@/lib/api-fetch';
import {
    counterExpense,
    counterExpenseNewVoucher,
    counterExpenseVouchersList,
    counterView,
    home,
    myCounterList,
} from '@/routes';
import { User, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { LoaderCircle, Search } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';

// Types
interface CompletedServiceOrder {
    id: number;
    so_number: string;
    type?: string;
    created_at?: string;
    patient?: { id: number; name?: string };
    doctor?: { id: number; name?: string };
    service?: { id: number; name?: string };
}

export default function NewUserVoucher({
    categories,
    openCounter,
    users,
}: {
    categories: ExpenseCategoryOption[];
    openCounter?: any;
    users: User[];
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Counter',
            href: openCounter
                ? counterView({
                      ctYear: openCounter.year,
                      ctMonth: openCounter.month,
                      ctNumber: openCounter.number,
                  }).url
                : myCounterList().url,
        },
        {
            title: 'Expense Vouchers',
            href: openCounter
                ? counterExpenseVouchersList().url
                : myCounterList().url,
        },
        {
            title: 'New Doctor Voucher',
            href: openCounter
                ? counterExpenseNewVoucher().url
                : myCounterList().url,
        },
    ];

    const bullets = [];

    openCounter &&
        bullets.push({
            title: openCounter && openCounter.ct_number,
            url:
                openCounter &&
                counterView({
                    ctYear: openCounter.year,
                    ctMonth: openCounter.month,
                    ctNumber: openCounter.number,
                }).url,
            active: true,
        });

    openCounter &&
        bullets.push({
            title: 'Expense Payment',
            url: openCounter && counterExpense().url,
            active: true,
        });

    openCounter &&
        bullets.push({
            title: 'New Doctor Voucher',
            url: openCounter && counterExpenseNewVoucher().url,
            active: true,
        });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head
                title={`Counter ${openCounter?.ct_number} - New Doctor Voucher`}
            />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-2 text-gray-800">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-row gap-4 overflow-x-auto rounded-xl bg-white p-2">
                            <NewVoucherTabs
                                openCounter={openCounter}
                                categories={categories}
                                users={users}
                            />
                        </div>
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}

function StepIndicator({
    step,
    currentStep,
    label,
}: {
    step: number;
    currentStep: number;
    label: string;
}) {
    const isActive = currentStep === step;
    const isCompleted = currentStep > step;

    return (
        <div className="flex items-center gap-2">
            <div
                className={clsx(
                    'flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold',
                    isActive && 'bg-blue-600 text-white',
                    isCompleted && 'bg-green-500 text-white',
                    !isActive && !isCompleted && 'bg-gray-200 text-gray-500',
                )}
            >
                {isCompleted ? '✓' : step}
            </div>
            <span
                className={clsx(
                    'text-sm font-medium',
                    isActive
                        ? 'text-blue-600'
                        : isCompleted
                          ? 'text-green-600'
                          : 'text-gray-400',
                )}
            >
                {label}
            </span>
        </div>
    );
}

function NewVoucherTabs({
    openCounter,
    categories,
    users,
}: {
    openCounter?: any;
    categories: ExpenseCategoryOption[];
    users: User[];
}) {
    const [step, setStep] = useState(1);
    const [selectedCategoryId, setSelectedCategoryId] = useState('');
    const [selectedCategory, setSelectedCategory] =
        useState<ExpenseCategoryOption | null>(null);
    const [selectedUserId, setSelectedUserId] = useState<number | null>(null);
    const [selectedUser, setSelectedUser] = useState<User | null>(null);

    const handleCategorySelect = (category: ExpenseCategoryOption | null) => {
        setSelectedCategory(category);
        if (category) {
            setSelectedCategoryId(category.id.toString());
            setStep(2);
        }
    };

    const handleUserSelect = (user: User) => {
        setSelectedUserId(user.id);
        setSelectedUser(user);
        setStep(3);
    };

    return (
        <div className="flex w-full flex-col gap-6">
            {/* Step indicators */}
            <div className="flex items-center gap-6 border-b pb-4">
                <StepIndicator
                    step={1}
                    currentStep={step}
                    label="Select Category"
                />
                <div className="h-px w-8 bg-gray-300" />
                <StepIndicator step={2} currentStep={step} label="Paid To" />
                <div className="h-px w-8 bg-gray-300" />
                <StepIndicator
                    step={3}
                    currentStep={step}
                    label="Voucher Details"
                />
            </div>

            {/* Step content */}
            <div className="flex flex-row gap-4">
                {/* Step 1: Select Category */}
                <div
                    className={clsx(
                        'flex flex-col gap-4 rounded-lg border p-4 transition-all',
                        step === 1
                            ? 'w-full'
                            : 'w-1/4 cursor-pointer hover:bg-gray-50',
                    )}
                    onClick={() => step > 1 && setStep(1)}
                >
                    <h2 className="text-lg font-semibold">
                        1. Select Category
                    </h2>
                    {step === 1 ? (
                        <div className="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                            {categories.map((cat) => (
                                <button
                                    key={cat.id}
                                    type="button"
                                    onClick={() => handleCategorySelect(cat)}
                                    className={clsx(
                                        'flex items-center gap-2 rounded-md border p-3 text-left transition-colors',
                                        selectedCategoryId === cat.id.toString()
                                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                                            : 'hover:bg-gray-100',
                                    )}
                                >
                                    <div
                                        className={clsx(
                                            'flex h-4 w-4 shrink-0 items-center justify-center rounded-full border',
                                            selectedCategoryId ===
                                                cat.id.toString()
                                                ? 'border-blue-500 bg-blue-500'
                                                : 'border-gray-300',
                                        )}
                                    >
                                        {selectedCategoryId ===
                                            cat.id.toString() && (
                                            <div className="h-2 w-2 rounded-full bg-white" />
                                        )}
                                    </div>
                                    {cat.name}
                                </button>
                            ))}
                        </div>
                    ) : (
                        <div className="rounded-md bg-green-50 p-2 text-sm text-green-700">
                            {selectedCategory?.name ?? 'Selected'}
                        </div>
                    )}
                </div>

                {/* Step 2: Paid To */}
                {step >= 2 && (
                    <div
                        className={clsx(
                            'flex flex-col gap-4 rounded-lg border p-4 transition-all',
                            step === 2
                                ? 'flex-1'
                                : 'w-1/4 cursor-pointer hover:bg-gray-50',
                        )}
                        onClick={() => step > 2 && setStep(2)}
                    >
                        <h2 className="text-lg font-semibold">2. Paid To</h2>
                        {step === 2 ? (
                            <div className="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                                {users.map((user) => (
                                    <button
                                        key={user.id}
                                        type="button"
                                        onClick={() => handleUserSelect(user)}
                                        className={clsx(
                                            'flex items-center gap-2 rounded-md border p-3 text-left transition-colors',
                                            selectedUserId === user.id
                                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                : 'hover:bg-gray-100',
                                        )}
                                    >
                                        {user.name}
                                    </button>
                                ))}
                            </div>
                        ) : (
                            <div className="rounded-md bg-green-50 p-2 text-sm text-green-700">
                                {selectedUser?.name ?? 'Selected'}
                            </div>
                        )}
                    </div>
                )}

                {/* Step 3: Voucher Details */}
                {step >= 3 && (
                    <div className="min-w-0 flex-[2] rounded-lg border p-4">
                        <h2 className="mb-4 text-lg font-semibold">
                            3. Voucher Details
                        </h2>
                        <VoucherDetailsForm
                            openCounter={openCounter}
                            category={selectedCategory}
                            userId={selectedUserId}
                        />
                    </div>
                )}
            </div>
        </div>
    );
}

function VoucherDetailsForm({
    openCounter,
    category,
    userId,
}: {
    openCounter?: any;
    category: ExpenseCategoryOption | null;
    userId: number | null;
}) {
    const [amount, setAmount] = useState('');
    const [description, setDescription] = useState('');
    const [selectedServiceOrderIds, setSelectedServiceOrderIds] = useState<
        number[]
    >([]);
    const [serviceOrders, setServiceOrders] = useState<CompletedServiceOrder[]>(
        [],
    );
    const [isLoading, setIsLoading] = useState(false);
    const [search, setSearch] = useState('');
    const [doctorOnly, setDoctorOnly] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [counterId, setCounterId] = useState('');

    const showServiceOrders =
        !!category?.pay_doc && !category?.pay_others && !category?.pay_users;

    const { errors } = usePage().props as any;

    const fetchServiceOrders = useCallback(
        async (
            searchQuery = '',
            filterDoctorOnly = false,
            filterClosingId = '',
        ) => {
            if (!showServiceOrders) return;
            setIsLoading(true);
            try {
                const body: Record<string, unknown> = {
                    search: searchQuery,
                    limit: 50,
                    doctor_only: filterDoctorOnly,
                    payed_to: userId,
                };
                if (filterClosingId)
                    body.closing_id = parseInt(filterClosingId, 10);
                const response = await apiFetch(
                    '/api/service-orders/completed-unpaid',
                    { method: 'POST', body: JSON.stringify(body) },
                );
                if (!response.ok) throw new Error('Failed to fetch');
                const data = await response.json();
                setServiceOrders(data.data ?? []);
            } catch {
                setServiceOrders([]);
            } finally {
                setIsLoading(false);
            }
        },
        [userId, showServiceOrders],
    );

    useEffect(() => {
        fetchServiceOrders('', doctorOnly, counterId);
    }, [fetchServiceOrders, doctorOnly, counterId]);

    useEffect(() => {
        const timer = setTimeout(() => {
            fetchServiceOrders(search, doctorOnly, counterId);
        }, 300);
        return () => clearTimeout(timer);
    }, [search, doctorOnly, counterId, fetchServiceOrders]);

    const toggleServiceOrder = (id: number) => {
        setSelectedServiceOrderIds((prev) =>
            prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id],
        );
    };

    const toggleAll = () => {
        if (selectedServiceOrderIds.length === serviceOrders.length) {
            setSelectedServiceOrderIds([]);
        } else {
            setSelectedServiceOrderIds(serviceOrders.map((so) => so.id));
        }
    };

    const handleSubmit = () => {
        if (!category || !userId || !amount) return;
        if (showServiceOrders && selectedServiceOrderIds.length === 0) return;

        setIsSubmitting(true);
        router.post(
            counterExpenseNewVoucher().url,
            {
                exp_category_id: category.id,
                payed_to: userId,
                service_order_ids: showServiceOrders
                    ? selectedServiceOrderIds
                    : [],
                amount: parseFloat(amount),
                description: description || null,
            },
            {
                onFinish: () => setIsSubmitting(false),
            },
        );
    };

    return (
        <div className="flex flex-col gap-4">
            {/* Errors */}
            {errors?.service_order_ids && (
                <div className="rounded-md bg-red-50 p-3 text-sm text-red-700">
                    {errors.service_order_ids}
                </div>
            )}

            {/* Service Orders Table */}
            {showServiceOrders && (
                <div className="space-y-2">
                    <Label>Service Orders (Completed &amp; Unpaid)</Label>
                    <div className="flex flex-wrap items-center gap-4">
                        <div className="relative min-w-[200px] flex-1">
                            <Search className="absolute top-2.5 left-3 h-4 w-4 text-muted-foreground" />
                            <Input
                                className="pl-9"
                                placeholder="Search by SO number or patient name..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
                        </div>
                        <div className="w-52">
                            <SelectCounter
                                value={counterId}
                                onValueChange={(val) => {
                                    setCounterId(val);
                                    setSelectedServiceOrderIds([]);
                                }}
                                label=""
                                placeholder="All counters"
                            />
                        </div>
                        <label className="flex cursor-pointer items-center gap-2 text-sm whitespace-nowrap">
                            <input
                                type="checkbox"
                                checked={doctorOnly}
                                onChange={(e) => {
                                    setDoctorOnly(e.target.checked);
                                    setSelectedServiceOrderIds([]);
                                }}
                                className="rounded"
                            />
                            Doctor only
                        </label>
                    </div>

                    <div className="max-h-72 overflow-auto rounded-md border">
                        <table className="w-full text-sm">
                            <thead className="sticky top-0 bg-gray-50">
                                <tr className="border-b">
                                    <th className="p-2 text-left">
                                        <input
                                            type="checkbox"
                                            checked={
                                                serviceOrders.length > 0 &&
                                                selectedServiceOrderIds.length ===
                                                    serviceOrders.length
                                            }
                                            onChange={toggleAll}
                                            className="rounded"
                                        />
                                    </th>
                                    <th className="p-2 text-left font-medium">
                                        SO Number
                                    </th>
                                    <th className="p-2 text-left font-medium">
                                        Type
                                    </th>
                                    <th className="p-2 text-left font-medium">
                                        Patient
                                    </th>
                                    <th className="p-2 text-left font-medium">
                                        Doctor
                                    </th>
                                    <th className="p-2 text-left font-medium">
                                        Service
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {isLoading ? (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="p-4 text-center"
                                        >
                                            <LoaderCircle className="mx-auto h-5 w-5 animate-spin text-muted-foreground" />
                                        </td>
                                    </tr>
                                ) : serviceOrders.length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            No completed unpaid service orders
                                            found.
                                        </td>
                                    </tr>
                                ) : (
                                    serviceOrders.map((so) => (
                                        <tr
                                            key={so.id}
                                            className={clsx(
                                                'cursor-pointer border-b last:border-b-0 hover:bg-muted/40',
                                                selectedServiceOrderIds.includes(
                                                    so.id,
                                                ) && 'bg-blue-50',
                                            )}
                                            onClick={() =>
                                                toggleServiceOrder(so.id)
                                            }
                                        >
                                            <td className="p-2">
                                                <input
                                                    type="checkbox"
                                                    checked={selectedServiceOrderIds.includes(
                                                        so.id,
                                                    )}
                                                    onChange={() =>
                                                        toggleServiceOrder(
                                                            so.id,
                                                        )
                                                    }
                                                    className="rounded"
                                                />
                                            </td>
                                            <td className="p-2 font-medium">
                                                {so.so_number}
                                            </td>
                                            <td className="p-2">
                                                {so.type ?? '-'}
                                            </td>
                                            <td className="p-2">
                                                {so.patient?.name ?? '-'}
                                            </td>
                                            <td className="p-2">
                                                {so.doctor?.name ?? '-'}
                                            </td>
                                            <td className="p-2">
                                                {so.service?.name ?? '-'}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                    {selectedServiceOrderIds.length > 0 && (
                        <p className="text-sm text-muted-foreground">
                            {selectedServiceOrderIds.length} service order(s)
                            selected
                        </p>
                    )}
                </div>
            )}

            {/* Amount & Description */}
            <div className="space-y-2">
                <Label>Amount</Label>
                <Input
                    placeholder="Enter amount"
                    type="number"
                    step="0.01"
                    min="0.01"
                    value={amount}
                    onChange={(e) => setAmount(e.target.value)}
                />
                {errors?.amount && (
                    <p className="text-sm text-red-600">{errors.amount}</p>
                )}
            </div>
            <div className="space-y-2">
                <Label>Description</Label>
                <Input
                    placeholder="Enter description (optional)"
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                />
                {errors?.description && (
                    <p className="text-sm text-red-600">{errors.description}</p>
                )}
            </div>
            <Button
                onClick={handleSubmit}
                disabled={
                    isSubmitting ||
                    !amount ||
                    (showServiceOrders && selectedServiceOrderIds.length === 0)
                }
            >
                {isSubmitting ? (
                    <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />
                ) : null}
                Save Voucher
            </Button>
        </div>
    );
}
