# Ultrasound Doctor Training Guide

## Who This Guide Is For
Ultrasound Doctors perform and report ultrasound imaging services.

## Your Portal
**URL:** `/ULT`

---

## Dashboard Overview

Shows all ULT service orders assigned to you today. Each row has the patient's name, requested service (e.g. Abdominal Ultrasound), and current status.

---

## Ultrasound Reporting Workflow

### Step 1 — Patient Arrives
The patient's service order is created by the receptionist. It appears in your queue with status **Waiting**.

### Step 2 — Open the Order
1. Click the patient row.
2. Click **Call Patient** → status becomes In Progress.

### Step 3 — Perform and Document

| Field | Ultrasound Guidance |
|-------|---------------------|
| **Chief Complaint** | Referral reason (e.g. "Right upper quadrant pain") |
| **HPI** | Clinical history provided by referring doctor |
| **ICD-10 Code** | Use the relevant code for the finding |
| **Diagnosis** | Ultrasound impression / diagnosis |
| **Treatment Plan** | Findings narrative (use this field for full ultrasound report) |

#### Writing the Ultrasound Report in Treatment Plan
Use the Treatment Plan field as your report body:
```
FINDINGS:
Liver: Normal size and echogenicity. No focal lesion.
Gallbladder: Multiple echogenic foci with posterior acoustic shadowing (calculi).
Kidneys: Normal size bilaterally. No hydronephrosis.

IMPRESSION:
Cholelithiasis (gallstones).
```

### Step 4 — Finalize
Click **Finalize Record** → status moves to Treated → the report is locked.

---

## Common ICD-10 Codes for Ultrasound

| Code | Finding |
|------|---------|
| K80.20 | Gallstones (cholelithiasis) |
| N20.0 | Kidney stone |
| H40.9 | Unspecified (eye US) |
| O34.00 | Obstetric scan, first trimester |
| Z34.90 | Routine antenatal ultrasound |

---

## Tips
- Write the full ultrasound report in the **Treatment Plan** field — this is the only text field large enough for a detailed report.
- Always assign an ICD-10 code to the primary finding — required for departmental statistics.
- Do not finalize until you have reviewed the images and completed your report.
