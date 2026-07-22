# X-Ray Technician Training Guide

## Who This Guide Is For
X-Ray Technicians perform and report radiology (X-ray) imaging services.

## Your Portal
**URL:** `/XRAY`

---

## Dashboard Overview

Shows all Radiology service orders assigned to you today. Each row displays the patient's name, requested service (e.g. Chest X-Ray, Lumbar Spine), and current status.

---

## X-Ray Reporting Workflow

### Step 1 — Patient Arrives
The patient's service order is created by the receptionist. It appears in your queue with status **Waiting**.

### Step 2 — Open the Order
1. Click the patient row.
2. Click **Call Patient** → status becomes In Progress.

### Step 3 — Perform and Document

| Field | Radiology Guidance |
|-------|---------------------|
| **Chief Complaint** | Referral reason (e.g. "Cough for 3 weeks") |
| **HPI** | Clinical history from the referring doctor |
| **ICD-10 Code** | Use the code for the primary radiological finding |
| **Diagnosis** | Radiological impression (one-line conclusion) |
| **Treatment Plan** | Full radiology report (technique, findings, impression) |

#### Writing the X-Ray Report in Treatment Plan
Use the Treatment Plan field as your full report:
```
TECHNIQUE:
Chest PA view taken in full inspiration.

FINDINGS:
Lungs: Clear. No consolidation, effusion, or pneumothorax.
Cardiac silhouette: Normal size (CTR < 0.5).
Mediastinum: Central. No widening.
Bones: No lytic or sclerotic lesions visualised.

IMPRESSION:
Normal chest radiograph.
```

### Step 4 — Finalize
Click **Finalize Record** → status moves to Treated → the report is locked.

---

## Common ICD-10 Codes for Radiology

| Code | Finding |
|------|---------|
| J18.9 | Pneumonia, unspecified |
| J93.9 | Pneumothorax, unspecified |
| J90 | Pleural effusion |
| M54.5 | Low back pain (Lumbar spine X-ray) |
| S52.501A | Fracture of radius (wrist X-ray) |
| Z87.891 | Personal history of fracture |

---

## Tips
- Write the full report in the **Treatment Plan** field — this is the only field large enough for a structured radiology report.
- Always assign an ICD-10 code to the primary finding — required for departmental statistics.
- Do not finalize until images are reviewed and the report is complete.
- If the image quality is inadequate, document this in the Treatment Plan before finalizing.
