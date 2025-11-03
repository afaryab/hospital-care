import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Upload, Save } from 'lucide-react';
import AdminPanel from '@/layouts/panels/admin-panel';
import { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import PageTemplate from '@/components/page-template';

const HospitalSettings: React.FC = () => {
    const [hospitalName, setHospitalName] = useState('');
    const [hospitalLogo, setHospitalLogo] = useState<File | null>(null);
    const [hospitalAddress, setHospitalAddress] = useState('');
    const [hospitalContactInfo, setHospitalContactInfo] = useState('');
    const [logoPreview, setLogoPreview] = useState<string>('');
    const [isLoading, setIsLoading] = useState(false);

    const handleLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setHospitalLogo(file);
            const reader = new FileReader();
            reader.onload = (e) => {
                setLogoPreview(e.target?.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        
        try {
            const formData = new FormData();
            formData.append('name', hospitalName);
            formData.append('address', hospitalAddress);
            formData.append('contact_info', hospitalContactInfo);
            if (hospitalLogo) {
                formData.append('logo', hospitalLogo);
            }
            
            // API call would go here
            console.log('Saving hospital settings:', { 
                hospitalName, 
                hospitalAddress, 
                hospitalContactInfo, 
                hospitalLogo 
            });
            
            // Simulate API delay
            await new Promise(resolve => setTimeout(resolve, 1000));
            
        } catch (error) {
            console.error('Error saving settings:', error);
        } finally {
            setIsLoading(false);
        }
    };
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'Administration',
            href: '',
        },
        {
            title: 'Hospital Settings',
            href: '',
        },
    ];

    return (
        <AdminPanel title="Administration - Hospital Settings" breadcrumbs={breadcrumbs}>
            <PageTemplate title="Hospital Settings" subtitle="Manage your hospital's basic information">
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="hospitalName">Hospital Name</Label>
                        <Input
                            id="hospitalName"
                            type="text"
                            placeholder="Enter hospital name"
                            value={hospitalName}
                            onChange={(e) => setHospitalName(e.target.value)}
                            required
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="hospitalLogo">Hospital Logo</Label>
                        <div className="flex flex-col space-y-4">
                            <div className="flex items-center space-x-4">
                                <Input
                                    id="hospitalLogo"
                                    type="file"
                                    accept="image/*"
                                    onChange={handleLogoChange}
                                    className="hidden"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => document.getElementById('hospitalLogo')?.click()}
                                    className="flex items-center space-x-2"
                                >
                                    <Upload className="w-4 h-4" />
                                    <span>Upload Logo</span>
                                </Button>
                                {hospitalLogo && (
                                    <span className="text-sm text-gray-600">
                                        {hospitalLogo.name}
                                    </span>
                                )}
                            </div>
                            
                            {logoPreview && (
                                <div className="mt-4">
                                    <img
                                        src={logoPreview}
                                        alt="Hospital logo preview"
                                        className="w-32 h-32 object-cover rounded-lg border border-gray-300"
                                    />
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="hospitalAddress">Hospital Address</Label>
                        <textarea
                            id="hospitalAddress"
                            placeholder="Enter complete hospital address"
                            value={hospitalAddress}
                            onChange={(e) => setHospitalAddress(e.target.value)}
                            rows={3}
                            className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="hospitalContactInfo">Hospital Contact Information</Label>
                        <textarea
                            id="hospitalContactInfo"
                            placeholder="Enter contact details (phone, email, fax, etc.)"
                            value={hospitalContactInfo}
                            onChange={(e) => setHospitalContactInfo(e.target.value)}
                            rows={4}
                            className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        />
                    </div>

                    <Button
                        type="submit"
                        disabled={isLoading || !hospitalName || !hospitalAddress || !hospitalContactInfo}
                        className="w-full flex items-center space-x-2"
                    >
                        <Save className="w-4 h-4" />
                        <span>{isLoading ? 'Saving...' : 'Save Hospital Settings'}</span>
                    </Button>
                </form>
            </PageTemplate>
        </AdminPanel>
    );
};

export default HospitalSettings;