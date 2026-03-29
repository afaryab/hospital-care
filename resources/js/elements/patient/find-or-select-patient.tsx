import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Check, Search, UserPlus } from 'lucide-react';
import React, { useState } from 'react';

interface Patient {
    id: string;
    ps: string;
    name: string;
    age: number;
    contactNo?: string;
    cnic?: string;
    isExactMatch?: boolean;
}

interface PatientMatch {
    score: 'A+' | '2';
    patient: Patient;
    matchType: 'exact' | 'partial' | 'new';
    message: string;
}

interface FindOrSelectPatientProps {
    isOpen: boolean;
    onClose: () => void;
    onPatientSelected: (patient: Patient) => void;
    onNewPatientCreated: (patient: Patient) => void;
}

const FindOrSelectPatient: React.FC<FindOrSelectPatientProps> = ({
    isOpen,
    onClose,
    onPatientSelected,
    onNewPatientCreated,
}) => {
    const [currentStep, setCurrentStep] = useState<'search' | 'results'>(
        'search',
    );
    const [formData, setFormData] = useState({
        psn: '',
        name: '',
        contactNo: '',
        cnic: '',
    });
    const [isLoading, setIsLoading] = useState(false);
    const [matches, setMatches] = useState<PatientMatch[]>([]);

    const handleInputChange = (field: string, value: string) => {
        setFormData((prev) => ({
            ...prev,
            [field]: value,
        }));
    };

    const handleSearch = async () => {
        setIsLoading(true);

        try {
            // Simulate API call - replace with actual API
            await new Promise((resolve) => setTimeout(resolve, 1000));

            // Mock search results based on the mockup
            const mockMatches: PatientMatch[] = [
                {
                    score: 'A+',
                    patient: {
                        id: '1',
                        ps: '2025/10/000674',
                        name: 'Patient name',
                        age: 12,
                    },
                    matchType: 'exact',
                    message: 'Exact match found',
                },
                {
                    score: '2',
                    patient: {
                        id: '2',
                        ps: '2025/10/000674',
                        name: 'XYZ Name',
                        age: 12,
                    },
                    matchType: 'partial',
                    message: 'Name / Age does not match',
                },
            ];

            setMatches(mockMatches);
            setCurrentStep('results');
        } catch (error) {
            console.error('Error searching patients:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const handleSelectPatient = (patient: Patient) => {
        onPatientSelected(patient);
        handleClose();
    };

    const handleAddNew = async () => {
        setIsLoading(true);

        try {
            // Simulate API call to create new patient - replace with actual API
            await new Promise((resolve) => setTimeout(resolve, 1000));

            const newPatient: Patient = {
                id: Date.now().toString(), // Temporary ID
                ps: `2025/10/${Date.now().toString().slice(-6)}`,
                name: formData.name,
                age: 0, // Calculate from form data if needed
                contactNo: formData.contactNo,
                cnic: formData.cnic,
            };

            onNewPatientCreated(newPatient);
            handleClose();
        } catch (error) {
            console.error('Error creating patient:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const handleClose = () => {
        setCurrentStep('search');
        setFormData({
            psn: '',
            name: '',
            contactNo: '',
            cnic: '',
        });
        setMatches([]);
        onClose();
    };

    const canSearch =
        formData.psn || formData.name || formData.contactNo || formData.cnic;

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className="max-h-[90vh] max-w-4xl overflow-hidden">
                <DialogHeader>
                    <DialogTitle>Find or Select Patient</DialogTitle>
                </DialogHeader>

                <div className="grid h-full grid-cols-1 gap-6 lg:grid-cols-2">
                    {/* Left Side - Search Form */}
                    <div className="space-y-4">
                        <div className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="psn">Patient PSN</Label>
                                <Input
                                    id="psn"
                                    type="text"
                                    placeholder="Enter patient PSN"
                                    value={formData.psn}
                                    onChange={(e) =>
                                        handleInputChange('psn', e.target.value)
                                    }
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="name">Patient Name</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    placeholder="Enter patient name"
                                    value={formData.name}
                                    onChange={(e) =>
                                        handleInputChange(
                                            'name',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="contactNo">Contact No.</Label>
                                <Input
                                    id="contactNo"
                                    type="text"
                                    placeholder="Enter contact number"
                                    value={formData.contactNo}
                                    onChange={(e) =>
                                        handleInputChange(
                                            'contactNo',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="cnic">CNIC</Label>
                                <Input
                                    id="cnic"
                                    type="text"
                                    placeholder="Enter CNIC"
                                    value={formData.cnic}
                                    onChange={(e) =>
                                        handleInputChange(
                                            'cnic',
                                            e.target.value,
                                        )
                                    }
                                />
                            </div>
                        </div>

                        {currentStep === 'search' && (
                            <Button
                                onClick={handleSearch}
                                disabled={!canSearch || isLoading}
                                className="flex w-full items-center space-x-2"
                            >
                                <Search className="h-4 w-4" />
                                <span>
                                    {isLoading
                                        ? 'Searching...'
                                        : 'Search Patient'}
                                </span>
                            </Button>
                        )}

                        {currentStep === 'results' && (
                            <div className="flex space-x-2">
                                <Button
                                    variant="outline"
                                    onClick={() => setCurrentStep('search')}
                                    className="flex-1"
                                >
                                    Back to Search
                                </Button>
                                <Button
                                    onClick={handleSearch}
                                    disabled={isLoading}
                                    className="flex flex-1 items-center space-x-2"
                                >
                                    <Search className="h-4 w-4" />
                                    <span>Search Again</span>
                                </Button>
                            </div>
                        )}
                    </div>

                    {/* Right Side - Search Results */}
                    <div className="border-l pl-6">
                        {currentStep === 'search' ? (
                            <div className="flex h-full items-center justify-center text-gray-500">
                                <div className="text-center">
                                    <Search className="mx-auto mb-4 h-12 w-12 text-gray-300" />
                                    <p>
                                        Enter patient information and click
                                        search to find matching records
                                    </p>
                                </div>
                            </div>
                        ) : (
                            <div className="space-y-4">
                                <h3 className="text-lg font-semibold">
                                    We found following record against provided
                                    information
                                </h3>

                                <div className="max-h-96 space-y-3 overflow-y-auto">
                                    {matches.map((match, index) => (
                                        <Card
                                            key={index}
                                            className={`cursor-pointer transition-all hover:shadow-md ${
                                                match.matchType === 'exact'
                                                    ? 'border-teal-200 bg-teal-50'
                                                    : 'border-orange-200 bg-orange-50'
                                            }`}
                                            onClick={() =>
                                                handleSelectPatient(
                                                    match.patient,
                                                )
                                            }
                                        >
                                            <CardContent className="p-4">
                                                <div className="flex items-center space-x-3">
                                                    <div
                                                        className={`flex h-8 w-10 items-center justify-center rounded text-sm font-bold text-white ${match.score === 'A+' ? 'bg-teal-500' : 'bg-orange-500'} `}
                                                    >
                                                        {match.score}
                                                    </div>
                                                    <div className="flex-1">
                                                        <div className="text-sm font-medium">
                                                            PS:{' '}
                                                            {match.patient.ps}
                                                        </div>
                                                        <div className="text-sm">
                                                            Name:{' '}
                                                            {match.patient.name}
                                                        </div>
                                                        <div className="text-sm">
                                                            Age:{' '}
                                                            {match.patient.age}
                                                        </div>
                                                        <div
                                                            className={`mt-1 text-xs ${
                                                                match.matchType ===
                                                                'exact'
                                                                    ? 'text-teal-700'
                                                                    : 'text-orange-700'
                                                            }`}
                                                        >
                                                            {match.message}
                                                        </div>
                                                    </div>
                                                    <Check className="h-5 w-5 text-gray-400" />
                                                </div>
                                            </CardContent>
                                        </Card>
                                    ))}

                                    {/* Add New Patient Option */}
                                    <Card
                                        className="cursor-pointer border-blue-200 bg-blue-50 transition-all hover:shadow-md"
                                        onClick={handleAddNew}
                                    >
                                        <CardContent className="p-4">
                                            <div className="flex items-center space-x-3">
                                                <div className="flex h-8 w-10 items-center justify-center rounded bg-blue-500 text-sm font-bold text-white">
                                                    2
                                                </div>
                                                <div className="flex-1">
                                                    <div className="text-sm font-medium text-blue-700">
                                                        Add New
                                                    </div>
                                                </div>
                                                <UserPlus className="h-5 w-5 text-blue-500" />
                                            </div>
                                        </CardContent>
                                    </Card>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default FindOrSelectPatient;
