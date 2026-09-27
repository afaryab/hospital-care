type ProfileMap = Record<string, unknown[] | undefined>;

export type Abilities = {
    authenticated: boolean;
    admin: boolean;
    accountant: boolean;
    receptionist: boolean;
    nursing: boolean;
    patientManager: boolean;
    opdDoctor: boolean;
    pedDoctor: boolean;
    indDoctor: boolean;
    emergencyDoctor: boolean;
    dentist: boolean;
    ultrasoundDoctor: boolean;
    xrayTechnician: boolean;
    anyDoctor: boolean;
    staff: boolean;
    lcd: (department: string) => boolean;
};

/**
 * Role flags derived from the shared `auth.user.profiles` prop — the same
 * rules the sidebar uses to decide which pages a user can open.
 */
export function abilitiesFor(
    user: { profiles?: ProfileMap } | null | undefined,
): Abilities {
    const has = (key: string) => (user?.profiles?.[key]?.length ?? 0) > 0;

    const anyDoctor =
        has('opd_doctor') ||
        has('ped_doctor') ||
        has('ind_doctor') ||
        has('emergency_doctor') ||
        has('dentist') ||
        has('ultrasound_doctor') ||
        has('xray_technician');

    return {
        authenticated: !!user,
        admin: has('admin'),
        accountant: has('accountant'),
        receptionist: has('receptionist'),
        nursing: has('nursing_staff'),
        patientManager: has('patient_manager'),
        opdDoctor: has('opd_doctor'),
        pedDoctor: has('ped_doctor'),
        indDoctor: has('ind_doctor'),
        emergencyDoctor: has('emergency_doctor'),
        dentist: has('dentist'),
        ultrasoundDoctor: has('ultrasound_doctor'),
        xrayTechnician: has('xray_technician'),
        anyDoctor,
        staff:
            anyDoctor ||
            has('admin') ||
            has('accountant') ||
            has('receptionist') ||
            has('nursing_staff'),
        lcd: (department: string) => has(`lcd_${department}`),
    };
}
