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
import { appointments, counter, counterList, counterListAll, expenses, home, patientsRegister, register } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Folder, LucideHome, BookAIcon, LucideShoppingBasket, Calendar1, CircleDollarSign, ListTree, ChartLine, Cog, ListPlus, LucideListPlus } from 'lucide-react';
import AppLogo from './app-logo';

const receptionMenuItems: NavItem[] = [
    {
        title: 'Counter',
        href: counter(),
        icon: LucideShoppingBasket,
    },
    {
        title: 'Appointments',
        href: appointments(),
        icon: Calendar1,
    },
    {
        title: 'Expenses',
        href: expenses(),
        icon: CircleDollarSign,
    },
    {
        title: 'My Counters',
        href: counterList(),
        icon: LucideListPlus,
    },
];

const accountsMenuItems: NavItem[] = [
    {
        title: 'Counter statements',
        href: counterListAll(),
        icon: LucideListPlus,
    },
];

const adminMenuItems: NavItem[] = [
    {
        title: 'Summaries',
        href: '/summeries',
        icon: ListTree,
    },
    {
        title: 'Reports',
        href: '/reports',
        icon: ChartLine,
    },
    {
        title: 'Administration',
        href: '/admin',
        icon: Cog,
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
    const page = usePage();
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={home()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <SidebarMenu className='px-2'>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url == home().url}
                            tooltip={{ children: 'Home page' }}
                        >
                            <Link href={home().url} prefetch>
                                <LucideHome />
                                <span>Home</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url.startsWith(
                                patientsRegister().url
                            ) || page.url == patientsRegister().url}
                            tooltip={{ children: 'Patients register' }}
                        >
                            <Link href={patientsRegister().url} prefetch>
                                <BookAIcon />
                                <span>Register</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                <NavMain title='Counter' items={receptionMenuItems} />
                <NavMain title='Accounts' items={accountsMenuItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={adminMenuItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
