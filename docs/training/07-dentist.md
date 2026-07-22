# Dentist Training Guide

## Who This Guide Is For
Dentists manage dental department service orders — procedures, extractions, fillings, and follow-up care.

## Your Portal
**URL:** `/DNT`

---

## Dashboard Overview

The Dental dashboard shows all DNT service orders assigned to you today. Patients are ordered by status: In Progress first, then Waiting, then Treated.

---

## Dental Consultation Workflow

### Step 1 — Review the Queue
Open `/DNT`. You see all today's dental orders assigned to you.

### Step 2 — Open a Patient Record
1. Click the patient row.
2. Click **Call Patient** to move status to In Progress.

### Step 3 — Document the Dental Consultation

| Field | Dental-Specific Guidance |
|-------|--------------------------|
| **Chief Complaint** | e.g. "Toothache upper left for 3 days" |
| **HPI** | Pain character, history of previous dental treatment, medications |
| **ICD-10 Code** | Use dental codes: K02.9 (Caries), K05.1 (Gingivitis), K08.1 (Tooth loss), etc. |
| **Diagnosis** | Auto-filled from ICD-10 |
| **Treatment Plan** | Procedure performed (extraction, filling, scaling, RCT, etc.) |
| **Prescriptions** | Antibiotics, analgesics, mouthwash instructions |

### Step 4 — Finalize
- After the procedure is complete, click **Finalize Record**.
- The status moves to Treated.
- Print the prescription for the patient if needed.

---

## Common ICD-10 Codes for Dentistry

| Code | Condition |
|------|-----------|
| K02.9 | Dental caries, unspecified |
| K05.1 | Chronic gingivitis |
| K05.3 | Chronic periodontitis |
| K08.1 | Loss of teeth |
| K08.9 | Disorder of teeth, unspecified |

Search for more by typing the condition in the ICD-10 picker (e.g. "tooth", "gum", "jaw").

---

## Tips
- Document the specific tooth number in the Treatment Plan field (e.g. "Extraction of tooth 46").
- If a follow-up is needed, note it in the Treatment Plan. There is no built-in appointment scheduling — the receptionist manages this.
- Prescription drugs for dental pain: record the generic name, not brand name (PHC requirement).
