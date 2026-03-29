// @ts-nocheck
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import BulletsWrapper from '@/elements/bullets-wrapper';
import PatientMiniCard from '@/elements/patient/mini-card';
import AppLayout from '@/layouts/app-layout';
import {
    counterSelectDepartment,
    counterView,
    home,
    myCounterList,
    printTransaction,
} from '@/routes';

import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { useState } from 'react';
import { toast } from 'sonner';

export default function TransactionView() {
    const { transaction, auth } = usePage().props as {
        transaction: any;
        auth?: { user?: { profiles?: { admin?: unknown[] } } };
    };

    const isAdminUser = (auth?.user?.profiles?.admin?.length ?? 0) > 0;
    const [isRefunding, setIsRefunding] = useState(false);

    const { closing: openCounter } = transaction;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: home().url,
        },
        {
            title: 'Counters',
            href: openCounter
                ? counterView({
                      ctYear: openCounter.year,
                      ctMonth: openCounter.month,
                      ctNumber: openCounter.number,
                  }).url
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
            active: false,
        });

    if (transaction.income_or_expense === 'INCOME') {
        bullets.push({
            title: `Patient ${transaction?.patient?.ps_number}`,
            url:
                transaction?.patient?.ps_number &&
                counterSelectDepartment({
                    pYear: transaction.patient.year,
                    pMonth: transaction.patient.month,
                    number: transaction.patient.number,
                }).url,
            active: false,
        });

        bullets.push({
            title: `Transaction ${transaction.tr_number}`,
            url: printTransaction({
                year: transaction.year,
                month: transaction.month,
                day: transaction.day,
                number: transaction.number,
            }).url,
            active: true,
        });
    }

    const [printVariant, setPrintVariant] = useState<
        'thermal' | 'dot-printer' | 'full'
    >('thermal');

    const handleRefund = async () => {
        const confirmed = window.confirm(
            `Refund ${transaction.tr_number}? This cannot be undone.`,
        );

        if (!confirmed) {
            return;
        }

        setIsRefunding(true);

        try {
            const response = await fetch(
                `/api/transactions/${transaction.id}/refund`,
                {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                },
            );

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                toast.error(payload?.message ?? 'Unable to refund transaction.');
                return;
            }

            router.reload();
        } finally {
            setIsRefunding(false);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Counter ${openCounter?.ct_number} `} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-[#06df72] p-1 dark:bg-[#262626]">
                <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-white p-2 text-gray-800">
                    <BulletsWrapper bullets={bullets}>
                        <div className="flex h-full w-full flex-col gap-4 overflow-x-auto rounded-xl bg-white p-2">
                            <div className="grid h-full grid-cols-1 gap-4 divide-x divide-[#06df72]">
                                <div className="flex hidden h-full flex-col gap-4 pr-4">
                                    {transaction.patient && (
                                        <PatientMiniCard
                                            patient={transaction.patient}
                                        />
                                    )}
                                    <div className="inset-shadow-lg flex h-full w-full max-w-96 min-w-80 flex-shrink bg-gray-300">
                                        <div className="mx-auto my-auto w-80 rounded bg-gray-50 px-6 pt-8 shadow-lg">
                                            <div className="flex flex-col gap-3 border-b py-6 text-xs">
                                                <p className="flex justify-between">
                                                    <span className="text-gray-400">
                                                        Receipt No.:
                                                    </span>
                                                    <span>
                                                        {transaction.tr_number}
                                                    </span>
                                                </p>
                                                <p className="flex justify-between">
                                                    <span className="text-gray-400">
                                                        Order Type:
                                                    </span>
                                                    <span>
                                                        {transaction.type}
                                                    </span>
                                                </p>
                                                {transaction.income_or_expense ===
                                                    'INCOME' && (
                                                    <>
                                                        <p className="flex justify-between">
                                                            <span className="text-gray-400">
                                                                Patient:
                                                            </span>
                                                            <span>
                                                                {
                                                                    transaction
                                                                        ?.patient
                                                                        ?.name
                                                                }
                                                            </span>
                                                        </p>
                                                        <p className="flex justify-between">
                                                            <span className="text-gray-400">
                                                                {
                                                                    transaction
                                                                        ?.patient
                                                                        ?.ps_number
                                                                }
                                                            </span>
                                                        </p>
                                                    </>
                                                )}
                                            </div>
                                            <div className="flex flex-col gap-3 pt-2 pb-6 text-xs">
                                                <table className="w-full text-left">
                                                    <thead>
                                                        <tr className="flex">
                                                            <th className="w-full py-2">
                                                                Product
                                                            </th>
                                                            <th className="min-w-[44px] py-2">
                                                                QTY
                                                            </th>
                                                            <th className="min-w-[44px] py-2">
                                                                Total
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {transaction.elements.map(
                                                            (
                                                                item: any,
                                                                index,
                                                            ) => (
                                                                <>
                                                                    <tr
                                                                        key={
                                                                            item.service_id
                                                                        }
                                                                        className="flex"
                                                                    >
                                                                        <td className="flex-1 py-1">
                                                                            {
                                                                                item
                                                                                    .service
                                                                                    ?.name
                                                                            }
                                                                        </td>
                                                                        <td className="min-w-[44px]">
                                                                            1
                                                                        </td>
                                                                        <td className="min-w-[44px]">
                                                                            {
                                                                                item.amount
                                                                            }
                                                                        </td>
                                                                    </tr>
                                                                    <tr
                                                                        className={clsx(
                                                                            'flex bg-gray-100',
                                                                            {
                                                                                // Is not last add border
                                                                                'border-b':
                                                                                    item !==
                                                                                    transaction
                                                                                        .elements[
                                                                                        transaction
                                                                                            .elements
                                                                                            .length -
                                                                                            1
                                                                                    ],
                                                                            },
                                                                        )}
                                                                    >
                                                                        <td
                                                                            colSpan={
                                                                                3
                                                                            }
                                                                            className="flex-1 py-1 text-xs"
                                                                        >
                                                                            {
                                                                                item
                                                                                    ?.service_order
                                                                                    ?.so_number
                                                                            }
                                                                        </td>
                                                                    </tr>
                                                                </>
                                                            ),
                                                        )}
                                                    </tbody>
                                                </table>
                                                <div className="border border-b border-dashed"></div>
                                                <div className="flex justify-between py-1 text-sm">
                                                    <span>Total:</span>
                                                    <span>
                                                        {transaction?.amount}
                                                    </span>
                                                </div>
                                                <div className="flex justify-between py-1 text-sm">
                                                    <span>Payment Method:</span>
                                                    <span className="capitalize">
                                                        {transaction.type}
                                                    </span>
                                                </div>
                                                <div className="flex justify-between py-1 text-sm">
                                                    <span>Amount Paid:</span>
                                                    <span>
                                                        {
                                                            transaction?.customer_payed
                                                        }
                                                    </span>
                                                </div>
                                                <div className="flex justify-between py-1 text-sm font-semibold">
                                                    <span>
                                                        {transaction?.change > 0
                                                            ? `Change:`
                                                            : `Balance`}
                                                    </span>
                                                    <span>
                                                        {transaction?.change}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    className={clsx(
                                        'grid h-full grid-cols-1 gap-4 pr-4',
                                    )}
                                >
                                    <div className="flex h-full flex-col space-y-4">
                                        {isAdminUser &&
                                            transaction?.income_or_expense ===
                                                'INCOME' &&
                                            !transaction?.is_refunded && (
                                                <div className="flex justify-end">
                                                    <Button
                                                        variant="destructive"
                                                        disabled={isRefunding}
                                                        onClick={handleRefund}
                                                    >
                                                        {isRefunding
                                                            ? 'Refunding...'
                                                            : 'Refund Transaction'}
                                                    </Button>
                                                </div>
                                            )}
                                        <div className="flex w-full flex-row">
                                            <div className="grid gap-2">
                                                <Label htmlFor="print">
                                                    Print Version
                                                </Label>
                                                <Select
                                                    value={printVariant}
                                                    onValueChange={(
                                                        value:
                                                            | 'thermal'
                                                            | 'dot-printer'
                                                            | 'full',
                                                    ) => setPrintVariant(value)}
                                                >
                                                    <SelectTrigger id="print">
                                                        <SelectValue placeholder="Select Print Version" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem
                                                            key={'full'}
                                                            value={'full'}
                                                        >
                                                            Full page
                                                        </SelectItem>
                                                        <SelectItem
                                                            key={'thermal'}
                                                            value={'thermal'}
                                                        >
                                                            Thermal
                                                        </SelectItem>
                                                        <SelectItem
                                                            key={'dot-printer'}
                                                            value={
                                                                'dot-printer'
                                                            }
                                                        >
                                                            Dot Printer
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                {/* <InputError message={errors.email} /> */}
                                            </div>
                                        </div>
                                        <iframe
                                            title="Transaction Print Preview"
                                            src={
                                                printTransaction({
                                                    year: transaction.year,
                                                    month: transaction.month,
                                                    day: transaction.day,
                                                    number: transaction.number,
                                                }).url +
                                                `?variant=${printVariant}`
                                            }
                                            className="h-full min-h-[300px] w-full border"
                                        ></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BulletsWrapper>
                </div>
            </div>
        </AppLayout>
    );
}
