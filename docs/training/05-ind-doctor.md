# Indoor (Inpatient) Doctor Training Guide

## Who This Guide Is For
Indoor / Inpatient Doctors manage admitted patients — from admission through daily rounds to discharge.

## Your Portal
**URL:** `/IND`

---

## Understanding the Indoor Dashboard

The IND dashboard shows:

| Section | Description |
|---------|-------------|
| **Ward Snapshot** | All wards with beds, showing occupied vs available |
| **Stats Bar** | Open / In Progress / Treated / Total for today |
| **Search Box** | Find a patient by service order or PS number |
| **Bed Assignment Modal** | Assign a bed to a patient |

The ward snapshot **auto-refreshes every 45 seconds**.

---

## Patient Admission Workflow

### Step 1 — Patient Arrives
The receptionist registers the patient and creates an Indoor service order. The patient appears in the ward snapshot with status **Waiting (no bed)**.

### Step 2 — Assign a Bed
1. Search for the patient by PS number in the search box.
2. In the search results, click the patient row.
3. A **Bed Assignment** modal appears showing available beds by ward and room.
4. Select the appropriate bed.
5. Click **Assign Bed**.
6. The patient's status moves to **In Progress** and the bed is marked as occupied.

### Step 3 — Write the Admission Note
1. Click on the patient row in the dashboard.
2. On the patient detail page, fill in:
   - **Chief Complaint** — reason for admission.
   - **History of Present Illness** — detailed history.
   - **Examination Findings** — physical exam results.
   - **Diagnosis** — use the ICD-10 picker.
   - **Treatment Plan** — initial management plan.
   - **Prescriptions** — admission medications.
3. Click **Save Draft** (do not finalize on admission — you will need to update this during rounds).

---

## Daily Rounds Workflow

1. Open `/IND` — your admitted patients appear in the ward snapshot.
2. Click on a patient → their treatment record opens.
3. Update:
   - **Examination Findings** — today's exam.
   - **Treatment Plan** — revisions.
   - **Prescriptions** — medication changes.
4. Click **Save Draft** after rounds.

---

## Discharge Workflow

### Step 1 — Write Discharge Note
1. Open the patient's record.
2. Update treatment plan with discharge instructions.
3. Set **Outcome** (Improved / Discharged / Referred / etc.).
4. Click **Finalize Record** — this locks the treatment record.

### Step 2 — Discharge Patient
On the patient detail page, click the **Discharge** button.
- The bed is freed automatically.
- The service order status moves to **Closed**.

> **Important:** Finalize the treatment record before discharging. A finalized record cannot be edited.

---

## Ward & Bed Reference

| Term | Meaning |
|------|---------|
| **Ward** | A clinical unit (e.g. Medical Ward, Surgical Ward) |
| **Room** | A room within a ward |
| **Bed** | An individual bed within a room |

Ward details are managed by the administrator under `/admin` → Indoor → Wards / Rooms / Beds.

---

## Status Flow

```
Waiting → In Progress (bed assigned) → Closed (discharged)
```

---

## Searching for an Admitted Patient

In the search box:
- Type **PS number** → shows all open IND orders for that patient.
- Type **SO number** → opens that specific service order.

---

## Tips
- Always assign a bed as early as possible — it updates the ward capacity for the admin dashboard.
- Use ICD-10 codes on admission. The same code should remain consistent through discharge unless the diagnosis changes.
- If a patient is moved to another bed or ward, the administrator can update the bed assignment in the admin panel.
- Do not finalize the record until the patient is ready for discharge — you cannot edit it afterwards.
