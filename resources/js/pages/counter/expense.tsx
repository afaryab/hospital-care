import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import BulletsWrapper from '@/elements/bullets-wrapper';
import SelectExpenseCategory, {
    ExpenseCategoryOption,
} from '@/elements/expense-category/select-expense-category';
import FilterAndSelectExpenseVoucher, {
    ExpenseVoucherSearchItem,
} from '@/elements/expense-voucher/filter-and-select-expense-voucher';
import FilterAndSelectServiceOrder from '@/elements/serviceorder/filter-and-select-serviceorder';
import FilterAndSelectTransaction, {
    TransactionSearchItem,
} from '@/elements/transaction/filter-and-select-transaction';
import AppLayout from '@/layouts/app-layout';
import {
    counterExpense,
    counterExpenseNewVoucher,
    counterView,
    home,
    myCounterList,
    transactionStore,
} from '@/routes';
import { User, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { LoaderCircle } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';

// Types for voucher data
interface ExpenseVoucher {
    id: number;
    vc_number: string;
    amount?: number;
    status?: string;
    description?: string;
    created_at?: string;
}

interface VoucherSearchResults {
    exact: ExpenseVoucher[];
    possible: ExpenseVoucher[];
}

const normalizeCategoryName = (category?: ExpenseCategoryOption | null) =>
    (category?.name ?? '').trim().toLowerCase();

export default function CounterExpense() {
    const { openCounter, users, categories, selected } = usePage<{
        openCounter: any;
        users: User[];
        categories: any[];
        selected?: any;
    }>().props;

    const [payedTo, setPayedTo] = useState(
        selected?.voucher?.payed_to ? selected.voucher.payed_to.toString() : '',
    );
    const [payedToOther, setPayedToOther] = useState('');
    const [selectedVoucher, setSelectedVoucher] =
        useState<ExpenseVoucherSearchItem | null>(
            selected?.voucher
                ? ({
                      id: selected.voucher.id,
                      vc_number: selected.voucher.vc_number,
                      amount: selected.voucher.amount,
                      payed_to: selected.voucher.payed_to,
                      payed_to_name:
                          selected.voucher.payed_to_user?.name ??
                          selected.voucher.payedTo?.name,
                      payedTo:
                          selected.voucher.payed_to_user ??
                          selected.voucher.payedTo,
                  } as ExpenseVoucherSearchItem)
                : null,
        );
    const [selectedVoucherId, setSelectedVoucherId] = useState(
        selected?.voucher ? selected.voucher.id.toString() : '',
    );
    const [processingVoucherPayment, setProcessingVoucherPayment] =
        useState(false);

    // Petty cash state
    const [pettyCashAmount, setPettyCashAmount] = useState(
        selected?.amount ? selected.amount.toString() : '',
    );
    const [pettyCashDescription, setPettyCashDescription] = useState('');
    const [pettyCashCategory, setPettyCashCategory] = useState(
        selected?.category?.id ? selected.category.id.toString() : '',
    );
    const [selectedExpenseCategory, setSelectedExpenseCategory] =
        useState<ExpenseCategoryOption | null>(selected?.category ?? null);
    const [pettyCashPayedTo, setPettyCashPayedTo] = useState(
        selected?.doctor?.id ? selected.doctor.id.toString() : '',
    );
    const [pettyCashOtherName, setPettyCashOtherName] = useState(
        selected?.payed_to_other || '',
    );
    const [pettyCashTransactionId, setPettyCashTransactionId] = useState(
        selected?.transaction?.tr_number || '',
    );
    const [pettyCashFileNumber, setPettyCashFileNumber] = useState('');
    const [processingPettyCashPayment, setProcessingPettyCashPayment] =
        useState(false);
    const [pettyCashErrors, setPettyCashErrors] = useState<string[]>([]);

    // Handle voucher payment
    const handleVoucherPayment = (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedVoucher) {
            toast.error('Please search and select a voucher first');
            return;
        }
        // TODO: Implement voucher payment logic
        console.log('Paying voucher:', selectedVoucher);

        // Prepare form data for submission
        const paymentData = {
            voucher_id: selectedVoucher.id,
            payed_to: payedTo === 'Other' ? null : payedTo,
            payed_to_other: payedTo === 'Other' ? payedToOther : null,
            income_or_expense: 'EXPENSE',
            type: 'VOUCHER-PAY',
        };

        console.log('Payment Data:', paymentData);

        // POST paymentData to server...
        // POST paymentData to server with error handling
        setProcessingVoucherPayment(true);
        router.post(transactionStore().url, paymentData, {
            onSuccess: () => {
                // Reset form on success
                setSelectedVoucher(null);
                setSelectedVoucherId('');
                setPayedTo('');
                setPayedToOther('');
                setProcessingVoucherPayment(false);
            },
            onError: (errors) => {
                // Handle validation errors
                console.error('Validation errors:', errors);

                // Display errors to user
                const errorMessages = Object.values(errors).flat().join('; ');
                toast.error(`Payment failed: ${errorMessages}`);
                setProcessingVoucherPayment(false);
            },
        });
    };

    // Handle petty cash payment
    const handlePettyCashPayment = (e: React.FormEvent) => {
        e.preventDefault();

        if (!pettyCashAmount || parseFloat(pettyCashAmount) <= 0) {
            toast.error('Please enter a valid amount');
            return;
        }
        // TODO: Implement petty cash payment logic
        console.log('Paying petty cash:', pettyCashAmount);

        // Prepare form data for submission
        const paymentData = {
            amount: parseFloat(pettyCashAmount),
            description: pettyCashDescription,
            category_id: pettyCashCategory,
            transaction_id: pettyCashTransactionId,
            file_number: pettyCashFileNumber,
            payed_to: pettyCashPayedTo === 'other' ? null : pettyCashPayedTo,
            payed_to_other:
                pettyCashPayedTo === 'other' ? pettyCashOtherName : null,
            income_or_expense: 'EXPENSE',
            type: 'EXP',
        };

        console.log('Payment Data:', paymentData);
        setProcessingPettyCashPayment(true);
        // POST paymentData to server...
        router.post(transactionStore().url, paymentData, {
            onSuccess: () => {
                // Reset form on success
                setPettyCashAmount('');
                setPettyCashDescription('');
                setPettyCashCategory('');
                setPettyCashPayedTo('');
                setPettyCashOtherName('');
                setPettyCashTransactionId('');
                setProcessingPettyCashPayment(false);
            },
            onError: (errors) => {
                // Handle validation errors
                console.error('Validation errors:', errors);

                // Display errors to user
                const errorMessages = Object.values(errors).flat().join('\n');
                // alert(`Payment failed:\n${errorMessages}`);
                setPettyCashErrors(Object.values(errors).flat());
                setProcessingPettyCashPayment(false);
            },
        });
    };

    const handleRefundTransactionSelect = (
        transaction: TransactionSearchItem | null,
    ) => {
        setPettyCashTransactionId(transaction ? transaction.id.toString() : '');

        if (
            normalizeCategoryName(selectedExpenseCategory) === 'refund' &&
            transaction?.patient?.name
        ) {
            setPettyCashDescription(
                `Refund for patient: ${transaction.patient.name}`,
            );
        }
    };

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
            title: 'Expenses',
            href: openCounter ? counterExpense().url : myCounterList().url,
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

    const handleNewVoucher = () => {
        // redirect to counterExpenseNewVoucher
        router.get(counterExpenseNewVoucher().url);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Counter ${openCounter?.ct_number} Expense`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-2 text-gray-800">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-row gap-4 overflow-x-auto rounded-xl bg-white p-2">
                            {/* Voucher Payment - Left Side */}
                            <div className="flex flex-1 flex-col gap-4 rounded-lg border p-4">
                                <h2 className="text-center text-xl font-semibold">
                                    Voucher Payment
                                </h2>
                                <form
                                    onSubmit={handleVoucherPayment}
                                    className="flex flex-1 flex-col items-center justify-center gap-4"
                                >
                                    <Button
                                        onClick={handleNewVoucher}
                                        type="button"
                                        className="w-full max-w-sm"
                                    >
                                        New Voucher
                                    </Button>
                                    <div className="w-full max-w-sm space-y-2">
                                        <FilterAndSelectExpenseVoucher
                                            value={selectedVoucherId}
                                            onValueChange={setSelectedVoucherId}
                                            onSelect={setSelectedVoucher}
                                            label="Voucher Number"
                                        />
                                    </div>
                                    {selectedVoucher && (
                                        <div className="w-full max-w-sm space-y-2">
                                            <Label htmlFor="payedTo">
                                                Payed To
                                            </Label>
                                            <div className="relative">
                                                <Select
                                                    onValueChange={(value) =>
                                                        setPayedTo(value)
                                                    }
                                                    value={payedTo}
                                                >
                                                    <SelectTrigger className="w-full">
                                                        <SelectValue placeholder="Select user" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {users.map(
                                                            (user: User) => (
                                                                <SelectItem
                                                                    key={
                                                                        user.id
                                                                    }
                                                                    value={user.id.toString()}
                                                                >
                                                                    {user.name}
                                                                </SelectItem>
                                                            ),
                                                        )}
                                                        <SelectItem value="Other">
                                                            Other
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </div>
                                    )}
                                    {payedTo === 'Other' && (
                                        <div className="w-full max-w-sm space-y-2">
                                            <Label htmlFor="payedToOther">
                                                Payed To (Other)
                                            </Label>
                                            <Input
                                                id="payedToOther"
                                                type="text"
                                                value={payedToOther}
                                                onChange={(e) =>
                                                    setPayedToOther(
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                        </div>
                                    )}
                                    <Button
                                        type="submit"
                                        className="w-full max-w-sm"
                                        disabled={
                                            !selectedVoucher ||
                                            processingVoucherPayment
                                        }
                                    >
                                        {processingVoucherPayment && (
                                            <LoaderCircle className="h-4 w-4 animate-spin" />
                                        )}
                                        Pay Voucher
                                    </Button>
                                </form>
                            </div>

                            {/* Petty Cash Payment - Right Side */}
                            <div className="flex flex-1 flex-col gap-4 rounded-lg border p-4">
                                <h2 className="text-center text-xl font-semibold">
                                    Petty Cash Payment
                                </h2>
                                <form
                                    onSubmit={handlePettyCashPayment}
                                    className="flex flex-1 flex-col items-center justify-center gap-4"
                                >
                                    <div className="w-full max-w-sm">
                                        <Label htmlFor="amount">Amount</Label>
                                        <Input
                                            id="amount"
                                            type="number"
                                            step="0.01"
                                            value={pettyCashAmount}
                                            onChange={(e) =>
                                                setPettyCashAmount(
                                                    e.target.value,
                                                )
                                            }
                                            className="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                            placeholder="Enter amount"
                                        />
                                        {pettyCashErrors.length > 0 &&
                                            pettyCashErrors
                                                .filter((error) =>
                                                    error
                                                        .toLowerCase()
                                                        .includes('amount'),
                                                )
                                                .map((error, index) => (
                                                    <div
                                                        key={index}
                                                        className="text-sm text-red-600"
                                                    >
                                                        {error}
                                                    </div>
                                                ))}
                                    </div>
                                    <div className="w-full max-w-sm">
                                        <SelectExpenseCategory
                                            value={pettyCashCategory}
                                            onValueChange={setPettyCashCategory}
                                            onSelect={
                                                setSelectedExpenseCategory
                                            }
                                            label="Category"
                                            placeholder="Select a category"
                                        />
                                        {pettyCashErrors.length > 0 &&
                                            pettyCashErrors
                                                .filter((error) =>
                                                    error
                                                        .toLowerCase()
                                                        .includes('category'),
                                                )
                                                .map((error, index) => (
                                                    <div
                                                        key={index}
                                                        className="text-sm text-red-600"
                                                    >
                                                        {error}
                                                    </div>
                                                ))}
                                    </div>
                                    {normalizeCategoryName(
                                        selectedExpenseCategory,
                                    ) === 'refund' && (
                                        <div className="w-full max-w-sm">
                                            <FilterAndSelectTransaction
                                                value={pettyCashTransactionId}
                                                onValueChange={
                                                    setPettyCashTransactionId
                                                }
                                                onSelect={
                                                    handleRefundTransactionSelect
                                                }
                                                label="Transaction"
                                                placeholder="Find refunded transaction"
                                            />
                                            {pettyCashErrors.length > 0 &&
                                                pettyCashErrors
                                                    .filter((error) =>
                                                        error
                                                            .toLowerCase()
                                                            .includes(
                                                                'transaction_id',
                                                            ),
                                                    )
                                                    .map((error, index) => (
                                                        <div
                                                            key={index}
                                                            className="text-sm text-red-600"
                                                        >
                                                            {error}
                                                        </div>
                                                    ))}
                                        </div>
                                    )}
                                    <div className="w-full max-w-sm">
                                        <Label htmlFor="payed_to">
                                            Payed To
                                        </Label>
                                        <Select
                                            value={pettyCashPayedTo}
                                            onValueChange={setPettyCashPayedTo}
                                        >
                                            <SelectTrigger className="w-full">
                                                <SelectValue placeholder="Select a user" />
                                            </SelectTrigger>
                                            <SelectContent searchable>
                                                <SelectItem value="other">
                                                    Other
                                                </SelectItem>
                                                {users.map((user: any) => (
                                                    <SelectItem
                                                        key={user.id}
                                                        value={user.id.toString()}
                                                    >
                                                        {user.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {pettyCashErrors.length > 0 &&
                                            pettyCashErrors
                                                .filter((error) =>
                                                    error
                                                        .toLowerCase()
                                                        .includes('payed_to'),
                                                )
                                                .map((error, index) => (
                                                    <div
                                                        key={index}
                                                        className="text-sm text-red-600"
                                                    >
                                                        {error}
                                                    </div>
                                                ))}
                                    </div>
                                    {pettyCashPayedTo === 'other' && (
                                        <div className="w-full max-w-sm">
                                            <Label htmlFor="other_name">
                                                Other name
                                            </Label>
                                            <Input
                                                id="other_name"
                                                type="text"
                                                value={pettyCashOtherName}
                                                onChange={(e) =>
                                                    setPettyCashOtherName(
                                                        e.target.value,
                                                    )
                                                }
                                                className="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                                placeholder="Enter other person name"
                                            />
                                            {pettyCashErrors.length > 0 &&
                                                pettyCashErrors
                                                    .filter((error) =>
                                                        error
                                                            .toLowerCase()
                                                            .includes(
                                                                'payed_to_other',
                                                            ),
                                                    )
                                                    .map((error, index) => (
                                                        <div
                                                            key={index}
                                                            className="text-sm text-red-600"
                                                        >
                                                            {error}
                                                        </div>
                                                    ))}
                                        </div>
                                    )}
                                    {normalizeCategoryName(
                                        selectedExpenseCategory,
                                    ) === 'inpatient doctor payment' && (
                                        <div className="w-full max-w-sm">
                                            <FilterAndSelectServiceOrder
                                                value={pettyCashFileNumber}
                                                onValueChange={
                                                    setPettyCashFileNumber
                                                }
                                                label="File Number"
                                                placeholder="Find service order"
                                            />
                                            {pettyCashErrors.length > 0 &&
                                                pettyCashErrors
                                                    .filter((error) =>
                                                        error
                                                            .toLowerCase()
                                                            .includes(
                                                                'file_number',
                                                            ),
                                                    )
                                                    .map((error, index) => (
                                                        <div
                                                            key={index}
                                                            className="text-sm text-red-600"
                                                        >
                                                            {error}
                                                        </div>
                                                    ))}
                                        </div>
                                    )}
                                    <div className="w-full max-w-sm">
                                        <Label htmlFor="description">
                                            Description
                                        </Label>
                                        <textarea
                                            id="description"
                                            rows={4}
                                            className="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                            placeholder="Enter description"
                                            value={pettyCashDescription}
                                            onChange={(e) =>
                                                setPettyCashDescription(
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        {pettyCashErrors.length > 0 &&
                                            pettyCashErrors
                                                .filter((error) =>
                                                    error
                                                        .toLowerCase()
                                                        .includes(
                                                            'description',
                                                        ),
                                                )
                                                .map((error, index) => (
                                                    <div
                                                        key={index}
                                                        className="text-sm text-red-600"
                                                    >
                                                        {error}
                                                    </div>
                                                ))}
                                    </div>
                                    <Button
                                        type="submit"
                                        className={clsx('w-full max-w-sm', {
                                            'cursor-not-allowed opacity-50':
                                                !pettyCashAmount ||
                                                parseFloat(pettyCashAmount) <=
                                                    0 ||
                                                processingPettyCashPayment,
                                        })}
                                        disabled={
                                            !pettyCashAmount ||
                                            parseFloat(pettyCashAmount) <= 0 ||
                                            processingPettyCashPayment
                                        }
                                    >
                                        {processingPettyCashPayment && (
                                            <LoaderCircle className="h-4 w-4 animate-spin" />
                                        )}
                                        Pay Amount
                                    </Button>
                                </form>
                            </div>
                        </div>
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}
