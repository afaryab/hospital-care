import { Patient } from '@/types';
import { LucidePhoneIncoming } from 'lucide-react';
import React from 'react';

interface PatientMiniCardProps {
    patient: Patient;
    className?: string;
}

const PatientMiniCard: React.FC<PatientMiniCardProps> = ({ patient, className = '' }) => {
    const { ps_number, name, gender, contact, cnic, age } = patient;

    return (
        <div className={`bg-white dark:bg-neutral-950 dark:text-white rounded-lg shadow-md p-4 ${className}`}>
            <div className="flex items-center space-x-4">
                <div className="flex-shrink-0">
                    <div className="w-12 h-12 bg-gray-50">
                        {gender ? (
                            <img 
                                src={gender == 'm' ? '/img/male-blue.png' : (gender == 'f' ? '/img/female-blue.png' : (gender == 't' ? '/img/transgender-blue.png' : '/img/avatar.png'))} 
                                alt={name}
                                className="w-full h-full object-cover"
                            />
                        ) : (
                            <div className="w-full h-full flex items-center justify-center bg-blue-100 text-blue-600">
                                <span className="text-xl font-semibold">
                                    {name.charAt(0).toUpperCase()}
                                </span>
                            </div>
                        )}
                    </div>
                </div>
                
                <div className="flex-1 min-w-0">
                    <h3 className="text-lg font-semibold truncate">
                        {name}
                    </h3>
                    
                    <div className="mt-2 space-y-1">
                        <span>{ps_number ? ps_number : 'Not Assigned Yet'}</span>
                        
                    </div>
                </div>
                <div className="flex flex-col space-y-2 text-right">
                    <div className="flex flex-row items-end text-sm">
                        <span className="text-blue-500">
                            <LucidePhoneIncoming size={16} />
                        </span>
                        <span className="ml-2">{contact}</span>
                    </div>
                    <div className='text-sm text-right'>
                        <span>Age: </span>
                        <span className='font-bold'>{age ? age : 'N/A'}</span>
                    </div>
                </div>

            </div>
        </div>
    );
};

export default PatientMiniCard;