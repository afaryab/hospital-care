# Emergency Doctor Training Guide

## Who This Guide Is For
Emergency Doctors manage emergency department service orders — triage, stabilisation, intervention, and handoff.

## Your Portal
**URL:** `/EMG`

---

## Dashboard Overview

The Emergency dashboard shows all EMG service orders assigned to you today:
- **Waiting** — patient registered, not yet seen
- **In Progress** — you clicked "Call Patient"
- **Treated** — record finalized

> The queue is server-rendered on page load. Refresh the page (`F5`) to see new arrivals.

---

## Emergency Consultation Workflow

### Step 1 — Patient Arrives
The receptionist registers the patient (if stable) and creates an Emergency service order. The order appears in your queue.

For critically ill patients the receptionist may ask you to create the record later — treat the patient first.

### Step 2 — Open the Patient Record
1. Click the patient row in your queue.
2. Click **Call Patient** → status moves to In Progress.

### Step 3 — Document the Emergency

| Field | What to Enter |
|-------|--------------|
| **Chief Complaint** | Presenting complaint (e.g. "Chest pain for 2 hours") |
| **HPI** | History, onset, severity, associated symptoms |
| **Examination Findings** | Vitals, clinical exam findings |
| **ICD-10 Code** | Type the condition to search (e.g. "chest pain" → R07.9) |
| **Diagnosis** | Auto-filled from ICD-10 or type manually |
| **Treatment Plan** | Interventions performed, medications given, instructions |
| **Prescriptions** | Medications prescribed on discharge |

### Step 4 — Finalize or Refer
- If the patient is stable and discharged: click **Finalize Record** → status becomes Treated.
- If the patient needs admission: inform the receptionist to create an Indoor service order and assign a bed.

---

## Searching for a Patient

Search box at the top:
- Enter SO number or PS number → redirects to the matching emergency order.

---

## Tips
- Document everything — emergency records are medico-legally important and cannot be edited after finalization.
- Use ICD-10 codes for every encounter — required for PHC compliance.
- If a patient's status should be "Referred" or "Deceased", set this in the **Outcome** field before finalizing.
- Triage priority is managed by the receptionist through the service order creation order.
