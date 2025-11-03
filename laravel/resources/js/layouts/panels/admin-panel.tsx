import { dashboard } from '@/routes';
import { BreadcrumbItem, NavItem } from '@/types';
import { LayoutGrid } from 'lucide-react';
import React from 'react';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { NavMain } from '@/components/nav-main';
import AppLayout from '../app-layout';
import { Head } from '@inertiajs/react';
import admin from '@/routes/admin';

interface AdminPanelProps {
    title?: string;
    breadcrumbs?: BreadcrumbItem[];
    children?: React.ReactNode;
}

const AdminPanel: React.FC<AdminPanelProps> = ({ title, breadcrumbs, children }) => {

    const adminMenuItems: NavItem[] = [
        {
            title: 'Hospital',
            href: admin.hospitalSettings(),
            icon: LayoutGrid,
        },
        {
            title: 'Receptions',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Services',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Panels',
            href: dashboard(),
            icon: LayoutGrid,
        }
    ];

    const usersMenuItems: NavItem[] = [
        {
            title: 'Accounts',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Executives',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Doctors',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Receptions',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Nursing Staff',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];


    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={title || 'Admin Panel'} />
            <div className="admin-panel grid grid-cols-12 gap-4 h-full">
                <div className='col-span-3 border-r block pt-4 bg-indigo-800'>
                    <NavMain items={adminMenuItems} />
                    <NavMain title='Users' items={usersMenuItems} />
                </div>
                <div className='col-span-9'>
                    {children}
                </div>
            </div>
        </AppLayout>
    );
};

export default AdminPanel;