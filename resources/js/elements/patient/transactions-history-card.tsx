import { Button } from '@/components/ui/button';
import { ServiceOrder, Transaction } from '@/types';
import clsx from 'clsx';
import React from 'react';


interface Patient {
    id: number;
    name: string;
    transactions: Transaction[];
}

interface PatientTransactionsHistoryCardProps {
    patient: Patient;
    className?: string;
}

const PatientTransactionsHistory: React.FC<{ transactions: Transaction[] }> = ({ transactions }) => {
    return (
        <>{transactions.length === 0 ? (
                <p className="text-gray-500 dark:text-white">No transactions found</p>
            ) : (
                <div className="space-y-3">
                    {transactions.map((transaction) => (
                        <div
                            key={transaction.id}
                            className="flex flex-col p-3 border rounded-md"
                        >
                            <div className=''>
                                {transaction.tr_number}
                            </div>
                            <div className='flex flex-col md:flex-row md:flex-row items-center justify-between '>
                                <div className='w-full'>
                                    <p>{new Date(transaction.created_at).toLocaleDateString()}</p>
                                    <p>{new Date(transaction.created_at).toLocaleTimeString()}</p>
                                </div>
                                <div className="text-right w-full">
                                    <p
                                        className={`font-semibold ${
                                            transaction.income_or_expense === 'INCOME' ? 'text-green-600' : 'text-red-600'
                                        }`}
                                    >
                                        {transaction.income_or_expense === 'INCOME' ? '+' : '-'} {transaction.amount}
                                    </p>
                                    <span
                                        className={`text-xs px-2 py-1 rounded ${
                                            transaction.income_or_expense === 'INCOME'
                                                ? 'bg-green-100 text-green-800'
                                                : transaction.income_or_expense === 'EXPENSE'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-red-100 text-red-800'
                                        }`}
                                    >
                                        {transaction.type}
                                    </span>
                                    
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}</>
    );
};

const PatientTreatmentsHistory: React.FC<{ treatments: ServiceOrder[] }> = ({ treatments }) => {
    return (
        <>{treatments.length === 0 ? (
                <p className="text-gray-500 dark:text-white">No treatments found</p>
            ) : (
                <div className="space-y-3">
                    {treatments.map((treatment) => (
                        <div
                            key={treatment.id}
                            className="flex flex-col p-3 border rounded-md"
                        >
                            <div className=''>
                                {treatment.so_number}
                            </div>
                            <div className='flex flex-col md:flex-row md:flex-row items-center justify-between '>
                                <div className='w-full'>
                                    <p>{new Date(treatment.created_at).toLocaleDateString()}</p>
                                    <p>{new Date(treatment.created_at).toLocaleTimeString()}</p>
                                </div>
                                <div className="text-right w-full">
                                    <p
                                        className={`font-semibold `}
                                    >
                                        sdf
                                    </p>
                                    
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}</>
    );
};

const PatientTransactionsHistoryCard: React.FC<PatientTransactionsHistoryCardProps> = ({ patient, className }) => {

    const [selectedTab, setSelectedTab] = React.useState<'transactions' | 'treatments' | 'receiveables'>('transactions');

    return (
        <div className={clsx('bg-white dark:bg-neutral-700', className)}>
            {/* Tab Bullets */}
            <div className='flex flex-row space-x-2 mb-2 p-2 border-2 border-dotted rounded-xl dark:border-white'>
                <Button onClick={() => setSelectedTab("transactions")} variant={ selectedTab === "transactions" ? "default" : "secondary"} >Transactions</Button>
                <Button onClick={() => setSelectedTab("treatments")} variant={ selectedTab === "treatments" ? "default" : "secondary"} >Treatments</Button>
                <Button onClick={() => setSelectedTab("receiveables")} variant={ selectedTab === "receiveables" ? "default" : "secondary"} >Receiveables</Button>
            </div>
            <div className='p-2 overflow-y-scroll border-2 border-dotted rounded-tl-xl dark:border-white' style={{ height: 'calc(80% - 1px)'}}>
                {selectedTab === 'transactions' && (
                    <>
                        <PatientTransactionsHistory transactions={patient.transactions} />
                    </>
                )}
                {selectedTab === 'treatments' && (
                    <>
                        <PatientTreatmentsHistory treatments={patient.treatments} />
                    </>
                )}
                {selectedTab === 'receiveables' && (
                    <>
                        <p className="text-gray-500 dark:text-white">No receiveables found</p>
                    </>
                )}
            </div>
            
        </div>
    );
};

export default PatientTransactionsHistoryCard;