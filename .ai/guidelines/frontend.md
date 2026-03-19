# Frontend Conventions (React + Inertia + TypeScript)

Project-specific frontend patterns extracted from the codebase. Follow these when creating or editing React/TypeScript files.

---

## Page Components

- **Location:** `resources/js/pages/`
- **Naming:** kebab-case filenames (e.g., `counter/income.tsx`, `patient.tsx`)
- Default export React component
- Props via `usePage<PropsType>().props`
- Wrap in `AppLayout` with breadcrumbs
- Use `<Head>` for page title

```tsx
import { usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

type CounterIncomeProps = {
    selectedPatient?: Patient;
    departments: Department[];
    openCounter?: Closing;
};

export default function CounterIncome() {
    const { selectedPatient, departments, openCounter } = usePage<CounterIncomeProps>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Counter Income" />
            {/* content */}
        </AppLayout>
    );
}
```

---

## Components

- **Location:** `resources/js/components/`
- **Naming:** kebab-case (e.g., `currency.tsx`, `input-error.tsx`)
- Typed props interface
- Named export or default export
- Use 30+ shadcn/ui Radix components from `@/components/ui/`

```tsx
type CurrencyProps = {
    value: number;
    currency?: string;
    className?: string;
};

const Currency: React.FC<CurrencyProps> = ({ value, currency = 'PKR', className }) => {
    const formatted = React.useMemo(() => formatCurrency(value, currency), [value, currency]);
    return <span className={className}>{formatted}</span>;
};

export default Currency;
```

---

## Elements (Domain Components)

- **Location:** `resources/js/elements/`
- **Organization:** By domain (`patient/`, `counter/`, `department/`, `expense-voucher/`, `serviceorder/`)
- Reusable UI pieces for specific business domains
- Can be compound components

---

## Layouts

- **Location:** `resources/js/layouts/`
- `app-layout.tsx` — Main layout with sidebar/header
- Accept `children` and `breadcrumbs` props

```tsx
interface AppLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({ children, breadcrumbs, ...props }: AppLayoutProps) => (
    <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
        {children}
    </AppLayoutTemplate>
);
```

---

## Hooks

- **Location:** `resources/js/hooks/`
- **Naming:** `use-{name}.ts` or `.tsx`
- Existing hooks: `use-appearance`, `use-clipboard`, `use-mobile`, `use-mobile-navigation`, `use-two-factor-auth`, `use-initials`

---

## Types

- **Location:** `resources/js/types/index.d.ts`
- Interface-based type system
- Include `[key: string]: unknown` for extensibility on shared interfaces
- Domain-specific types: `Patient`, `Transaction`, `ServiceOrder`, `Department`, etc.

```typescript
export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface Patient {
    id: string;
    ps_number: string;
    year: number;
    month: number;
    name: string;
    gender: 'm' | 'f' | 't' | 'o';
    contact: string;
    cnic: string;
    age: number;
    treatments: ServiceOrder[];
}
```

---

## Routing (Wayfinder)

- Import from `@/routes` for named routes, `@/actions` for controller invocables
- Usage: `routeFunction({...}).url` to get URL string

```tsx
import { home, counter, counterSelectDepartment } from '@/routes';

const url = counterSelectDepartment({
    pYear: selectedPatient.year,
    pMonth: selectedPatient.month,
    number: selectedPatient.number,
}).url;
```

---

## Styling

- Tailwind CSS v4 utility classes
- Use `clsx` for conditional classes (not `classnames`)
- Use `cn()` utility from `@/lib/utils` (shadcn pattern)

```tsx
import { clsx } from 'clsx';

<div className={clsx('flex gap-4', isActive && 'bg-blue-500')} />
```

---

## Common Patterns

- **Props destructuring:** Always destructure with types via `usePage<T>().props`
- **Conditional rendering:** Ternary or logical operators, not if/else blocks
- **Breadcrumbs:** Import `BreadcrumbItem` from `@/types`, pass array to `AppLayout`
- **Currency formatting:** Use `Currency` component or `formatCurrency()` utility
