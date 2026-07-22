import { clsx } from 'clsx';
import { useState } from 'react';

export interface ToothTreatment {
    procedure: string;
    notes?: string;
}

export type DentalChartValue = Record<string, ToothTreatment>;

interface DentalChartProps {
    value: DentalChartValue;
    onChange: (teeth: DentalChartValue) => void;
    disabled?: boolean;
    className?: string;
}

const PROCEDURES = [
    'Extraction',
    'Filling',
    'Root Canal',
    'Crown',
    'Cleaning / Scaling',
    'Sealant',
    'Bridge',
    'Implant',
    'Other',
];

// FDI/ISO tooth numbering, laid out as viewed facing the patient.
const UPPER_ROW = ['18', '17', '16', '15', '14', '13', '12', '11', '21', '22', '23', '24', '25', '26', '27', '28'];
const LOWER_ROW = ['48', '47', '46', '45', '44', '43', '42', '41', '31', '32', '33', '34', '35', '36', '37', '38'];

export default function DentalChart({ value, onChange, disabled = false, className }: DentalChartProps) {
    const [activeTooth, setActiveTooth] = useState<string | null>(null);

    const treatedCount = Object.keys(value).length;

    const toggleTooth = (tooth: string) => {
        if (disabled) return;
        setActiveTooth((current) => (current === tooth ? null : tooth));
    };

    const updateTooth = (tooth: string, patch: Partial<ToothTreatment>) => {
        const existing = value[tooth] ?? { procedure: '', notes: '' };
        onChange({ ...value, [tooth]: { ...existing, ...patch } });
    };

    const removeTooth = (tooth: string) => {
        const next = { ...value };
        delete next[tooth];
        onChange(next);
        setActiveTooth(null);
    };

    const renderRow = (teeth: string[], label: string) => (
        <div>
            <p className="mb-1 text-[10px] font-medium tracking-wide text-slate-400 uppercase">{label}</p>
            <div className="flex flex-wrap gap-1">
                {teeth.map((tooth) => {
                    const treated = Boolean(value[tooth]?.procedure);
                    const isActive = activeTooth === tooth;
                    return (
                        <button
                            key={tooth}
                            type="button"
                            disabled={disabled}
                            onClick={() => toggleTooth(tooth)}
                            title={value[tooth]?.procedure}
                            className={clsx(
                                'flex h-9 w-9 flex-col items-center justify-center rounded-lg border text-[11px] font-semibold transition-colors',
                                treated
                                    ? 'border-teal-400 bg-teal-100 text-teal-800'
                                    : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50',
                                isActive && 'ring-2 ring-teal-400 ring-offset-1',
                                disabled && 'cursor-not-allowed opacity-60',
                            )}
                        >
                            {tooth}
                        </button>
                    );
                })}
            </div>
        </div>
    );

    return (
        <div className={clsx('space-y-3', className)}>
            <div className="flex items-center justify-between">
                <p className="text-xs text-slate-500">
                    Click a tooth to record a procedure. {treatedCount > 0 && <span className="font-medium text-teal-700">{treatedCount} tooth/teeth recorded.</span>}
                </p>
            </div>

            <div className="space-y-3 rounded-xl border border-slate-200 bg-slate-50/60 p-3">
                {renderRow(UPPER_ROW, 'Upper Arch (18–28)')}
                {renderRow(LOWER_ROW, 'Lower Arch (48–38)')}
            </div>

            {activeTooth && (
                <div className="rounded-xl border border-teal-200 bg-teal-50/50 p-3">
                    <div className="mb-2 flex items-center justify-between">
                        <span className="text-sm font-semibold text-slate-800">Tooth {activeTooth}</span>
                        {value[activeTooth] && (
                            <button
                                type="button"
                                onClick={() => removeTooth(activeTooth)}
                                className="text-xs font-medium text-red-600 hover:text-red-800"
                            >
                                Remove
                            </button>
                        )}
                    </div>
                    <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <select
                            disabled={disabled}
                            value={value[activeTooth]?.procedure ?? ''}
                            onChange={(e) => updateTooth(activeTooth, { procedure: e.target.value })}
                            className="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs text-slate-800 focus:border-teal-400 focus:ring-1 focus:ring-teal-300 focus:outline-none"
                        >
                            <option value="">Select procedure…</option>
                            {PROCEDURES.map((p) => (
                                <option key={p} value={p}>
                                    {p}
                                </option>
                            ))}
                        </select>
                        <input
                            disabled={disabled}
                            value={value[activeTooth]?.notes ?? ''}
                            onChange={(e) => updateTooth(activeTooth, { notes: e.target.value })}
                            placeholder="Notes (material, surface, etc.)"
                            className="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs text-slate-800 focus:border-teal-400 focus:ring-1 focus:ring-teal-300 focus:outline-none"
                        />
                    </div>
                </div>
            )}
        </div>
    );
}
