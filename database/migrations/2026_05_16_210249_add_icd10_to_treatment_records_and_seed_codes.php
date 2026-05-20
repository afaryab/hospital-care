<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Seed ICD-10 codes ────────────────────────────────────────────
        // ~150 codes covering the most common presentations in a Pakistani
        // general hospital. insertOrIgnore lets this run safely on re-deploy.
        DB::table('icd10_codes')->insertOrIgnore($this->codes());

        // ── 2. Add icd10_code_id FK to treatment_records ───────────────────
        Schema::table('treatment_records', function (Blueprint $table) {
            $table->foreignId('icd10_code_id')
                ->nullable()
                ->after('diagnosis_code')
                ->constrained('icd10_codes')
                ->nullOnDelete();
        });

        // ── 3. Backfill: link existing string diagnosis_code rows ──────────
        // Where treatment_records.diagnosis_code matches an icd10_codes.code,
        // populate the new FK so historical data is not orphaned. A correlated
        // subquery keeps this portable across MySQL and SQLite (UPDATE...JOIN
        // is MySQL-only and breaks the in-memory SQLite test database).
        DB::table('treatment_records')
            ->whereNull('icd10_code_id')
            ->whereNotNull('diagnosis_code')
            ->where('diagnosis_code', '!=', '')
            ->update([
                'icd10_code_id' => DB::raw(
                    '(SELECT id FROM icd10_codes WHERE icd10_codes.code = treatment_records.diagnosis_code LIMIT 1)'
                ),
            ]);
    }

    public function down(): void
    {
        Schema::table('treatment_records', function (Blueprint $table) {
            $table->dropForeign(['icd10_code_id']);
            $table->dropColumn('icd10_code_id');
        });

        DB::table('icd10_codes')->whereIn('code', array_column($this->codes(), 'code'))->delete();
    }

    /** @return array<int, array<string, mixed>> */
    private function codes(): array
    {
        $now = now();

        return array_map(fn ($c) => [...$c, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now], [

            // ── Infectious & Parasitic (A & B) ─────────────────────────────
            ['code' => 'A01.0',  'category' => 'Infectious',    'description' => 'Typhoid fever'],
            ['code' => 'A09',    'category' => 'Infectious',    'description' => 'Diarrhoea and gastroenteritis of infectious origin'],
            ['code' => 'A15.0',  'category' => 'Respiratory',   'description' => 'Tuberculosis of lung, confirmed by sputum microscopy'],
            ['code' => 'A16.2',  'category' => 'Respiratory',   'description' => 'Tuberculosis of lung, without bacteriological confirmation'],
            ['code' => 'A33',    'category' => 'Infectious',    'description' => 'Neonatal tetanus'],
            ['code' => 'A34',    'category' => 'Infectious',    'description' => 'Obstetrical tetanus'],
            ['code' => 'A36.0',  'category' => 'Infectious',    'description' => 'Pharyngeal diphtheria'],
            ['code' => 'A90',    'category' => 'Infectious',    'description' => 'Dengue fever'],
            ['code' => 'A91',    'category' => 'Infectious',    'description' => 'Dengue haemorrhagic fever'],
            ['code' => 'B15.9',  'category' => 'Infectious',    'description' => 'Acute hepatitis A without hepatic coma'],
            ['code' => 'B16.9',  'category' => 'Infectious',    'description' => 'Acute hepatitis B without delta-agent'],
            ['code' => 'B17.1',  'category' => 'Infectious',    'description' => 'Acute hepatitis C'],
            ['code' => 'B18.1',  'category' => 'Infectious',    'description' => 'Chronic viral hepatitis B'],
            ['code' => 'B18.2',  'category' => 'Infectious',    'description' => 'Chronic viral hepatitis C'],
            ['code' => 'B50.9',  'category' => 'Infectious',    'description' => 'Plasmodium falciparum malaria, unspecified'],
            ['code' => 'B54',    'category' => 'Infectious',    'description' => 'Unspecified malaria'],
            ['code' => 'B05.9',  'category' => 'Infectious',    'description' => 'Measles without complication'],
            ['code' => 'B26.9',  'category' => 'Infectious',    'description' => 'Mumps without complication'],

            // ── Neoplasms (C & D) ──────────────────────────────────────────
            ['code' => 'C50.9',  'category' => 'Neoplasm',      'description' => 'Malignant neoplasm of breast, unspecified'],
            ['code' => 'C61',    'category' => 'Neoplasm',      'description' => 'Malignant neoplasm of prostate'],
            ['code' => 'C53.9',  'category' => 'Neoplasm',      'description' => 'Malignant neoplasm of cervix uteri, unspecified'],
            ['code' => 'C34.9',  'category' => 'Neoplasm',      'description' => 'Malignant neoplasm of bronchus and lung, unspecified'],
            ['code' => 'D50.9',  'category' => 'Blood',         'description' => 'Iron deficiency anaemia, unspecified'],
            ['code' => 'D64.9',  'category' => 'Blood',         'description' => 'Anaemia, unspecified'],

            // ── Endocrine & Metabolic (E) ──────────────────────────────────
            ['code' => 'E03.9',  'category' => 'Endocrine',     'description' => 'Hypothyroidism, unspecified'],
            ['code' => 'E05.9',  'category' => 'Endocrine',     'description' => 'Thyrotoxicosis, unspecified'],
            ['code' => 'E10.9',  'category' => 'Endocrine',     'description' => 'Type 1 diabetes mellitus without complications'],
            ['code' => 'E11.9',  'category' => 'Endocrine',     'description' => 'Type 2 diabetes mellitus without complications'],
            ['code' => 'E11.65', 'category' => 'Endocrine',     'description' => 'Type 2 diabetes mellitus with hyperglycaemia'],
            ['code' => 'E14.9',  'category' => 'Endocrine',     'description' => 'Unspecified diabetes mellitus without complications'],
            ['code' => 'E46',    'category' => 'Endocrine',     'description' => 'Unspecified protein-energy malnutrition'],
            ['code' => 'E66.9',  'category' => 'Endocrine',     'description' => 'Obesity, unspecified'],

            // ── Mental & Behavioural (F) ────────────────────────────────────
            ['code' => 'F10.2',  'category' => 'Mental',        'description' => 'Mental and behavioural disorders due to alcohol — dependence syndrome'],
            ['code' => 'F20.9',  'category' => 'Mental',        'description' => 'Schizophrenia, unspecified'],
            ['code' => 'F32.9',  'category' => 'Mental',        'description' => 'Depressive episode, unspecified'],
            ['code' => 'F41.1',  'category' => 'Mental',        'description' => 'Generalised anxiety disorder'],
            ['code' => 'F51.0',  'category' => 'Mental',        'description' => 'Nonorganic insomnia'],

            // ── Nervous System (G) ─────────────────────────────────────────
            ['code' => 'G35',    'category' => 'Neurology',     'description' => 'Multiple sclerosis'],
            ['code' => 'G40.9',  'category' => 'Neurology',     'description' => 'Epilepsy, unspecified'],
            ['code' => 'G43.9',  'category' => 'Neurology',     'description' => 'Migraine, unspecified'],
            ['code' => 'G89.29', 'category' => 'Neurology',     'description' => 'Other chronic pain'],
            ['code' => 'G62.9',  'category' => 'Neurology',     'description' => 'Polyneuropathy, unspecified'],

            // ── Eye (H00-H59) ──────────────────────────────────────────────
            ['code' => 'H10.9',  'category' => 'Ophthalmology', 'description' => 'Unspecified conjunctivitis'],
            ['code' => 'H26.9',  'category' => 'Ophthalmology', 'description' => 'Unspecified cataract'],
            ['code' => 'H40.9',  'category' => 'Ophthalmology', 'description' => 'Unspecified glaucoma'],
            ['code' => 'H52.4',  'category' => 'Ophthalmology', 'description' => 'Presbyopia'],

            // ── Ear (H60-H95) ──────────────────────────────────────────────
            ['code' => 'H60.9',  'category' => 'ENT',           'description' => 'Otitis externa, unspecified'],
            ['code' => 'H66.9',  'category' => 'ENT',           'description' => 'Otitis media, unspecified'],
            ['code' => 'H81.1',  'category' => 'ENT',           'description' => 'Benign paroxysmal vertigo'],
            ['code' => 'H91.9',  'category' => 'ENT',           'description' => 'Hearing loss, unspecified'],

            // ── Circulatory System (I) ─────────────────────────────────────
            ['code' => 'I10',    'category' => 'Cardiovascular', 'description' => 'Essential (primary) hypertension'],
            ['code' => 'I11.0',  'category' => 'Cardiovascular', 'description' => 'Hypertensive heart disease with heart failure'],
            ['code' => 'I20.9',  'category' => 'Cardiovascular', 'description' => 'Angina pectoris, unspecified'],
            ['code' => 'I21.9',  'category' => 'Cardiovascular', 'description' => 'Acute myocardial infarction, unspecified'],
            ['code' => 'I25.10', 'category' => 'Cardiovascular', 'description' => 'Atherosclerotic heart disease of native coronary artery without angina pectoris'],
            ['code' => 'I48',    'category' => 'Cardiovascular', 'description' => 'Atrial fibrillation and flutter'],
            ['code' => 'I50.9',  'category' => 'Cardiovascular', 'description' => 'Heart failure, unspecified'],
            ['code' => 'I63.9',  'category' => 'Cardiovascular', 'description' => 'Cerebral infarction, unspecified'],
            ['code' => 'I64',    'category' => 'Cardiovascular', 'description' => 'Stroke, not specified as haemorrhage or infarction'],
            ['code' => 'I70.9',  'category' => 'Cardiovascular', 'description' => 'Generalised and unspecified atherosclerosis'],

            // ── Respiratory System (J) ─────────────────────────────────────
            ['code' => 'J00',    'category' => 'Respiratory',   'description' => 'Acute nasopharyngitis (common cold)'],
            ['code' => 'J02.9',  'category' => 'Respiratory',   'description' => 'Acute pharyngitis, unspecified'],
            ['code' => 'J03.9',  'category' => 'Respiratory',   'description' => 'Acute tonsillitis, unspecified'],
            ['code' => 'J06.9',  'category' => 'Respiratory',   'description' => 'Acute upper respiratory infection, unspecified'],
            ['code' => 'J11.1',  'category' => 'Respiratory',   'description' => 'Influenza with other respiratory manifestations'],
            ['code' => 'J18.9',  'category' => 'Respiratory',   'description' => 'Pneumonia, unspecified organism'],
            ['code' => 'J20.9',  'category' => 'Respiratory',   'description' => 'Acute bronchitis, unspecified'],
            ['code' => 'J21.9',  'category' => 'Respiratory',   'description' => 'Acute bronchiolitis, unspecified'],
            ['code' => 'J22',    'category' => 'Respiratory',   'description' => 'Unspecified acute lower respiratory infection'],
            ['code' => 'J30.1',  'category' => 'Respiratory',   'description' => 'Allergic rhinitis due to pollen'],
            ['code' => 'J30.4',  'category' => 'Respiratory',   'description' => 'Allergic rhinitis, unspecified'],
            ['code' => 'J44.1',  'category' => 'Respiratory',   'description' => 'Chronic obstructive pulmonary disease with acute exacerbation'],
            ['code' => 'J45.9',  'category' => 'Respiratory',   'description' => 'Asthma, unspecified'],

            // ── Digestive System (K) ───────────────────────────────────────
            ['code' => 'K02.9',  'category' => 'Dental',        'description' => 'Dental caries, unspecified'],
            ['code' => 'K05.1',  'category' => 'Dental',        'description' => 'Chronic gingivitis'],
            ['code' => 'K05.3',  'category' => 'Dental',        'description' => 'Chronic periodontitis'],
            ['code' => 'K08.1',  'category' => 'Dental',        'description' => 'Loss of teeth due to accident, extraction or local periodontal disease'],
            ['code' => 'K08.9',  'category' => 'Dental',        'description' => 'Disorder of teeth, unspecified'],
            ['code' => 'K21.0',  'category' => 'Gastroenterology', 'description' => 'Gastro-oesophageal reflux disease with oesophagitis'],
            ['code' => 'K21.9',  'category' => 'Gastroenterology', 'description' => 'Gastro-oesophageal reflux disease without oesophagitis'],
            ['code' => 'K25.9',  'category' => 'Gastroenterology', 'description' => 'Gastric ulcer, unspecified'],
            ['code' => 'K27.9',  'category' => 'Gastroenterology', 'description' => 'Peptic ulcer, site unspecified'],
            ['code' => 'K29.7',  'category' => 'Gastroenterology', 'description' => 'Gastritis, unspecified'],
            ['code' => 'K35.2',  'category' => 'Surgery',       'description' => 'Acute appendicitis with generalised peritonitis'],
            ['code' => 'K37',    'category' => 'Surgery',       'description' => 'Unspecified appendicitis'],
            ['code' => 'K40.90', 'category' => 'Surgery',       'description' => 'Unilateral inguinal hernia, without obstruction or gangrene'],
            ['code' => 'K57.30', 'category' => 'Gastroenterology', 'description' => 'Diverticular disease of large intestine without perforation'],
            ['code' => 'K70.9',  'category' => 'Gastroenterology', 'description' => 'Alcoholic liver disease, unspecified'],
            ['code' => 'K74.6',  'category' => 'Gastroenterology', 'description' => 'Other and unspecified cirrhosis of liver'],
            ['code' => 'K80.20', 'category' => 'Gastroenterology', 'description' => 'Calculus of gallbladder without cholecystitis, without obstruction'],
            ['code' => 'K92.1',  'category' => 'Gastroenterology', 'description' => 'Melaena'],

            // ── Skin (L) ───────────────────────────────────────────────────
            ['code' => 'L20.9',  'category' => 'Dermatology',   'description' => 'Atopic dermatitis, unspecified'],
            ['code' => 'L23.9',  'category' => 'Dermatology',   'description' => 'Allergic contact dermatitis, unspecified cause'],
            ['code' => 'L40.0',  'category' => 'Dermatology',   'description' => 'Psoriasis vulgaris'],
            ['code' => 'L50.0',  'category' => 'Dermatology',   'description' => 'Allergic urticaria'],
            ['code' => 'L72.0',  'category' => 'Dermatology',   'description' => 'Epidermal cyst'],

            // ── Musculoskeletal (M) ────────────────────────────────────────
            ['code' => 'M06.9',  'category' => 'Musculoskeletal', 'description' => 'Rheumatoid arthritis, unspecified'],
            ['code' => 'M10.9',  'category' => 'Musculoskeletal', 'description' => 'Gout, unspecified'],
            ['code' => 'M13.0',  'category' => 'Musculoskeletal', 'description' => 'Polyarthritis, unspecified'],
            ['code' => 'M16.9',  'category' => 'Musculoskeletal', 'description' => 'Coxarthrosis, unspecified'],
            ['code' => 'M17.9',  'category' => 'Musculoskeletal', 'description' => 'Gonarthrosis, unspecified'],
            ['code' => 'M47.9',  'category' => 'Musculoskeletal', 'description' => 'Spondylosis, unspecified'],
            ['code' => 'M54.5',  'category' => 'Musculoskeletal', 'description' => 'Low back pain'],
            ['code' => 'M79.3',  'category' => 'Musculoskeletal', 'description' => 'Panniculitis, unspecified'],

            // ── Genitourinary (N) ──────────────────────────────────────────
            ['code' => 'N18.9',  'category' => 'Nephrology',    'description' => 'Chronic kidney disease, unspecified'],
            ['code' => 'N20.0',  'category' => 'Nephrology',    'description' => 'Calculus of kidney'],
            ['code' => 'N20.1',  'category' => 'Nephrology',    'description' => 'Calculus of ureter'],
            ['code' => 'N30.00', 'category' => 'Nephrology',    'description' => 'Acute cystitis without haematuria'],
            ['code' => 'N39.0',  'category' => 'Nephrology',    'description' => 'Urinary tract infection, site not specified'],
            ['code' => 'N40',    'category' => 'Urology',       'description' => 'Hyperplasia of prostate'],
            ['code' => 'N92.0',  'category' => 'Gynaecology',   'description' => 'Excessive and frequent menstruation with regular cycle'],
            ['code' => 'N93.9',  'category' => 'Gynaecology',   'description' => 'Abnormal uterine and vaginal bleeding, unspecified'],

            // ── Pregnancy & Childbirth (O) ─────────────────────────────────
            ['code' => 'O00.9',  'category' => 'Obstetrics',    'description' => 'Ectopic pregnancy, unspecified'],
            ['code' => 'O10.0',  'category' => 'Obstetrics',    'description' => 'Pre-existing essential hypertension complicating pregnancy'],
            ['code' => 'O13',    'category' => 'Obstetrics',    'description' => 'Gestational hypertension'],
            ['code' => 'O14.0',  'category' => 'Obstetrics',    'description' => 'Mild to moderate pre-eclampsia'],
            ['code' => 'O14.1',  'category' => 'Obstetrics',    'description' => 'Severe pre-eclampsia'],
            ['code' => 'O20.0',  'category' => 'Obstetrics',    'description' => 'Threatened abortion'],
            ['code' => 'O24.0',  'category' => 'Obstetrics',    'description' => 'Diabetes mellitus in pregnancy: pre-existing diabetes mellitus, insulin-dependent'],
            ['code' => 'O47.0',  'category' => 'Obstetrics',    'description' => 'False labour before 37 completed weeks of gestation'],
            ['code' => 'O60.0',  'category' => 'Obstetrics',    'description' => 'Preterm labour without delivery'],
            ['code' => 'O80',    'category' => 'Obstetrics',    'description' => 'Encounter for full-term uncomplicated delivery'],
            ['code' => 'O82',    'category' => 'Obstetrics',    'description' => 'Encounter for caesarean delivery without indication'],

            // ── Perinatal (P) ──────────────────────────────────────────────
            ['code' => 'P07.3',  'category' => 'Paediatrics',   'description' => 'Other preterm newborn'],
            ['code' => 'P22.0',  'category' => 'Paediatrics',   'description' => 'Respiratory distress syndrome of newborn'],
            ['code' => 'P36.9',  'category' => 'Paediatrics',   'description' => 'Bacterial sepsis of newborn, unspecified'],
            ['code' => 'P59.9',  'category' => 'Paediatrics',   'description' => 'Neonatal jaundice, unspecified'],

            // ── Symptoms & Signs (R) ───────────────────────────────────────
            ['code' => 'R00.0',  'category' => 'Symptoms',      'description' => 'Tachycardia, unspecified'],
            ['code' => 'R05',    'category' => 'Symptoms',      'description' => 'Cough'],
            ['code' => 'R06.0',  'category' => 'Symptoms',      'description' => 'Dyspnoea'],
            ['code' => 'R07.9',  'category' => 'Symptoms',      'description' => 'Chest pain, unspecified'],
            ['code' => 'R10.9',  'category' => 'Symptoms',      'description' => 'Unspecified abdominal pain'],
            ['code' => 'R11',    'category' => 'Symptoms',      'description' => 'Nausea and vomiting'],
            ['code' => 'R50.9',  'category' => 'Symptoms',      'description' => 'Fever, unspecified'],
            ['code' => 'R51',    'category' => 'Symptoms',      'description' => 'Headache'],
            ['code' => 'R53.1',  'category' => 'Symptoms',      'description' => 'Weakness'],
            ['code' => 'R55',    'category' => 'Symptoms',      'description' => 'Syncope and collapse'],
            ['code' => 'R73.09', 'category' => 'Symptoms',      'description' => 'Other abnormal glucose'],

            // ── Injury & Poisoning (S & T) ─────────────────────────────────
            ['code' => 'S00.0',  'category' => 'Emergency',     'description' => 'Superficial injury of scalp'],
            ['code' => 'S09.9',  'category' => 'Emergency',     'description' => 'Unspecified injury of head'],
            ['code' => 'S20.0',  'category' => 'Emergency',     'description' => 'Contusion of breast'],
            ['code' => 'S52.5',  'category' => 'Emergency',     'description' => 'Fracture of lower end of radius'],
            ['code' => 'S62.00', 'category' => 'Emergency',     'description' => 'Fracture of navicular (scaphoid) bone of hand, unspecified'],
            ['code' => 'S72.00', 'category' => 'Emergency',     'description' => 'Fracture of neck of femur, unspecified'],
            ['code' => 'S82.00', 'category' => 'Emergency',     'description' => 'Fracture of patella, unspecified'],
            ['code' => 'T14.0',  'category' => 'Emergency',     'description' => 'Superficial injury of unspecified body region'],
            ['code' => 'T40.2',  'category' => 'Emergency',     'description' => 'Poisoning by other opioids'],
            ['code' => 'T78.1',  'category' => 'Emergency',     'description' => 'Other adverse food reactions not elsewhere classified'],
            ['code' => 'T78.4',  'category' => 'Emergency',     'description' => 'Allergy, unspecified'],
            ['code' => 'T79.9',  'category' => 'Emergency',     'description' => 'Unspecified early complication of trauma'],

            // ── Factors Influencing Health (Z) ────────────────────────────
            ['code' => 'Z00.00', 'category' => 'Preventive',    'description' => 'Encounter for general adult medical examination without abnormal findings'],
            ['code' => 'Z00.01', 'category' => 'Preventive',    'description' => 'Encounter for general adult medical examination with abnormal findings'],
            ['code' => 'Z00.1',  'category' => 'Preventive',    'description' => 'Encounter for newborn, infant and child health examinations'],
            ['code' => 'Z12.1',  'category' => 'Preventive',    'description' => 'Encounter for screening for intestinal infectious diseases'],
            ['code' => 'Z23',    'category' => 'Preventive',    'description' => 'Encounter for immunization'],
            ['code' => 'Z34.00', 'category' => 'Obstetrics',    'description' => 'Encounter for supervision of normal first pregnancy, unspecified trimester'],
            ['code' => 'Z34.90', 'category' => 'Obstetrics',    'description' => 'Encounter for supervision of normal pregnancy, unspecified, unspecified trimester'],
            ['code' => 'Z38.00', 'category' => 'Obstetrics',    'description' => 'Single liveborn infant, delivered vaginally'],
            ['code' => 'Z71.3',  'category' => 'Preventive',    'description' => 'Dietary counselling and surveillance'],
        ]);
    }
};
