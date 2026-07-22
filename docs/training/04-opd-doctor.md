# OPD Doctor Training Guide

## Who This Guide Is For
Outpatient Department (OPD) Doctors use the OPD portal to manage their daily patient queue, write treatment records, and issue prescriptions.

## Your Portal
**URL:** `/OPD`

---

## Understanding Your Dashboard

When you open `/OPD` you see:

| Section | Description |
|---------|-------------|
| **Stats Bar** | Today's count of Waiting / In Progress / Treated / Total patients |
| **Search Box** | Find a patient by SO number or PS number |
| **Queue Table** | All OPD service orders assigned to you today |

Each row in the queue shows:
- Patient name and PS number
- Service requested
- Age / Gender
- Current status (Waiting / In Progress / Treated)

---

## Daily Workflow

### 1. Check Your Queue
Open `/OPD`. The queue shows all patients assigned to you today, ordered by status (In Progress first, then Waiting, then Treated).

The queue **auto-refreshes every 30 seconds** — no need to reload the page.

### 2. Call a Patient
Click on a patient row → the patient detail page opens.  
Click **Call Patient** button → the patient's status changes to **In Progress** (the receptionist sees this on their screen).

### 3. Record the Treatment

On the patient detail page you have sections:

#### Chief Complaint
- Enter the patient's main complaint in their own words.

#### History of Present Illness (HPI)
- Enter relevant medical history for this visit.

#### Diagnosis (ICD-10)
- Click the **ICD-10 Code** field and start typing:
  - Type the code (e.g. `J06`) to search by code prefix.
  - Type a condition (e.g. `fever`) to search by description.
- Select from the dropdown — the **Diagnosis** text fills automatically.
- You can override the diagnosis text if needed.

#### Treatment Plan
- Enter your treatment plan, medications instructions, advice.

#### Prescriptions
Each row has: Drug Name, Dose, Frequency, Duration, Route, Instructions.
- Click **+ Add row** to add more medications.
- Click **✕** on a row to remove it.

### 4. Save or Finalize

| Button | Effect |
|--------|--------|
| **Save Draft** | Saves the record but keeps it editable. Status → In Progress. |
| **Finalize Record** | Locks the record permanently. Status → Treated. Returns you to the queue. |

> **Important:** Once finalized, a treatment record **cannot be edited**. Only finalize when you are sure the consultation is complete.

---

## Searching for a Patient

In the search box at the top of `/OPD`:
- Type the **SO number** (e.g. `OPD/2024/03/0001/OPD/00000001`) and press Enter.
- Type the **PS number** (e.g. `PS/2024/03/0001`) and press Enter.
- The system redirects you to the first matching open OPD order for that patient.

---

## Viewing Previous Visits

On the patient detail page, the right sidebar shows the patient's **Previous Visits** (up to 5 recent OPD orders). Each shows:
- Order number and date
- Diagnosis text from that visit

---

## Status Flow

```
Waiting (open) → In Progress → Treated
```

- **Waiting:** Patient registered and paid at the counter but not yet called.
- **In Progress:** You clicked "Call Patient" — patient is in your room.
- **Treated:** Record finalized — patient has been seen.

---

## Tips
- Always use the **ICD-10 picker** for diagnosis — this is required for PHC compliance reports.
- Save your work as a draft periodically for long consultations.
- The prescription table prints as part of the patient receipt (ask the receptionist to reprint if needed).
- If a patient's order does not appear in your queue, ask the receptionist to check the service order assignment.
