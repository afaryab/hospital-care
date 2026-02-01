import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import BulletsWrapper from '@/elements/bullets-wrapper';
import HumanSimpleBody from '@/human/simple-body';
import AppLayout from '@/layouts/app-layout';
import { apiExpenseVouchersIndex, counterExpense, counterList, counterSelectPatient, counterView, home, myCounterList, patientsRegister, patientsRegisterPsNumberDepartment, printClosingStatement, transactionStore } from '@/routes';
import { User, type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { LucidePrinter, LucideShoppingBasket, LucideSearch, LucideLoader2 } from 'lucide-react';
import { useState, useEffect } from 'react';

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

export default function CounterExpense() {

    const { openCounter, users, categories } = usePage<{ openCounter: any, users: User[], categories: any[] }>().props

    // Voucher search state
    const [voucherNumber, setVoucherNumber] = useState('');
    const [payedTo, setPayedTo] = useState('');
    const [payedToOther, setPayedToOther] = useState('');
    const [voucherData, setVoucherData] = useState<ExpenseVoucher | null>(null);
    const [isSearching, setIsSearching] = useState(false);
    const [searchResults, setSearchResults] = useState<VoucherSearchResults | null>(null);
    const [selectedVoucher, setSelectedVoucher] = useState<ExpenseVoucher | null>(null);
    

    // Petty cash state
    const [pettyCashAmount, setPettyCashAmount] = useState('');
    const [pettyCashDescription, setPettyCashDescription] = useState('');
    const [pettyCashCategory, setPettyCashCategory] = useState('');
    const [pettyCashPayedTo, setPettyCashPayedTo] = useState('');
    const [pettyCashOtherName, setPettyCashOtherName] = useState('');

    // Search voucher function
    const searchVoucher = async (vcNumber: string) => {
        if (!vcNumber || vcNumber.length < 3) {
            setSearchResults(null);
            return;
        }

        setIsSearching(true);
        try {
            const response = await fetch(apiExpenseVouchersIndex().url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    vc_number: vcNumber
                })
            });

            if (!response.ok) {
                throw new Error('Failed to search vouchers');
            }

            const data = await response.json();
            setSearchResults(data.data);
            
            // If exact match found, auto-select it
            if (data.data.exact && data.data.exact.length > 0) {
                setSelectedVoucher(data.data.exact[0]);
            }
        } catch (error) {
            console.error('Error searching vouchers:', error);
            setSearchResults(null);
        } finally {
            setIsSearching(false);
        }
    };

    // Handle voucher selection
    const selectVoucher = (voucher: ExpenseVoucher) => {
        setSelectedVoucher(voucher);
        setVoucherNumber(voucher.vc_number);
        setSearchResults(null);
    };

    // Handle voucher payment
    const handleVoucherPayment = (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedVoucher) {
            alert('Please search and select a voucher first');
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
        router.post(transactionStore().url, paymentData, {
            onSuccess: () => {
            // Reset form on success
            setSelectedVoucher(null);
            setVoucherNumber('');
            setPayedTo('');
            setPayedToOther('');
            setSearchResults(null);
            },
            onError: (errors) => {
            // Handle validation errors
            console.error('Validation errors:', errors);
            
            // Display errors to user
            const errorMessages = Object.values(errors).flat().join('\n');
            alert(`Payment failed:\n${errorMessages}`);
            }
        });
    };

    // Handle petty cash payment
    const handlePettyCashPayment = (e: React.FormEvent) => {
        e.preventDefault();
        if (!pettyCashAmount || parseFloat(pettyCashAmount) <= 0) {
            alert('Please enter a valid amount');
            return;
        }
        // TODO: Implement petty cash payment logic
        console.log('Paying petty cash:', pettyCashAmount);

        // Prepare form data for submission
        const paymentData = {
            amount: parseFloat(pettyCashAmount),
            description: pettyCashDescription,
            category_id: pettyCashCategory,
            payed_to: pettyCashPayedTo === 'other' ? null : pettyCashPayedTo,
            payed_to_other: pettyCashPayedTo === 'other' ? pettyCashOtherName : null,
            income_or_expense: 'EXPENSE',
            type: 'EXP',
        };

        console.log('Payment Data:', paymentData);

        // POST paymentData to server...
        router.post(transactionStore().url, paymentData, {
            onSuccess: () => {
                // Reset form on success
                setPettyCashAmount('');
                setPettyCashDescription('');
                setPettyCashCategory('');
                setPettyCashPayedTo('');
                setPettyCashOtherName('');
            },
            onError: (errors) => {
                // Handle validation errors
                console.error('Validation errors:', errors);
                
                // Display errors to user
                const errorMessages = Object.values(errors).flat().join('\n');
                alert(`Payment failed:\n${errorMessages}`);
            }
        });
    };

    // Debounced search effect
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            if (voucherNumber) {
                searchVoucher(voucherNumber);
            }
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [voucherNumber]);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Counter',
            href: openCounter ? counterView({
                ctYear: openCounter.year,
                ctMonth: openCounter.month,
                ctNumber: openCounter.number
            }).url : myCounterList().url, 
        },
        {
            title: 'Expenses',
            href: openCounter ? counterExpense().url : myCounterList().url, 
        },
    ];

    let bullets = [];

    openCounter && bullets.push({ 
        title: openCounter && openCounter.ct_number,
        url: openCounter && counterView({
            ctYear: openCounter.year,
            ctMonth: openCounter.month,
            ctNumber: openCounter.number
        }).url,
        active: true
    });

    openCounter && bullets.push({ 
        title: 'Expense Payment',
        url: openCounter && counterExpense().url,
        active: true
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Counter ${openCounter?.ct_number} Expense`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-1 bg-[#06df72] dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 bg-white text-gray-800">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-row gap-4 overflow-x-auto rounded-xl p-2 bg-white">
                            {/* Voucher Payment - Left Side */}
                            <div className="flex-1 flex flex-col gap-4 p-4 border rounded-lg">
                                <h2 className="text-xl font-semibold text-center">Voucher Payment</h2>
                                <form onSubmit={handleVoucherPayment} className="flex flex-col gap-4 items-center justify-center flex-1">
                                    <div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="voucherNumber">Voucher Number</Label>
                                        <div className="relative">
                                            <input
                                                id="voucherNumber"
                                                type="text"
                                                value={voucherNumber}
                                                onChange={(e) => setVoucherNumber(e.target.value)}
                                                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="Enter voucher number"
                                            />
                                            {isSearching && (
                                                <LucideLoader2 className="absolute right-2 top-2.5 h-4 w-4 animate-spin text-gray-400" />
                                            )}
                                        </div>
                                        
                                        {/* Search Results */}
                                        {searchResults && (searchResults.exact.length > 0 || searchResults.possible.length > 0) && (
                                            <div className="border rounded-md bg-white shadow-lg max-h-60 overflow-auto">
                                                {/* Exact Matches */}
                                                {searchResults.exact.length > 0 && (
                                                    <div>
                                                        <div className="px-3 py-2 bg-green-50 border-b text-sm font-semibold text-green-700">
                                                            Exact Match
                                                        </div>
                                                        {searchResults.exact.map((voucher: ExpenseVoucher) => (
                                                            <div
                                                                key={voucher.id}
                                                                onClick={() => selectVoucher(voucher)}
                                                                className="px-3 py-2 cursor-pointer hover:bg-gray-50 border-b last:border-b-0"
                                                            >
                                                                <div className="font-medium">{voucher.vc_number}</div>
                                                                <div className="text-sm text-gray-600">
                                                                    Amount: {voucher.amount || 'N/A'} | Status: {voucher.status || 'N/A'}
                                                                </div>
                                                            </div>
                                                        ))}
                                                    </div>
                                                )}
                                                
                                                {/* Possible Matches */}
                                                {searchResults.possible.length > 0 && (
                                                    <div>
                                                        <div className="px-3 py-2 bg-blue-50 border-b text-sm font-semibold text-blue-700">
                                                            Similar Matches
                                                        </div>
                                                        {searchResults.possible.map((voucher: ExpenseVoucher) => (
                                                            <div
                                                                key={voucher.id}
                                                                onClick={() => selectVoucher(voucher)}
                                                                className="px-3 py-2 cursor-pointer hover:bg-gray-50 border-b last:border-b-0"
                                                            >
                                                                <div className="font-medium">{voucher.vc_number}</div>
                                                                <div className="text-sm text-gray-600">
                                                                    Amount: {voucher.amount || 'N/A'} | Status: {voucher.status || 'N/A'}
                                                                </div>
                                                            </div>
                                                        ))}
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                        
                                        {/* Selected Voucher Info */}
                                        {selectedVoucher && (
                                            <div className="p-3 bg-green-50 border border-green-200 rounded-md">
                                                <div className="font-semibold text-green-700">Selected Voucher</div>
                                                <div className="text-sm">
                                                    <div>Number: {selectedVoucher.vc_number}</div>
                                                    <div>Amount: {selectedVoucher.amount || 'N/A'}</div>
                                                    <div>Status: {selectedVoucher.status || 'N/A'}</div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                    {searchResults && (searchResults.exact.length > 0 || searchResults.possible.length > 0) && (<div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="payedTo">Payed To</Label>
                                        <div className="relative">
                                            <Select
                                                onValueChange={(value) => setPayedTo(value)}
                                                value={payedTo}
                                            >
                                                <SelectTrigger className="w-full">
                                                    <SelectValue placeholder="Select user" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {users.map((user: User) => (
                                                        <SelectItem key={user.id} value={user.id.toString()}>
                                                            {user.name}
                                                        </SelectItem>
                                                    ))}
                                                    <SelectItem value="Other">Other</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                    </div>)}
                                    {payedTo === 'Other' && (<div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="payedToOther">Payed To (Other)</Label>
                                        <Input
                                            id="payedToOther"
                                            type="text"
                                            value={payedToOther}
                                            onChange={(e) => setPayedToOther(e.target.value)}
                                        />
                                    </div>)}
                                    <Button 
                                        type="submit" 
                                        className="w-full max-w-sm"
                                        disabled={!selectedVoucher}
                                    >
                                        Pay Voucher
                                    </Button>
                                </form>
                            </div>

                            {/* Petty Cash Payment - Right Side */}
                            <div className="flex-1 flex flex-col gap-4 p-4 border rounded-lg">
                                <h2 className="text-xl font-semibold text-center">Petty Cash Payment</h2>
                                <form onSubmit={handlePettyCashPayment} className="flex flex-col gap-4 items-center justify-center flex-1">
                                    <div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="amount">Amount</Label>
                                        <Input
                                            id="amount"
                                            type="number"
                                            step="0.01"
                                            value={pettyCashAmount}
                                            onChange={(e) => setPettyCashAmount(e.target.value)}
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Enter amount"
                                        />
                                    </div>
                                    <div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="category">Category</Label>
                                        <Select
                                            value={pettyCashCategory}
                                            onValueChange={setPettyCashCategory}
                                        >
                                            <SelectTrigger className="w-full">
                                                <SelectValue placeholder="Select a category" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {categories.map((category: any) => (
                                                    <SelectItem key={category.id} value={category.id.toString()}>
                                                        {category.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="payed_to">Payed To</Label>
                                        <Select
                                            value={pettyCashPayedTo}
                                            onValueChange={setPettyCashPayedTo}
                                        >
                                            <SelectTrigger className="w-full">
                                                <SelectValue placeholder="Select a user" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="other">Other</SelectItem>
                                                {users.map((user: any) => (
                                                    <SelectItem key={user.id} value={user.id.toString()}>
                                                        {user.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    {pettyCashPayedTo === 'other' && <div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="other_name">Other name</Label>
                                        <Input
                                            id="other_name"
                                            type="text"
                                            value={pettyCashOtherName}
                                            onChange={(e) => setPettyCashOtherName(e.target.value)}
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Enter other person name"
                                        />
                                    </div>}
                                    <div className="w-full max-w-sm space-y-2">
                                        <Label htmlFor="description">Description</Label>
                                        <textarea
                                            id="description"
                                            rows={4}
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Enter description"
                                            value={pettyCashDescription}
                                            onChange={(e) => setPettyCashDescription(e.target.value)}
                                        />
                                    </div>
                                    <Button type="submit" className="w-full max-w-sm">
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
