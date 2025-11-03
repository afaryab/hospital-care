import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { counter, dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid } from 'lucide-react';
import AppLogo from './app-logo';
import admin from '@/routes/admin';

const receptionMenuItems: NavItem[] = [
    {
        title: 'Register',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Counter',
        href: counter(),
        icon: LayoutGrid,
    },
    {
        title: 'Appointments',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Expenses',
        href: dashboard(),
        icon: LayoutGrid,
    }
];

const adminMenuItems: NavItem[] = [
    {
        title: 'Summaries',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Reports',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Administration',
        href: admin.hospitalSettings(),
        icon: LayoutGrid,
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain title='Counter' items={receptionMenuItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavMain items={adminMenuItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
