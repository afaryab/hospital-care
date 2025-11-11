import { Patient } from '@/types';
import React from 'react';

interface PatientMiniCardProps {
    patient: Patient;
    className?: string;
}

const PatientMiniCard: React.FC<PatientMiniCardProps> = ({ patient, className = '' }) => {
    const { name, gender, contact, cnic, age } = patient;

    return (
        <div className={`bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow ${className}`}>
            <div className="flex items-center space-x-4">
                <div className="flex-shrink-0">
                    <div className="w-16 h-16 bg-gray-50">
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
                    <h3 className="text-lg font-semibold text-gray-900 truncate">
                        {name}
                    </h3>
                    
                    <div className="mt-2 space-y-1">
                        <div className="flex items-center text-sm text-gray-600">
                            <span className="font-medium">Contact:</span>
                            <span className="ml-2">{contact}</span>
                        </div>
                        
                        <div className="flex items-center text-sm text-gray-600">
                            <span className="font-medium">CNIC:</span>
                            <span className="ml-2">{cnic}</span>
                        </div>
                        
                        <div className="flex items-center text-sm text-gray-600">
                            <span className="font-medium">Age:</span>
                            <span className="ml-2">{age} years</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PatientMiniCard;