import { NavFooter } from '@/components/nav-footer';
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
import {
    counter,
    counterExpenseVouchersList,
    home,
    hospitalDentalQueue,
    hospitalEmergencyQueue,
    hospitalIndoorQueue,
    hospitalLaboratoryQueue,
    hospitalOpdQueue,
    hospitalRadiologyQueue,
    hospitalUltrasoundQueue,
    myCounterList,
    patientsRegister,
    receaveables,
    transactionEditSearch,
    transactionSearch,
} from '@/routes';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import {
    BookAIcon,
    ChartLine,
    Cog,
    ListTree,
    LucideBlinds,
    LucideHome,
    LucideShoppingBasket,
    LucideWaypoints,
} from 'lucide-react';
import AppLogoIcon from './app-logo-icon';

export function AppSidebar() {
    const page = usePage<SharedData>();

    const { props } = page;

    const { auth, routeName } = props as any;

    const { user } = auth;

    console.log('Current page URL:', user);

    const haveAdminProfile =
        user?.profiles?.admin && user?.profiles?.admin.length > 0;
    const haveAccountantProfile =
        user?.profiles?.accountant && user?.profiles?.accountant.length > 0;
    const haveReceptionistProfile =
        user?.profiles?.receptionist && user?.profiles?.receptionist.length > 0;
    const haveNursingProfile =
        user?.profiles?.nursing_staff &&
        user?.profiles?.nursing_staff.length > 0;
    const haveEmergencyDoctorProfile =
        user?.profiles?.emergency_doctor &&
        user?.profiles?.emergency_doctor.length > 0;
    const haveOPDDoctorProfile =
        user?.profiles?.opd_doctor && user?.profiles?.opd_doctor.length > 0;
    const haveIndoorDoctorProfile =
        user?.profiles?.ind_doctor && user?.profiles?.ind_doctor.length > 0;
    const haveDentistProfile =
        user?.profiles?.dentist && user?.profiles?.dentist.length > 0;
    const haveUltrasoundDoctorProfile =
        user?.profiles?.ultrasound_doctor &&
        user?.profiles?.ultrasound_doctor.length > 0;
    const haveXrayDoctorProfile =
        user?.profiles?.xray_technician &&
        user?.profiles?.xray_technician.length > 0;

    const havePatientManagerProfile =
        user?.profiles?.patient_manager &&
        user?.profiles?.patient_manager.length > 0;

    const adminMenuItems: NavItem[] = [];

    if (haveAccountantProfile) {
        adminMenuItems.push({
            title: 'Summaries',
            href: '/summeries',
            icon: ListTree,
        });

        adminMenuItems.push({
            title: 'Accounts',
            href: '/accounts',
            icon: ChartLine,
        });
    }

    if (haveAdminProfile) {
        adminMenuItems.push({
            title: 'Administration',
            href: '/admin',
            icon: Cog,
        });
    }

    const isEditingTransaction =
        routeName === 'transaction-edit' ||
        page.url == transactionEditSearch().url;
    const isViewingTransaction =
        routeName === 'transaction-view' || page.url == transactionSearch().url;

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            size="lg"
                            asChild
                            className="hover:bg-transparent"
                        >
                            <Link href={home()} prefetch>
                                <AppLogoIcon size={10} direction="horizontal" />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <SidebarMenu className="px-2">
                    {(haveReceptionistProfile ||
                        haveAdminProfile ||
                        haveAccountantProfile ||
                        haveNursingProfile ||
                        haveEmergencyDoctorProfile ||
                        haveOPDDoctorProfile ||
                        haveIndoorDoctorProfile ||
                        haveDentistProfile ||
                        haveUltrasoundDoctorProfile ||
                        haveXrayDoctorProfile) && (
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
                    )}
                    {(haveReceptionistProfile ||
                        haveAccountantProfile ||
                        haveNursingProfile ||
                        haveEmergencyDoctorProfile ||
                        haveOPDDoctorProfile ||
                        haveIndoorDoctorProfile ||
                        haveDentistProfile ||
                        haveUltrasoundDoctorProfile ||
                        haveXrayDoctorProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    page.url.startsWith(
                                        patientsRegister().url,
                                    ) || page.url == patientsRegister().url
                                }
                                tooltip={{ children: 'Patients register' }}
                            >
                                <Link href={patientsRegister().url} prefetch>
                                    <BookAIcon />
                                    <span>Register</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                </SidebarMenu>
                {haveReceptionistProfile && (
                    <SidebarMenu className="px-2">
                        <span className="sidebar-menu-label text-sm text-[#06df72]">
                            Reception
                        </span>
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    routeName === 'counter' ||
                                    routeName === 'counter-view' ||
                                    routeName === 'counter-open' ||
                                    routeName === 'counter-close' ||
                                    routeName === 'counter-select-patient' ||
                                    routeName === 'counter-patient-services' ||
                                    routeName ===
                                        'counter-select-department-service' ||
                                    routeName === 'counter-expense'
                                }
                                tooltip={{ children: 'Counter' }}
                            >
                                <Link href={counter().url} prefetch>
                                    <LucideShoppingBasket />
                                    <span>Counter</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    routeName ===
                                        'counter-expense-vouchers-list' ||
                                    routeName === 'counter-expense-new-voucher'
                                }
                                tooltip={{ children: 'Expense Vouchers' }}
                            >
                                <Link
                                    href={counterExpenseVouchersList().url}
                                    prefetch
                                >
                                    <LucideWaypoints />
                                    <span>Expense Vouchers</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={routeName === 'receaveables'}
                                tooltip={{ children: 'Receaveables' }}
                            >
                                <Link href={receaveables().url} prefetch>
                                    <LucideWaypoints />
                                    <span>Receaveables</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                        {(routeName === 'transaction-view' ||
                            routeName === 'transaction-search') && (
                            <SidebarMenuItem>
                                <SidebarMenuButton
                                    asChild
                                    isActive={true}
                                    tooltip={{ children: 'Print Reciept' }}
                                >
                                    <Link
                                        href={transactionSearch().url}
                                        prefetch
                                    >
                                        <LucideWaypoints />
                                        <span>Print Reciept</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        )}
                        {(routeName === 'transaction-edit' ||
                            routeName === 'transaction-edit-search') && (
                            <SidebarMenuItem>
                                <SidebarMenuButton
                                    asChild
                                    isActive={true}
                                    tooltip={{ children: 'Edit Reciept' }}
                                >
                                    <Link
                                        href={transactionEditSearch().url}
                                        prefetch
                                    >
                                        <LucideWaypoints />
                                        <span>Edit Reciept</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        )}
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    page.url.startsWith(myCounterList().url) ||
                                    page.url == myCounterList().url
                                }
                                tooltip={{ children: 'My Closings' }}
                            >
                                <Link href={myCounterList().url} prefetch>
                                    <LucideBlinds />
                                    <span>My Closings</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                )}
                <SidebarMenu className="px-2">
                    <span className="sidebar-menu-label text-sm text-[#06df72]">
                        Departments
                    </span>
                    {(haveReceptionistProfile ||
                        haveAdminProfile ||
                        haveAccountantProfile ||
                        haveNursingProfile ||
                        haveEmergencyDoctorProfile ||
                        haveOPDDoctorProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url == hospitalOpdQueue().url}
                                tooltip={{ children: 'Home page' }}
                            >
                                <Link href={hospitalOpdQueue().url} prefetch>
                                    <img src="/img/opd.png" className="h-4" />
                                    <span>OPD</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveReceptionistProfile ||
                        haveAdminProfile ||
                        haveAccountantProfile ||
                        haveNursingProfile ||
                        haveEmergencyDoctorProfile ||
                        haveOPDDoctorProfile ||
                        haveIndoorDoctorProfile ||
                        haveDentistProfile ||
                        haveUltrasoundDoctorProfile ||
                        haveXrayDoctorProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url == hospitalIndoorQueue().url}
                                tooltip={{ children: 'Home page' }}
                            >
                                <Link href={hospitalIndoorQueue().url} prefetch>
                                    <img src="/img/ind.png" className="h-4" />
                                    <span>Indoor</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveReceptionistProfile ||
                        haveAdminProfile ||
                        haveAccountantProfile ||
                        haveNursingProfile ||
                        haveEmergencyDoctorProfile ||
                        haveOPDDoctorProfile ||
                        haveIndoorDoctorProfile ||
                        haveDentistProfile ||
                        haveUltrasoundDoctorProfile ||
                        haveXrayDoctorProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    page.url == hospitalEmergencyQueue().url
                                }
                                tooltip={{ children: 'Home page' }}
                            >
                                <Link
                                    href={hospitalEmergencyQueue().url}
                                    prefetch
                                >
                                    <img
                                        src="/img/emergency.png"
                                        className="h-4"
                                    />
                                    <span>Emergency</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveReceptionistProfile ||
                        haveAdminProfile ||
                        haveAccountantProfile ||
                        haveNursingProfile ||
                        haveEmergencyDoctorProfile ||
                        haveOPDDoctorProfile ||
                        haveIndoorDoctorProfile ||
                        haveDentistProfile ||
                        haveUltrasoundDoctorProfile ||
                        haveXrayDoctorProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url == hospitalDentalQueue().url}
                                tooltip={{ children: 'Home page' }}
                            >
                                <Link href={hospitalDentalQueue().url} prefetch>
                                    <img
                                        src="/img/dental.png"
                                        className="h-4"
                                    />
                                    <span>Dental</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveReceptionistProfile ||
                        haveAdminProfile ||
                        haveAccountantProfile ||
                        haveNursingProfile ||
                        haveEmergencyDoctorProfile ||
                        haveOPDDoctorProfile ||
                        haveIndoorDoctorProfile ||
                        haveDentistProfile ||
                        haveUltrasoundDoctorProfile ||
                        haveXrayDoctorProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    page.url == hospitalLaboratoryQueue().url
                                }
                                tooltip={{ children: 'Home page' }}
                            >
                                <Link
                                    href={hospitalLaboratoryQueue().url}
                                    prefetch
                                >
                                    <img
                                        src="/img/laboratory.png"
                                        className="h-4"
                                    />
                                    <span>Laboratory</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveReceptionistProfile ||
                        haveAdminProfile ||
                        haveAccountantProfile ||
                        haveNursingProfile ||
                        haveEmergencyDoctorProfile ||
                        haveOPDDoctorProfile ||
                        haveIndoorDoctorProfile ||
                        haveDentistProfile ||
                        haveUltrasoundDoctorProfile ||
                        haveXrayDoctorProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    page.url == hospitalUltrasoundQueue().url
                                }
                                tooltip={{ children: 'Home page' }}
                            >
                                <Link
                                    href={hospitalUltrasoundQueue().url}
                                    prefetch
                                >
                                    <img
                                        src="/img/ultrasound.png"
                                        className="h-4"
                                    />
                                    <span>Ultrasound</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveReceptionistProfile ||
                        haveAdminProfile ||
                        haveAccountantProfile ||
                        haveNursingProfile ||
                        haveEmergencyDoctorProfile ||
                        haveOPDDoctorProfile ||
                        haveIndoorDoctorProfile ||
                        haveDentistProfile ||
                        haveUltrasoundDoctorProfile ||
                        haveXrayDoctorProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    page.url == hospitalRadiologyQueue().url
                                }
                                tooltip={{ children: 'Home page' }}
                            >
                                <Link
                                    href={hospitalRadiologyQueue().url}
                                    prefetch
                                >
                                    <img src="/img/xray.png" className="h-4" />
                                    <span>Radiology</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                </SidebarMenu>
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={adminMenuItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
