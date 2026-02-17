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
import { appointments, counter, counterListAll, receaveables, expenses, home, hospitalDentalQueue, hospitalEmergencyQueue, hospitalIndoorQueue, hospitalLaboratoryQueue, hospitalOpdQueue, hospitalRadiologyQueue, hospitalUltrasoundQueue, myCounterList, patientsRegister, register, transactionSearch } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Folder, LucideHome, BookAIcon, LucideShoppingBasket, Calendar1, LucideWaypoints, ListTree, ChartLine, Cog, ListPlus, LucideListPlus, LucideBlinds } from 'lucide-react';
import AppLogo from './app-logo';
import AppLogoIcon from './app-logo-icon';

export function AppSidebar() {

    const page = usePage();

    const { props } = page;

    const { auth } = props;

    const { user } = auth;

    console.log('Current page URL:', user);

    const haveAdminProfile = user?.profiles?.admin && user?.profiles?.admin.length > 0;
    const haveAccountantProfile = user?.profiles?.accountant && user?.profiles?.accountant.length > 0;
    const haveReceptionistProfile = user?.profiles?.receptionist && user?.profiles?.receptionist.length > 0;
    const haveNursingProfile = user?.profiles?.nursing_staff && user?.profiles?.nursing_staff.length > 0;
    const haveEmergencyDoctorProfile = user?.profiles?.emergency_doctor && user?.profiles?.emergency_doctor.length > 0;
    const haveOPDDoctorProfile = user?.profiles?.opd_doctor && user?.profiles?.opd_doctor.length > 0;
    const haveIndoorDoctorProfile = user?.profiles?.ind_doctor && user?.profiles?.ind_doctor.length > 0;
    const haveDentistProfile = user?.profiles?.dentist && user?.profiles?.dentist.length > 0;
    const haveUltrasoundDoctorProfile = user?.profiles?.ultrasound_doctor && user?.profiles?.ultrasound_doctor.length > 0;
    const haveXrayDoctorProfile = user?.profiles?.xray_technician && user?.profiles?.xray_technician.length > 0;

    const havePatientManagerProfile = user?.profiles?.patient_manager && user?.profiles?.patient_manager.length > 0;

    let adminMenuItems: NavItem[] = [];

    if(haveAccountantProfile){
        adminMenuItems.push(
            {
                title: 'Summaries',
                href: '/summeries',
                icon: ListTree,
            }
        );

        adminMenuItems.push(
            {
                title: 'Accounts',
                href: '/accounts',
                icon: ChartLine,
            }
        );
    }

    if(haveAdminProfile){
        adminMenuItems.push(
            {
                title: 'Administration',
                href: '/admin',
                icon: Cog,
            }
        );
    }



    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild className='hover:bg-transparent'>
                            <Link href={home()} prefetch >
                                <AppLogoIcon size={10} direction="horizontal" />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <SidebarMenu className='px-2'>
                    { (
                        haveReceptionistProfile 
                        || haveAdminProfile 
                        || haveAccountantProfile 
                        || haveNursingProfile 
                        || haveEmergencyDoctorProfile 
                        || haveOPDDoctorProfile 
                        || haveIndoorDoctorProfile 
                        || haveDentistProfile 
                        || haveUltrasoundDoctorProfile 
                        || haveXrayDoctorProfile
                    ) && <SidebarMenuItem>
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
                    </SidebarMenuItem>}
                    { (
                        haveReceptionistProfile
                        || haveAccountantProfile 
                        || haveNursingProfile 
                        || haveEmergencyDoctorProfile 
                        || haveOPDDoctorProfile 
                        || haveIndoorDoctorProfile 
                        || haveDentistProfile 
                        || haveUltrasoundDoctorProfile 
                        || haveXrayDoctorProfile
                    ) && <SidebarMenuItem>
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
                    </SidebarMenuItem>}
                </SidebarMenu>
                { haveReceptionistProfile && <SidebarMenu className='px-2'>
                    <span className="sidebar-menu-label text-[#06df72] text-sm">Reception</span>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url.startsWith(
                                counter().url
                            ) || page.url == counter().url}
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
                            isActive={page.url.startsWith(
                                receaveables().url
                            ) || page.url == receaveables().url}
                            tooltip={{ children: 'Receaveables' }}
                        >
                            <Link href={receaveables().url} prefetch>
                                <LucideWaypoints />
                                <span>Receaveables</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url.startsWith(
                                transactionSearch().url
                            ) || page.url == transactionSearch().url}
                            tooltip={{ children: 'Transaction Lookup' }}
                        >
                            <Link href={transactionSearch().url} prefetch>
                                <LucideWaypoints />
                                <span>Transaction Lookup</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url.startsWith(
                                myCounterList().url
                            ) || page.url == myCounterList().url}
                            tooltip={{ children: 'My Closings' }}
                        >
                            <Link href={myCounterList().url} prefetch>
                                <LucideBlinds />
                                <span>My Closings</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>}
                <SidebarMenu className='px-2'>
                    <span className="sidebar-menu-label text-[#06df72] text-sm">Departments</span>
                    { (
                        haveReceptionistProfile 
                        || haveAdminProfile 
                        || haveAccountantProfile 
                        || haveNursingProfile 
                        || haveEmergencyDoctorProfile 
                        || haveOPDDoctorProfile
                    ) && <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url == hospitalOpdQueue().url}
                            tooltip={{ children: 'Home page' }}
                        >
                            <Link href={hospitalOpdQueue().url} prefetch>
                                <img src='/img/opd.png' className='h-4' />
                                <span>OPD</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>}
                    { (
                        haveReceptionistProfile 
                        || haveAdminProfile 
                        || haveAccountantProfile 
                        || haveNursingProfile 
                        || haveEmergencyDoctorProfile 
                        || haveOPDDoctorProfile 
                        || haveIndoorDoctorProfile 
                        || haveDentistProfile 
                        || haveUltrasoundDoctorProfile 
                        || haveXrayDoctorProfile
                    ) && <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url == hospitalIndoorQueue().url}
                            tooltip={{ children: 'Home page' }}
                        >
                            <Link href={hospitalIndoorQueue().url} prefetch>
                                <img src='/img/ind.png' className='h-4' />
                                <span>Indoor</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>}
                    { (
                        haveReceptionistProfile 
                        || haveAdminProfile 
                        || haveAccountantProfile 
                        || haveNursingProfile 
                        || haveEmergencyDoctorProfile 
                        || haveOPDDoctorProfile 
                        || haveIndoorDoctorProfile 
                        || haveDentistProfile 
                        || haveUltrasoundDoctorProfile 
                        || haveXrayDoctorProfile
                    ) && <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url == hospitalEmergencyQueue().url}
                            tooltip={{ children: 'Home page' }}
                        >
                            <Link href={hospitalEmergencyQueue().url} prefetch>
                                <img src='/img/emergency.png' className='h-4' />
                                <span>Emergency</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>}
                    { (
                        haveReceptionistProfile 
                        || haveAdminProfile 
                        || haveAccountantProfile 
                        || haveNursingProfile 
                        || haveEmergencyDoctorProfile 
                        || haveOPDDoctorProfile 
                        || haveIndoorDoctorProfile 
                        || haveDentistProfile 
                        || haveUltrasoundDoctorProfile 
                        || haveXrayDoctorProfile
                    ) && <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url == hospitalDentalQueue().url}
                            tooltip={{ children: 'Home page' }}
                        >
                            <Link href={hospitalDentalQueue().url} prefetch>
                                <img src='/img/dental.png' className='h-4' />
                                <span>Dental</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>}
                    { (
                        haveReceptionistProfile 
                        || haveAdminProfile 
                        || haveAccountantProfile 
                        || haveNursingProfile 
                        || haveEmergencyDoctorProfile 
                        || haveOPDDoctorProfile 
                        || haveIndoorDoctorProfile 
                        || haveDentistProfile 
                        || haveUltrasoundDoctorProfile 
                        || haveXrayDoctorProfile
                    ) && <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url == hospitalLaboratoryQueue().url}
                            tooltip={{ children: 'Home page' }}
                        >
                            <Link href={hospitalLaboratoryQueue().url} prefetch>
                                <img src='/img/laboratory.png' className='h-4' />
                                <span>Laboratory</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>}
                    { (
                        haveReceptionistProfile 
                        || haveAdminProfile 
                        || haveAccountantProfile 
                        || haveNursingProfile 
                        || haveEmergencyDoctorProfile 
                        || haveOPDDoctorProfile 
                        || haveIndoorDoctorProfile 
                        || haveDentistProfile 
                        || haveUltrasoundDoctorProfile 
                        || haveXrayDoctorProfile
                    ) && <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url == hospitalUltrasoundQueue().url}
                            tooltip={{ children: 'Home page' }}
                        >
                            <Link href={hospitalUltrasoundQueue().url} prefetch>
                                <img src='/img/ultrasound.png' className='h-4' />
                                <span>Ultrasound</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>}
                    { (
                        haveReceptionistProfile 
                        || haveAdminProfile 
                        || haveAccountantProfile 
                        || haveNursingProfile 
                        || haveEmergencyDoctorProfile 
                        || haveOPDDoctorProfile 
                        || haveIndoorDoctorProfile 
                        || haveDentistProfile 
                        || haveUltrasoundDoctorProfile 
                        || haveXrayDoctorProfile
                    ) && <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            isActive={page.url == hospitalRadiologyQueue().url}
                            tooltip={{ children: 'Home page' }}
                        >
                            <Link href={hospitalRadiologyQueue().url} prefetch>
                                <img src='/img/xray.png' className='h-4' />
                                <span>Radiology</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>}
                </SidebarMenu>
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={adminMenuItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
