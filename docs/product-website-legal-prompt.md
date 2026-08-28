# Website Build Prompt — Hospital OS (Terms & Conditions + Privacy Policy)

> **What this document is:** a self-contained briefing for an AI coding agent working in a **separate repository** — the one building the marketing/resell website for this software product (see the companion document `product-website-prompt.md` for the main site brief). This document narrows in on two specific pages: **Terms & Conditions** and **Privacy Policy** for the *marketing/resell website itself* — not the hospital-facing software product.
>
> **How to use it:** paste this whole document into the other repo's AI session alongside `product-website-prompt.md`. Everything under "Verified Facts" is confirmed against the actual product and can inform legal copy. Everything under "Needs Your Input" is a placeholder only the business owner (or their lawyer) can fill in — **do not invent legal values**. Terms & Conditions and Privacy Policy pages carry real legal weight; when in doubt, generate a clearly-marked placeholder rather than a plausible-sounding guess.

---

## 1. Critical Scope Distinction — read this first

This is a **self-hosted** product (see `product-website-prompt.md` §1). That changes what these two pages need to cover, compared to a typical SaaS company:

- **The marketing website** (the thing being built) is a lead-generation/informational site. Its own Privacy Policy only needs to cover data the *website* collects — visitors, demo-request form submissions, cookies/analytics.
- **The software product itself** runs on each hospital's own infrastructure. The vendor does **not** host, process, or have access to any hospital's patient data by default — that data lives on the hospital's own Docker deployment, under the hospital's own control.
- **Do not conflate the two.** Do not write a Privacy Policy that implies the vendor collects, stores, or processes patient health information (PHI) through the website or through normal operation of the software. If the business wants to describe how the *software* protects patient data (for the hospital's own compliance obligations to their patients), that belongs in a separate **Data Processing / Product Privacy** document or Trust Center — not the website's own visitor-facing Privacy Policy. Flag this as a placeholder/future addition (§6) rather than drafting it from assumption.
- Terms & Conditions for the *website* (browsing terms, IP, disclaimers) are also distinct from a **software license/EULA or Master Services Agreement**, which governs the actual commercial relationship with a purchasing hospital. Draft the website T&C narrowly; flag the license/EULA as a placeholder for the business owner's legal counsel.

---

## 2. Verified Facts (safe to use as source material)

Pulled from the actual product and already-vetted language in `product-website-prompt.md` §3 — reuse rather than re-deriving:

- Deployment model: self-hosted via Docker, on the hospital's own infrastructure. No mandatory multi-tenant cloud; hospital data does not transit or reside on vendor-controlled servers as part of normal product operation.
- The product implements (in the software, for the hospital's own compliance): role-based access control, full audit trails, encryption of sensitive patient fields at rest, immutable/versioned medical and financial records, no hard deletes on patient/financial data (soft-delete/archival only), automated backups, and anomaly/breach-pattern flagging.
- Positioning language already approved: **"HIPAA-inspired"** (not HIPAA-certified — HIPAA certification is not a real, awardable status), and **"built to align with Punjab Healthcare Commission (PHC) guidelines"** (not an official PHC endorsement or certification). These same guardrails apply to any compliance language on the legal pages.
- Primary market: Pakistani hospitals/clinics, with architecture intended to generalize to other markets.
- The marketing site's only currently-planned data-collecting surface (per `product-website-prompt.md` §5, item 9) is a **"Request a Demo / Contact Sales"** lead-gen form collecting: name, hospital/organization, email, phone, and message. Treat this as the baseline for what the Privacy Policy needs to disclose — adjust if the building agent adds other forms (newsletter signup, careers, etc.).
- No self-serve signup flow, no user accounts, no payment collection on the marketing site itself (this is a sold/licensed product, not self-serve SaaS — confirmed in `product-website-prompt.md` §5 item 1).

---

## 3. Privacy Policy — content & structure guidance

Recommended sections, scoped to the marketing website only:

1. **Who this policy covers** — the operator of the website (legal entity name — placeholder, see §6), and a clear statement that this policy governs the website only, not the self-hosted software product deployed at customer hospitals.
2. **What data is collected**
   - Demo/contact form submissions: name, hospital/organization name, email, phone, message.
   - Standard technical data collected passively by any website: IP address, browser/device type, pages visited, referrer — *only if* analytics tooling is actually added by the building agent (don't claim collection that doesn't exist).
   - Cookies — only describe what's actually implemented (e.g., if no analytics/cookies are added, say so plainly rather than including boilerplate cookie language).
3. **How data is used** — responding to demo/sales inquiries, internal record-keeping of leads, basic website analytics if implemented. No secondary use, no selling of data to third parties (standard, safe commitment for a B2B lead-gen form).
4. **Third-party services** — list only tools actually integrated (e.g., an email service to receive form submissions, an analytics provider if added). Leave as placeholder list until the building agent knows the actual stack.
5. **Data retention** — placeholder; do not invent a specific retention period.
6. **Data security** — general commitment (encrypted transmission via HTTPS, access limited to authorized staff). Do not borrow the *software product's* security claims (encryption at rest, audit logs, etc.) into this section — those apply to the hospital software, not lead form data, unless the vendor's own CRM/storage actually implements them.
7. **User rights** — right to request deletion/correction of submitted contact info, and how to exercise it (placeholder contact — see §6).
8. **Children's data** — standard statement that the site is not directed at children and doesn't knowingly collect data from them (safe boilerplate for a B2B site).
9. **Changes to this policy** — standard "we may update this policy" clause with a last-updated date field (leave as `[Last Updated: TBD]` until publish time).
10. **Contact** — placeholder email/address (§6).
11. **Relationship to the product's own data handling** — a short, clearly-labeled note: *"This policy describes how [Company] handles data submitted through this website. It does not describe how the Hospital OS software processes patient data once deployed by a hospital — that is governed by the hospital's own policies and, where applicable, a separate agreement between [Company] and the licensing hospital."* This sentence is important to avoid accidentally implying HIPAA/PHC obligations the vendor doesn't actually carry for the website.

---

## 4. Terms & Conditions — content & structure guidance

Recommended sections, scoped to website usage (not a software EULA):

1. **Acceptance of terms** — standard "by using this site you agree" framing.
2. **Who operates the site** — legal entity name (placeholder, §6).
3. **Purpose of the site** — informational/marketing; describes the Hospital OS product; facilitates demo/sales inquiries. Not a self-serve product — no account creation, no purchase flow on the site itself.
4. **Intellectual property** — site content, product name, logo, and copy are owned by the operator; no license granted to reproduce site content without permission.
5. **Acceptable use** — standard prohibitions (no scraping for competitive purposes, no attempts to disrupt the site, etc.) — generic, safe boilerplate.
6. **No warranty on website content** — informational content (feature descriptions, compliance posture language) is provided in good faith but the *website* is not a substitute for a signed licensing agreement; actual product capabilities and terms are governed by a separate contract with the purchasing hospital.
7. **Limitation of liability** — standard limitation clause; leave the specific liability cap as placeholder pending legal review.
8. **Links to third-party sites** — standard disclaimer if the site links out anywhere.
9. **Governing law & jurisdiction** — **placeholder, do not invent** (see §6). Given the primary market is Pakistan, this is likely Pakistani law, but do not assert this without the business owner confirming — get explicit confirmation rather than defaulting silently.
10. **Changes to these terms** — standard "may be updated" clause with a `[Last Updated: TBD]` field.
11. **Relationship to product licensing terms** — a short, clearly-labeled note that using this website does not itself grant any license to use the Hospital OS software; software licensing terms are governed by a separate agreement executed with purchasing hospitals (placeholder — see §6).
12. **Contact** — placeholder email/address (§6).

---

## 5. Accuracy guardrails — do not violate these

Same discipline as `product-website-prompt.md` §3, extended to legal copy:

- Never write "HIPAA certified" or "HIPAA compliant" as a certification claim, on either page. Use "HIPAA-inspired practices" only, and only in the context of describing the *software product*, not the website's own data handling.
- Never write "PHC certified" or imply official PHC endorsement. Use "built to align with Punjab Healthcare Commission (PHC) guidelines."
- Never state or imply the website or the vendor has passed a third-party security audit, penetration test, or data-protection certification (ISO 27001, SOC 2, GDPR compliance, etc.) unless the business owner supplies one to cite.
- Never invent a governing-law jurisdiction, a registered company name, a physical address, a DPO/contact email, or a data-retention period. Every one of these must come from the business owner — placeholders only.
- Do not draft clauses implying the website processes or stores patient health information. It doesn't, by the product's own architecture (§1).
- Keep the two pages internally consistent with each other and with `product-website-prompt.md` — same company name placeholder, same tone, same compliance language.

---

## 6. Needs Your Input — do not invent these

The building agent should render these as clearly marked placeholders (e.g. `[Company Legal Name — TBD]`) until the business owner supplies real values:

- Legal entity/company name to be named as the site operator (may differ from the "Hospital OS" product name — see `product-website-prompt.md` §6).
- Registered business address / jurisdiction of incorporation.
- Governing law and dispute-resolution jurisdiction for the Terms & Conditions.
- Privacy/legal contact email or address (for both policies' "Contact" sections and for data-subject requests).
- Whether the marketing site will actually use analytics/cookies, and which provider(s) — this determines what the Privacy Policy's cookie/third-party sections should say.
- Data retention period for demo/contact form submissions.
- Whether a separate software License Agreement / EULA / Master Services Agreement already exists to reference/link from the Terms & Conditions, or whether that's still pending legal drafting.
- Whether a separate Data Processing Agreement or product-level "Trust Center" page describing the software's patient-data handling is planned (recommended given this is healthcare software, but out of scope for these two website pages — see §1).
- Any actual third-party compliance certifications completed, if applicable (do not assume none — confirm).
- Effective/last-updated dates for both documents at publish time.
