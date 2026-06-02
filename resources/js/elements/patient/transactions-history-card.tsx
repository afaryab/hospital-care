import Currency from '@/components/currency';
import DateTime from '@/components/date-time';
import { Button } from '@/components/ui/button';
import { Patient, Receaveable, ServiceOrder, Transaction } from '@/types';
import clsx from 'clsx';
import React from 'react';
import ReceaveAblesButton from '../receaveables/ReceaveAblesButton';

interface PatientHistorySideBarProps {
    patient: Patient;
    className?: string;
}

const PatientTransactionsHistory: React.FC<{ transactions: Transaction[] }> = ({
    transactions,
}) => {
    return (
        <>
            {transactions.length === 0 ? (
                <p className="text-gray-500 dark:text-white">
                    No transactions found
                </p>
            ) : (
                <div className="space-y-3">
                    {transactions.map((transaction) => (
                        <div
                            key={transaction.id}
                            className="flex flex-col rounded-md border p-3"
                        >
                            <div className="">{transaction.tr_number}</div>
                            <div className="flex flex-col items-center justify-between md:flex-row">
                                <div className="w-full">
                                    <DateTime
                                        value={transaction.created_at}
                                        className="text-sm"
                                    />
                                </div>
                                <div className="w-full text-right">
                                    <Currency
                                        value={transaction.amount}
                                        currency="PKR"
                                        fromMinorUnit={false}
                                        className={`font-semibold ${transaction.income_or_expense === 'INCOME' ? 'text-green-600' : 'text-red-600'}`}
                                    />
                                </div>
                            </div>
                            {transaction.elements &&
                                transaction.elements.length > 0 && (
                                    <div className="flex flex-col items-center justify-between md:flex-row">
                                        {transaction.elements.map(
                                            (element, index) => (
                                                <div
                                                    key={index}
                                                    className="w-full"
                                                >
                                                    <p>
                                                        {element.type} -{' '}
                                                        {element?.service
                                                            ?.name ??
                                                            element
                                                                ?.serviceOrder
                                                                ?.name}
                                                    </p>
                                                </div>
                                            ),
                                        )}
                                    </div>
                                )}
                        </div>
                    ))}
                </div>
            )}
        </>
    );
};

const PatientTreatmentsHistory: React.FC<{ treatments: ServiceOrder[] }> = ({
    treatments,
}) => {
    return (
        <>
            {treatments.length === 0 ? (
                <p className="text-gray-500 dark:text-white">
                    No treatments found
                </p>
            ) : (
                <div className="space-y-3">
                    {treatments.map((treatment) => (
                        <div
                            key={treatment.id}
                            className="flex flex-col rounded-md border p-3"
                        >
                            <div className="">
                                {treatment.so_number}
                                {treatment.so_short
                                    ? ` (${treatment.so_short})`
                                    : ''}
                            </div>
                            <div className="flex flex-col items-center justify-between md:flex-row">
                                <div className="w-full">
                                    <p>
                                        {new Date(
                                            treatment.created_at!,
                                        ).toLocaleDateString()}
                                    </p>
                                    <p>
                                        {new Date(
                                            treatment.created_at!,
                                        ).toLocaleTimeString()}
                                    </p>
                                </div>
                                <div className="w-full text-right">
                                    <Button>Open</Button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </>
    );
};

const PatientReceivableHistory: React.FC<{
    receaveables: Receaveable[];
    patient: Patient;
}> = ({ receaveables, patient }) => {
    return (
        <>
            {receaveables.length === 0 ? (
                <p className="text-gray-500 dark:text-white">
                    No receaveables found
                </p>
            ) : (
                <div className="space-y-3">
                    {receaveables.map((receaveable) => {
                        const clone = { ...receaveable };
                        clone.patient = patient;
                        return (
                            <div
                                key={receaveable.id}
                                className="flex flex-col rounded-md border p-3"
                            >
                                <div className="flex flex-col items-center justify-between md:flex-row">
                                    <div className="w-full">
                                        <p>
                                            {new Date(
                                                receaveable.created_at,
                                            ).toLocaleDateString()}
                                        </p>
                                        <p>
                                            {new Date(
                                                receaveable.created_at,
                                            ).toLocaleTimeString()}
                                        </p>
                                    </div>
                                    <div className="w-full text-right">
                                        <ReceaveAblesButton
                                            receaveable={clone}
                                            onConfirm={(
                                                amountCollected,
                                                note,
                                            ) => {
                                                // Handle the confirmation logic here
                                                console.log(
                                                    `Amount collected: ${amountCollected}, Note: ${note}`,
                                                );
                                            }}
                                        />
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </>
    );
};

const PatientHistorySideBar: React.FC<PatientHistorySideBarProps> = ({
    patient,
    className,
}) => {
    const [selectedTab, setSelectedTab] = React.useState<
        'transactions' | 'treatments' | 'receiveables'
    >('transactions');

    console.log(patient);

    return (
        <div className={clsx('bg-white dark:bg-neutral-700', className)}>
            {/* Tab Bullets */}
            <div className="mb-2 flex flex-row space-x-2 rounded-xl border-2 border-dotted p-2 dark:border-white">
                <Button
                    onClick={() => setSelectedTab('transactions')}
                    variant={
                        selectedTab === 'transactions' ? 'default' : 'secondary'
                    }
                >
                    TR ({patient.transactions.length})
                </Button>
                <Button
                    onClick={() => setSelectedTab('treatments')}
                    variant={
                        selectedTab === 'treatments' ? 'default' : 'secondary'
                    }
                >
                    SO ({patient.treatments.length})
                </Button>
                <Button
                    onClick={() => setSelectedTab('receiveables')}
                    variant={
                        selectedTab === 'receiveables' ? 'default' : 'secondary'
                    }
                >
                    RE ({patient.receaveables.length})
                </Button>
            </div>
            <div
                className="overflow-y-scroll rounded-tl-xl border-2 border-dotted p-2 dark:border-white"
                style={{ height: 'calc(80% - 1px)' }}
            >
                {selectedTab === 'transactions' && (
                    <>
                        <PatientTransactionsHistory
                            transactions={patient.transactions}
                        />
                    </>
                )}
                {selectedTab === 'treatments' && (
                    <>
                        <PatientTreatmentsHistory
                            treatments={patient.treatments}
                        />
                    </>
                )}
                {selectedTab === 'receiveables' && (
                    <>
                        <PatientReceivableHistory
                            receaveables={patient.receaveables}
                            patient={patient}
                        />
                    </>
                )}
            </div>
        </div>
    );
};

export default PatientHistorySideBar;
