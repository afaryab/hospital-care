import React from 'react';

export default function CreatePatientPolicy({ className }: { className?: string }) {
    return <div className={className}>
        <div className="w-full mt-6 p-6">
            <div className="flex items-start gap-4">
                <div>
                    <h3 className="text-lg font-bold text-red-800">
                        Regulatory Warning – Mandatory Compliance
                    </h3>

                    <p className="mt-2 text-sm text-red-900 leading-relaxed">
                        Failure to comply with the Hospital Patient Registration Policy and mandatory demographic
                        data requirements constitutes a violation of institutional governance standards and may
                        amount to non-compliance under the Punjab Healthcare Commission regulatory framework.
                    </p>

                    <p className="mt-3 text-sm text-red-900 leading-relaxed">
                        Incomplete, false, or deliberately omitted patient demographic information
                        (including Name, Contact Number, Age, or Gender) may:
                    </p>

                    <ul className="mt-3 space-y-2 text-sm text-red-900">
                        <li className="flex gap-2">
                            <span className="mt-1 h-2 w-2 rounded-full bg-red-700"></span>
                            <span>Compromise patient safety and continuity of care</span>
                        </li>
                        <li className="flex gap-2">
                            <span className="mt-1 h-2 w-2 rounded-full bg-red-700"></span>
                            <span>Invalidate medico-legal documentation</span>
                        </li>
                        <li className="flex gap-2">
                            <span className="mt-1 h-2 w-2 rounded-full bg-red-700"></span>
                            <span>Expose the healthcare establishment to regulatory penalties</span>
                        </li>
                        <li className="flex gap-2">
                            <span className="mt-1 h-2 w-2 rounded-full bg-red-700"></span>
                            <span>Result in inspection objections, fines, suspension, or sealing of services</span>
                        </li>
                    </ul>

                    <div className="mt-4 p-3 bg-white border border-red-200 rounded-lg">
                        <p className="text-xs text-red-800 font-semibold">
                            All staff are legally and professionally obligated to ensure complete and accurate
                            patient registration data at the point of entry. Non-compliance may result in
                            disciplinary action, regulatory reporting, and institutional liability.
                        </p>
                    </div>

                    <p className="mt-3 text-xs text-red-700">
                        This warning is issued in alignment with institutional governance policies and
                        regulatory standards enforced under the Punjab Healthcare Commission framework.
                    </p>
                </div>
            </div>
        </div>

        <div className="w-full p-6">
            <div className="flex items-start justify-between gap-4">
                <div>
                    <h2 className="text-xl font-semibold text-slate-900">Patient Registration – Mandatory Minimum Dataset Policy</h2>
                    <p className="mt-1 text-sm text-slate-600">
                        This policy defines the minimum patient registration data to support safe care, traceability, continuity of care,
                        and regulatory inspection readiness under Punjab Healthcare Commission (PHC) governance.
                    </p>
                </div>
                <span className="shrink-0 inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                    Effective: Immediate
                </span>
            </div>

            <div className="mt-6 grid gap-4">
                <div className="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <h3 className="text-sm font-semibold text-slate-900">1) Mandatory Fields (System-Enforced)</h3>
                    <p className="mt-2 text-sm text-slate-700">
                        The Hospital Information System (HIS/EMR) <span className="font-semibold">must</span> capture the following fields at first contact (OPD/IPD/ER):
                    </p>
                <ul className="mt-3 space-y-2 text-sm text-slate-800">
                    <li className="flex gap-2">
                        <span className="mt-1 h-2 w-2 rounded-full bg-slate-900"></span>
                        <span><span className="font-semibold">Name</span> (Required)</span>
                    </li>
                    <li className="flex gap-2">
                        <span className="mt-1 h-2 w-2 rounded-full bg-slate-900"></span>
                        <span>
                            <span className="font-semibold">Contact Number</span> (Required) —
                            for follow-up, results communication, continuity of care, complaint resolution, and patient traceability.
                        </span>
                    </li>
                    <li className="flex gap-2">
                        <span className="mt-1 h-2 w-2 rounded-full bg-slate-900"></span>
                        <span><span className="font-semibold">Age</span> (Required) — or Date of Birth where available; required for safe clinical decisions and lawful consent handling.</span>
                    </li>
                    <li className="flex gap-2">
                        <span className="mt-1 h-2 w-2 rounded-full bg-slate-900"></span>
                        <span><span className="font-semibold">Gender</span> (Required) — required for accurate clinical assessment, documentation integrity, and reporting.</span>
                    </li>
                </ul>

                <div className="mt-4 rounded-lg bg-white border border-slate-200 p-3">
                    <p className="text-xs text-slate-600">
                        <span className="font-semibold text-slate-800">System rule:</span>
                        Registration cannot be completed unless all mandatory fields above are provided.
                    </p>
                </div>
                </div>

                <div className="rounded-xl border border-slate-200 p-4">
                    <h3 className="text-sm font-semibold text-slate-900">2) Optional Field (Collected When Available)</h3>
                    <p className="mt-2 text-sm text-slate-700">
                        <span className="font-semibold">CNIC</span> is marked <span className="font-semibold">Optional</span> at initial OPD registration, but should be collected where applicable
                        (e.g., admissions, procedures, medico-legal scenarios, insurance, or when the patient can provide it).
                    </p>
                    <ul className="mt-3 space-y-2 text-sm text-slate-800">
                        <li className="flex gap-2">
                            <span className="mt-1 h-2 w-2 rounded-full bg-slate-900"></span>
                            <span><span className="font-semibold">CNIC</span> (Optional at OPD; recommended/required by workflow where legally or clinically necessary)</span>
                        </li>
                    </ul>
                </div>

                <div className="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <h3 className="text-sm font-semibold text-amber-900">3) Compliance Basis (Regulatory Rationale)</h3>
                    <p className="mt-2 text-sm text-amber-900/90">
                        The Punjab Healthcare Commission Act empowers PHC to regulate healthcare establishments and enforce minimum standards.
                        PHC’s MSDS framework expects healthcare establishments to maintain complete, accurate, and retrievable patient records that support
                        identification, continuity of care, and accountability during inspections and complaint handling.
                    </p>
                </div>

                <div className="rounded-xl border border-slate-200 p-4">
                    <h3 className="text-sm font-semibold text-slate-900">4) Important Official References (Links)</h3>
                    <div className="mt-3 grid gap-2">
                        <a
                        className="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 hover:bg-slate-50"
                        href="https://os.phc.org.pk/downloads/PHC_Final_Act.pdf"
                        target="_blank"
                        rel="noopener noreferrer"
                        >
                            <div>
                                <div className="text-sm font-semibold text-slate-900">Punjab Healthcare Commission Act, 2010 (Official PDF)</div>
                                <div className="text-xs text-slate-600">Legal authority for PHC regulation & standards enforcement</div>
                            </div>
                            <span className="text-xs font-medium text-slate-700">Open →</span>
                        </a>

                        <a
                        className="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 hover:bg-slate-50"
                        href="https://os.phc.org.pk/downloads.aspx"
                        target="_blank"
                        rel="noopener noreferrer"
                        >
                            <div>
                                <div className="text-sm font-semibold text-slate-900">PHC Official Downloads</div>
                                <div className="text-xs text-slate-600">Access MSDS requests, Patient Rights Charter, and other PHC documents</div>
                            </div>
                            <span className="text-xs font-medium text-slate-700">Open →</span>
                        </a>

                        <a
                        className="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 hover:bg-slate-50"
                        href="https://os.phc.org.pk/catI_HCE.aspx"
                        target="_blank"
                        rel="noopener noreferrer"
                        >
                            <div>
                                <div className="text-sm font-semibold text-slate-900">PHC – Minimum Service Delivery Standards (MSDS) Overview</div>
                                <div className="text-xs text-slate-600">MSDS as mandatory benchmarks for healthcare establishments</div>
                            </div>
                            <span className="text-xs font-medium text-slate-700">Open →</span>
                        </a>
                    </div>

                    <p className="mt-3 text-xs text-slate-500">
                        Note: Use these references in SOPs, audit reports, and internal compliance documentation to justify mandatory registration fields.
                    </p>
                </div>

                <div className="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <h3 className="text-sm font-semibold text-slate-900">Approval & Control</h3>
                    <div className="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                        <div className="rounded-lg bg-white border border-slate-200 p-3">
                            <div className="text-xs text-slate-500">Owner</div>
                            <div className="font-semibold text-slate-900">Hospital Administration</div>
                        </div>
                        <div className="rounded-lg bg-white border border-slate-200 p-3">
                            <div className="text-xs text-slate-500">Applicable Areas</div>
                            <div className="font-semibold text-slate-900">OPD / INP / EMR</div>
                        </div>
                        <div className="rounded-lg bg-white border border-slate-200 p-3">
                            <div className="text-xs text-slate-500">Compliance Review</div>
                            <div className="font-semibold text-slate-900">Quarterly</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>;
}