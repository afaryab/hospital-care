import { Transaction } from '@/types';
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

const PatientTransactionsHistoryCard: React.FC<PatientTransactionsHistoryCardProps> = ({ patient, className }) => {
    return (
        <div className={clsx('bg-white dark:bg-neutral-700 p-4 border border-dotted rounded-xl dark:border-white', className)}>
            
            {patient.transactions.length === 0 ? (
                <p className="text-gray-500 dark:text-white">No transactions found</p>
            ) : (
                <div className="space-y-3">
                    {patient.transactions.map((transaction) => (
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
            )}
        </div>
    );
};

export default PatientTransactionsHistoryCard;