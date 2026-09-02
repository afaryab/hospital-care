import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface Hospital {
    name: string;
    logoUrl: string | null;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    timezone: string;
    hospital: Hospital;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    timezone: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface Patient {
    id: string;
    ps_number: string;
    year: number;
    month: number;
    number: number;
    name: string;
    image?: string;
    gender: 'm' | 'f' | 't' | 'o';
    contact: string;
    cnic: string;
    /** Computed age in years — may be absent; prefer formatPatientAge() */
    age?: number;
    /** ISO date string — present when DOB is known */
    age_dob?: string | null;
    /** Age expressed in days — used for infants when DOB is unknown */
    age_days?: number | null;
    /** Freeform age group label e.g. "Adult", "Infant" */
    age_group?: string | null;
    address?: string | null;
    guardian?: string | null;
    relation?: string | null;
    treatments: ServiceOrder[];
    transactions: Transaction[];
    receaveables: Receaveable[];
}

export interface ServiceDepartment {
    id: number;
    name: string;
    slug: string;
    image?: string;
    /** Resolved, renderable URL — always use this over `image` for <img src>. */
    image_url?: string;
}

export interface Service {
    id: number;
    name: string;
    icon?: string | null;
    service_department_id: number;
    charge: number;
    charges_include_tax: boolean;
    have_service_provider: boolean;
}

export interface Transaction {
    id: number;
    tr_number: string;
    old_id: string;
    closing_id: number;
    created_by: number;
    patient_id?: number;
    type: string;
    income_or_expense: 'INCOME' | 'EXPENSE';
    amount: number;
    amount_alphabetical: string;
    orignal_amount: number;
    customer_payed: number;
    change: number;
    edited_amount: number;
    created_at: string;
    updated_at: string;
    elements?: any[];
    patient?: Patient;
    closing?: any;
    year?: number | string;
    month?: number | string;
    day?: number | string;
    number?: number | string;
    [key: string]: unknown;
}

export interface ServiceOrder {
    id?: number;
    type: string;
    so_number: string;
    so_short: string;
    created_by: number;
    patient_id: number;
    service_id?: number;
    service_recestation_id?: number;
    doctor_id: number;
    is_composit: boolean;
    notes: string;
    notes_json: any;
    created_at?: string;
    updated_at?: string;
    departmentKey?: string;
    serviceNumber?: string;
    [key: string]: unknown;
}

export interface Receaveable {
    id: number;
    patient: Patient;
    transaction: Transaction;
    orignal_amount: number;
    amount: number;
    due_date: string;
    status: string;
    created_at: string;
    closed_at?: string;
    linked_service_order?: {
        id: number;
        so_number: string;
        so_short?: string;
    } | null;
    payments?: Array<{
        id: number;
        tr_number: string;
        amount: number;
        created_at: string;
    }>;
    expense_vouchers?: Array<{
        id: number;
        vc_number: string;
        amount: number;
        share_amount: number;
        status: string;
    }>;
}

export interface Closing {
    id: number;
    ct_number: string;
    year?: number | string;
    month?: number | string;
    number?: number | string;
    status: string;
    reception_id?: number;
    receptionist_id?: number;
    opening_amount: number;
    closing_amount?: number;
    closing_amount_cash?: number;
    closing_amount_cheque?: number;
    closing_amount_card?: number;
    expense_payed?: number;
    amount_received?: number;
    closed_at?: string;
    created_at: string;
    updated_at: string;
    reception?: { id: number; name: string };
    receptionist?: User;
    [key: string]: unknown;
}

export interface ExpenseVoucher {
    id: number;
    vc_number: string;
    exp_category_id?: number;
    service_order_id?: number;
    payed_to?: string;
    payed_to_name?: string;
    amount: number;
    notes?: string;
    transaction_id?: number;
    transaction_element_id?: number;
    status: 'pending' | 'payed';
    created_at: string;
    updated_at: string;
    expenseCategory?: { id: number; name: string };
    [key: string]: unknown;
}

export type Receivable = Receaveable;

export interface PaymentMethod {
    id: number;
    name: string;
    slug: string;
    id_required: boolean;
    payables?: string | null;
    [key: string]: unknown;
}

export interface Panel {
    id: number;
    name: string;
    [key: string]: unknown;
}

export interface DmsClassification {
    id: number;
    name: string;
    code: string;
    security_level: 'public' | 'internal' | 'confidential' | 'restricted';
    retention_years?: number | null;
    description?: string | null;
    [key: string]: unknown;
}

export interface DmsFolder {
    id: number;
    uuid: string;
    name: string;
    parent_id: number | null;
    path: string;
    classification_id: number | null;
    classification?: DmsClassification | null;
    is_system: boolean;
    created_by: number;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
}

export interface DmsDocument {
    id: number;
    uuid: string;
    folder_id: number;
    name: string;
    classification_id: number | null;
    classification?: DmsClassification | null;
    status: string;
    is_locked: boolean;
    locked_by: number | null;
    lockedBy?: User | null;
    locked_at: string | null;
    current_version: number;
    created_by: number;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
}

export interface DmsFolderOption {
    uuid: string;
    label: string;
}
