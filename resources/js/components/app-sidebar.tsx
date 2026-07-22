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
    dntDashboard,
    emgDashboard,
    home,
    hospitalDentalQueue,
    hospitalEmergencyQueue,
    hospitalIndoorQueue,
    hospitalLaboratoryQueue,
    hospitalOpdQueue,
    hospitalRadiologyQueue,
    hospitalUltrasoundQueue,
    indDashboard,
    labDashboard,
    myCounterList,
    myPatients,
    myPayments,
    opdDashboard,
    patientsRegister,
    receaveables,
    transactionEditSearch,
    transactionSearch,
    ultDashboard,
    xrayDashboard,
} from '@/routes';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import {
    BookAIcon,
    BriefcaseMedical,
    ChartLine,
    Cog,
    FlaskConical,
    ListTree,
    LucideBlinds,
    LucideHome,
    LucideShoppingBasket,
    LucideWaypoints,
    PiggyBank,
    Radiation,
    ScanLine,
    Siren,
    Stethoscope,
    Users,
    Waypoints,
} from 'lucide-react';
import AppLogoIcon from './app-logo-icon';

export function AppSidebar() {
    const page = usePage<SharedData>();

    const { props } = page;

    const { auth, routeName } = props as any;

    const { user } = auth;

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
                    {(haveOPDDoctorProfile || haveNursingProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url.startsWith('/OPD')}
                                tooltip={{ children: 'OPD Dashboard' }}
                            >
                                <Link href={opdDashboard().url} prefetch>
                                    <Stethoscope />
                                    <span>OPD</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveIndoorDoctorProfile || haveNursingProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url.startsWith('/IND')}
                                tooltip={{ children: 'Indoor / Inpatient' }}
                            >
                                <Link href={indDashboard().url} prefetch>
                                    <BriefcaseMedical />
                                    <span>Indoor</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveEmergencyDoctorProfile || haveNursingProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url.startsWith('/EMG')}
                                tooltip={{ children: 'Emergency Portal' }}
                            >
                                <Link href={emgDashboard().url} prefetch>
                                    <Siren />
                                    <span>Emergency</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveDentistProfile || haveNursingProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url.startsWith('/DNT')}
                                tooltip={{ children: 'Dental Portal' }}
                            >
                                <Link href={dntDashboard().url} prefetch>
                                    <Waypoints />
                                    <span>Dental</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveUltrasoundDoctorProfile || haveNursingProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url.startsWith('/ULT')}
                                tooltip={{ children: 'Ultrasound Portal' }}
                            >
                                <Link href={ultDashboard().url} prefetch>
                                    <ScanLine />
                                    <span>Ultrasound</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveXrayDoctorProfile || haveNursingProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={
                                    page.url.startsWith('/XRAY') ||
                                    page.url.startsWith('/RAD')
                                }
                                tooltip={{ children: 'Radiology Portal' }}
                            >
                                <Link href={xrayDashboard().url} prefetch>
                                    <Radiation />
                                    <span>Radiology</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                    {(haveNursingProfile ||
                        haveReceptionistProfile ||
                        haveAdminProfile) && (
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url.startsWith('/LAB')}
                                tooltip={{ children: 'Laboratory Portal' }}
                            >
                                <Link href={labDashboard().url} prefetch>
                                    <FlaskConical />
                                    <span>Laboratory</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )}
                </SidebarMenu>
                {(haveOPDDoctorProfile ||
                    haveIndoorDoctorProfile ||
                    haveEmergencyDoctorProfile ||
                    haveDentistProfile ||
                    haveUltrasoundDoctorProfile ||
                    haveXrayDoctorProfile) && (
                    <SidebarMenu className="px-2">
                        <span className="sidebar-menu-label text-sm text-[#06df72]">
                            My Work
                        </span>
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url.startsWith(myPatients().url)}
                                tooltip={{ children: 'My Patients' }}
                            >
                                <Link href={myPatients().url} prefetch>
                                    <Users />
                                    <span>My Patients</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url.startsWith(myPayments().url)}
                                tooltip={{ children: 'My Payments' }}
                            >
                                <Link href={myPayments().url} prefetch>
                                    <PiggyBank />
                                    <span>My Payments</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                )}
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
                                isActive={
                                    routeName === 'service-orders-overview' ||
                                    page.url.startsWith('/service-orders')
                                }
                                tooltip={{ children: 'Service Orders' }}
                            >
                                <Link href="/service-orders" prefetch>
                                    <ListTree />
                                    <span>Service Orders</span>
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
                        Queue
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
