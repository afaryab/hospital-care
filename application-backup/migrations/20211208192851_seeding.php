<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seeding extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        // Add Fields.
        $this->db->simple_query('SET GLOBAL time_zone = "+05:00"');

        $this->aauth->create_group('Admin', 'Administratior', 'Super Admin Group', 'Dashboard/ViewDashboard');
        $this->aauth->create_group('Public', 'Public', 'Public Access Group', 'Dashboard/ViewDashboard');
        $this->aauth->create_group('Default', 'Default', 'Default Group', 'Dashboard/ViewDashboard');
        $this->aauth->create_group('Doctor - Emergency', 'Doctors', 'Doctors Group', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Nursing - Emergency', 'Doctors', 'Nursing Group', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Doctor - OPD', 'Doctors', 'Doctors Group', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Nursing - OPD', 'Doctors', 'Nursing Group', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Doctors - Inpatient', 'Doctors', 'Doctors Group', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Nursing - Inpatient', 'Doctors', 'Nursing Group', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Doctors - Day Care', 'Doctors', 'Doctors Group', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Nursing - Day Care', 'Doctors', 'Nursing Group', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Accountant', 'Accounts', 'Accountant Group', 'Reports/Accounts/Index');
        $this->aauth->create_group('Receptionist', 'Receptionist', 'Receptionist Group', 'Dashboard/ViewDashboard');
        $this->aauth->create_group('Reception Tablet', 'Reception Tablet', 'Reception Tablet', 'Patients/AddNew');
        $this->aauth->create_group('Stock Manager', 'Stock Management', 'Inventory and Assets Department', 'Dashboard/ViewDashboard');
        $this->aauth->create_group('Assets Manager', 'Assets Management', 'Inventory and Assets Department', 'Dashboard/ViewDashboard');
        $this->aauth->create_group('Xray Tech', 'Xray', 'Nursing Department', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Laboratory Tech', 'Pathology LAB', 'Pathology Department', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Laboratory Collection Center', 'Pathology LAB', 'Pathology Department', 'PatientTreatments/ListPatients');
        $this->aauth->create_group('Dental Tech', 'Dental', 'Dental Department', 'PatientTreatments/ListPatients');
        
        $domain = "@hamzahospital.com";
        
        $userId = $this->aauth->create_user('ahmadkokab@processton.com','Kokab!23','Ahmad Kokab');
        $this->aauth->add_member($userId,'Admin');
        $this->aauth->update_user_key($userId,'is_super_admin',1);

        $userId = $this->aauth->create_user('osamanisar@processton.com' ,'Osama!23','Osama Nisar');
        $this->aauth->add_member($userId,'Admin');
        $this->aauth->update_user_key($userId,'is_super_admin',1);

        $userId = $this->aauth->create_user('ceo@hamzahospital.com' ,'Ceo!23','Hamza CEO');
        $this->aauth->add_member($userId,'Admin');
        $this->aauth->update_user_key($userId,'is_super_admin',1);
        $this->aauth->update_user_key($userId,'is_accountant',1);

        $userId = $this->aauth->create_user('admin@hamzahospital.com' ,'Admin!23','Hamza Admin');
        $this->aauth->add_member($userId,'Admin');
        $this->aauth->update_user_key($userId,'is_super_admin',1);

        $accountantId = $this->aauth->create_user('accountent'.$domain ,'Accounts!23','Accountent');
        $this->aauth->update_user_key($accountantId,'is_accountant',1);
        
        $receptionistId = $this->aauth->create_user('mainreception.mor'.$domain ,'Main!23m','Main Reception Morning');
        $this->aauth->update_user_key($receptionistId,'is_receptionist',1);
        $this->aauth->update_user_key($receptionistId,'reception_id',1);
        $this->aauth->update_user_key($receptionistId,'is_accountant',1);

        $receptionistId = $this->aauth->create_user('mainreception.ev'.$domain ,'Main!23e','Main Reception Evening');
        $this->aauth->update_user_key($receptionistId,'is_receptionist',1);
        $this->aauth->update_user_key($receptionistId,'reception_id',1);
        $this->aauth->update_user_key($receptionistId,'is_accountant',1);

        $receptionistId = $this->aauth->create_user('mainreception.nt'.$domain ,'Main!23n','Main Reception Night');
        $this->aauth->update_user_key($receptionistId,'is_receptionist',1);
        $this->aauth->update_user_key($receptionistId,'reception_id',1);
        $this->aauth->update_user_key($receptionistId,'is_accountant',1);

        $receptionistId = $this->aauth->create_user('lgreception.mor'.$domain ,'Lg!23m','Lg Reception Morning');
        $this->aauth->update_user_key($receptionistId,'is_receptionist',1);
        $this->aauth->update_user_key($receptionistId,'reception_id',2);
        $this->aauth->update_user_key($receptionistId,'is_accountant',1);

        $receptionistId = $this->aauth->create_user('lgreception.ev'.$domain ,'Lg!23e','Lg Reception Evening');
        $this->aauth->update_user_key($receptionistId,'is_receptionist',1);
        $this->aauth->update_user_key($receptionistId,'reception_id',2);
        $this->aauth->update_user_key($receptionistId,'is_accountant',1);

        $receptionistId = $this->aauth->create_user('bsmreception.mor'.$domain ,'Bsm!23m','Basement Reception Morning');
        $this->aauth->update_user_key($receptionistId,'is_receptionist',1);
        $this->aauth->update_user_key($receptionistId,'reception_id',3);
        $this->aauth->update_user_key($receptionistId,'is_accountant',1);

        $receptionistId = $this->aauth->create_user('bsmreception.ev'.$domain ,'Bsm!23e','Basement Reception Evening');
        $this->aauth->update_user_key($receptionistId,'is_receptionist',1);
        $this->aauth->update_user_key($receptionistId,'reception_id',3);
        $this->aauth->update_user_key($receptionistId,'is_accountant',1);

        $doctorOpdID = $this->aauth->create_user('drnasirraza'.$domain,'Nasir!23','Dr. Nasir Raza');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);
        

        $doctorOpdID = $this->aauth->create_user('drhumairanasir'.$domain,'Humaira!23','Dr. Humaira Nasir');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('dramirsaeed'.$domain,'Amir!23','Dr. Amir Saeed');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drnaeemafzal'.$domain,'Naeem!23','Dr. Naeem Afzal');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drazharfarooq'.$domain,'Azhar!23','Dr. Azhar Farooq');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drarshad'.$domain,'Arshad!23','Dr. Arshad');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drzaki'.$domain,'Zaki!23','Dr. Zaki');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drsobiashahalam'.$domain,'Sobia!23','Dr. Sobia Shah Alam');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drzafar'.$domain,'Zafar!23','Dr. Zafar');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drnadeemsheikh'.$domain,'Nadeem!23','Dr. Nadeem Sheikh');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drabidkhan'.$domain,'Abid!23','Dr. Abid Khan');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('druzma'.$domain,'Uzma!23','Dr. Uzma');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drtehmina'.$domain,'Tehmina!23','Dr. Tehmina');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drfiazyazdani'.$domain,'Fiaz!23','Dr. Fiaz Yazdani');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drjavedshakir'.$domain,'Javed!23','Dr. Javed Shakir');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorOpdID = $this->aauth->create_user('drusmanahmed'.$domain,'Usman!23','Dr. Usman Ahmed Chadda');
        $this->aauth->update_user_key($doctorOpdID,'is_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_opd_doctor',1);
        $this->aauth->update_user_key($doctorOpdID,'is_inpatient_doctor',1);

        $doctorEmerID = $this->aauth->create_user('mo'.$domain,'moEmr!23','M.O');
        $this->aauth->update_user_key($doctorEmerID,'is_doctor',1);
        $this->aauth->update_user_key($doctorEmerID,'is_emergency_doctor',1);

        $doctorEmerID = $this->aauth->create_user('eo'.$domain,'eoEmr!23','E.O');
        $this->aauth->update_user_key($doctorEmerID,'is_doctor',1);
        $this->aauth->update_user_key($doctorEmerID,'is_emergency_doctor',1);

        


        $string = "
        INSERT INTO `reception_counters` (`id`, `counter_name`, `client_id`, `is_opd_allowed`, `is_emergency_allowed`, `is_inpatient_allowed`, `is_followup_allowed`, `is_allowed_to_pay_voucher`, `is_allowed_to_pay_from_petty_cash`, `cash_on_counter`, `cheques_on_counter`, `card_slips_on_counter`, `created_on`, `modified_on`) VALUES (NULL, 'Main Reception ', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', CURRENT_TIMESTAMP, NULL);
        INSERT INTO `reception_counters` (`id`, `counter_name`, `client_id`, `is_opd_allowed`, `is_emergency_allowed`, `is_inpatient_allowed`, `is_followup_allowed`, `is_allowed_to_pay_voucher`, `is_allowed_to_pay_from_petty_cash`, `cash_on_counter`, `cheques_on_counter`, `card_slips_on_counter`, `created_on`, `modified_on`) VALUES (NULL, 'Lg Reception ', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', CURRENT_TIMESTAMP, NULL);
        INSERT INTO `reception_counters` (`id`, `counter_name`, `client_id`, `is_opd_allowed`, `is_emergency_allowed`, `is_inpatient_allowed`, `is_followup_allowed`, `is_allowed_to_pay_voucher`, `is_allowed_to_pay_from_petty_cash`, `cash_on_counter`, `cheques_on_counter`, `card_slips_on_counter`, `created_on`, `modified_on`) VALUES (NULL, 'Basement Reception ', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', CURRENT_TIMESTAMP, NULL);
        INSERT INTO `opd_services` (`id`, `name`, `charges`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'M.O Morning', '300', 'M.O_MOR', '0', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `opd_services` (`id`, `name`, `charges`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'M.O Evening', '300', 'M.O_EV', '0', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `opd_services` (`id`, `name`, `charges`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'M.O Night', '300', 'M.O_NT', '0', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `opd_services` (`id`, `name`, `charges`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'W.M.O Morning', '400', 'W.M.O_MOR', '0', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `opd_services` (`id`, `name`, `charges`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'W.M.O Evening', '400', 'W.M.O_EV', '0', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `opd_services` (`id`, `name`, `charges`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'W.M.O Night', '400', 'W.M.O_NT', '0', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `opd_services` (`id`, `name`, `charges`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Consultation', '400', 'CONST', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Drip Service', '500', '1', '0', 'DRIP', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Venofer Service', '300', '1', '0', 'VENOFER', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Short Stay for 12 Hrs', '2000', '1', '0', 'SHRT_STAY_12H', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Short Stay for 24 Hrs', '3000', '1', '0', 'SHRT_STAY_24H', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Major Dressing', '300', '1', '0', 'MJR_DRSNG', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Minor Dressing', '150', '1', '0', 'MNR_DRSNG', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Nebulization', '100', '1', '0', 'NBL', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Injection', '100', '1', '0', 'INJ', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Blood Sugar Check', '100', '1', '0', 'BLD_SUGR', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'ICU', '6000', '1', '0', 'ICU', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Folyes Catheter', '1200', '1', '0', 'FOL_CATH', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'NG Tube Passing', '1200', '1', '0', 'NG_TUB_PAS', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Stomach Wash', '5000', '1', '0', 'STMCH_WASH', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'CTG', '600', '1', '0', 'CTG', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'ECG', '500', '1', '0', 'ECG', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Blood Transfusion', '1500', '1', '0', 'BLD_TRNS', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Blood Pressure', '50', '1', '0', 'BP', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Stitches', '500', '1', '0', 'STCHS', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'X-Ray', '1500', '1', '0', 'X_RAY', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Bed (per hour)', '200', '1', '0', 'BED_P_H', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Oxygen (per hour)', '200', '1', '0', 'OXG_P_H', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Vaccination', '1000', '1', '0', 'VAC', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Semi Private Room', '5000', '1', '0', 'SEM_PVT_RM', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Private Room', '7000', '1', '0', 'PVT_RM', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Vip Room', '9000', '1', '0', 'VIP_RM', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `emergency_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Nursery', '6000', '1', '0', 'NURSRY', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Normal Delivery Gereral', '25000', '1', '0', 'NRML_DLV_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Normal Delivery Private', '28000', '1', '0', 'NRML_DLV_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Normal Delivery Vip', '32000', '1', '0', 'NRML_DLV_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'DNC Gereral', '25000', '1', '0', 'DNC_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'DNC Private', '28000', '1', '0', 'DNC_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'DNC Vip', '32000', '1', '0', 'DNC_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Expulsion Gereral', '25000', '1', '0', 'EXP_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Expulsion Private', '30000', '1', '0', 'EXP_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Expulsion Vip', '35000', '1', '0', 'EXP_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'C-Section Gereral', '48000', '1', '0', 'C_SEC_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'C-Section Private', '55000', '1', '0', 'C_SEC_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'C-Section Vip', '60000', '1', '0', 'C_SEC_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Hystrectomy Gereral', '60000', '1', '0', 'HYS_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Hystrectomy Private', '65000', '1', '0', 'HYS_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Hystrectomy Vip', '70000', '1', '0', 'HYS_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Ovarian Cyst General', '55000', '1', '0', 'OV_CYSY_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Ovarian Cyst Private', '60000', '1', '0', 'OV_CYSY_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Ovarian Cyst Vip', '65000', '1', '0', 'OV_CYSY_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Apendix Gereral', '45000', '1', '0', 'APNDX_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Apendix Private', '50000', '1', '0', 'APNDX_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Apendix Vip', '55000', '1', '0', 'APNDX_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Open Method Gereral', '55000', '1', '0', 'OPN_MTHD_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Open Method Private', '60000', '1', '0', 'OPN_MTHD_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Open Method Vip', '65000', '1', '0', 'OPN_MTHD_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Laproscopic Gereral', '85000', '1', '0', 'LAP_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Laproscopic Private', '90000', '1', '0', 'LAP_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Laproscopic Vip', '95000', '1', '0', 'LAP_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'TVP Gereral', '70000', '1', '0', 'TVP_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'TVP Private', '80000', '1', '0', 'TVP_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'TVP Vip', '90000', '1', '0', 'TVP_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'TURP Gereral', '95000', '1', '0', 'TURP_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'TURP Private', '100000', '1', '0', 'TURP_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'TURP Vip', '110000', '1', '0', 'TURP_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Piles Operation Gereral', '50000', '1', '0', 'PIL_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Piles Operation Private', '55000', '1', '0', 'PIL_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Piles Operation Vip', '60000', '1', '0', 'PIL_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Abdominal Gereral', '50000', '1', '0', 'ABD_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Abdominal Private', '55000', '1', '0', 'ABD_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Abdominal Vip', '60000', '1', '0', 'ABD_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Inguinal Gereral', '50000', '1', '0', 'ING_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Inguinal Private', '55000', '1', '0', 'ING_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Inguinal Vip', '60000', '1', '0', 'ING_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Incisional Gereral', '70000', '1', '0', 'INC_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Incisional Private', '80000', '1', '0', 'INC_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Incisional Vip', '90000', '1', '0', 'INC_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Goitres Gereral', '100000', '1', '0', 'GOTRS_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Goitres Private', '110000', '1', '0', 'GOTRS_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Goitres Vip', '120000', '1', '0', 'GOTRS_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Bladder Stones Gereral', '65000', '1', '0', 'BLDR_STN_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Bladder Stones Private', '70000', '1', '0', 'BLDR_STN_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Bladder Stones Vip', '75000', '1', '0', 'BLDR_STN_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Tonsils Gereral', '50000', '1', '0', 'TNSL_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Tonsils Private', '55000', '1', '0', 'TNSL_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Tonsils Vip', '60000', '1', '0', 'TNSL_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Nose Operation Gereral', '55000', '1', '0', 'NOS_OPR_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Nose Operation Private', '60000', '1', '0', 'NOS_OPR_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Nose Operation Vip', '65000', '1', '0', 'NOS_OPR_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Hernia Gereral', '50000', '1', '0', 'HRNIA_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Hernia Private', '55000', '1', '0', 'HRNIA_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Hernia Vip', '60000', '1', '0', 'HRNIA_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Uralogy Gereral', '50000', '1', '0', 'URLG_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Uralogy Private', '55000', '1', '0', 'URLG_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Uralogy Vip', '60000', '1', '0', 'URLG_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Ortho Gereral', '70000', '1', '0', 'ORTHO_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Ortho Private', '75000', '1', '0', 'ORTHO_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Ortho Vip', '80000', '1', '0', 'ORTHO_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'E&T Gereral', '70000', '1', '0', 'ENT_G', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'E&T Private', '75000', '1', '0', 'ENT_P', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_services` (`id`, `name`, `charges`, `charges_including_tax`, `tax_rate`, `post_key`, `is_doctor_selectable`, `is_multiple`, `is_quantityable`, `fix_amount`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'E&T Vip', '80000', '1', '0', 'ENT_V', '1', '0', '0', '0', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO expenses_categories(name) VALUES ('Doctors Payments');
        INSERT INTO expenses_categories(name) VALUES ('Rent or mortgage payments');
        INSERT INTO expenses_categories(name) VALUES ('Home office costs');
        INSERT INTO expenses_categories(name) VALUES ('Utilities');
        INSERT INTO expenses_categories(name) VALUES ('Furniture, equipment, and machinery');
        INSERT INTO expenses_categories(name) VALUES ('Office supplies');
        INSERT INTO expenses_categories(name) VALUES ('Advertising and marketing');
        INSERT INTO expenses_categories(name) VALUES ('Website and software expenses');
        INSERT INTO expenses_categories(name) VALUES ('Entertainment');
        INSERT INTO expenses_categories(name) VALUES ('Business meals and travel expenses');
        INSERT INTO expenses_categories(name) VALUES ('Vehicle expenses');
        INSERT INTO expenses_categories(name) VALUES ('Payroll');
        INSERT INTO expenses_categories(name) VALUES ('Employee benefits ');
        INSERT INTO expenses_categories(name) VALUES ('Taxes');
        INSERT INTO expenses_categories(name) VALUES ('Business insurance');
        INSERT INTO expenses_categories(name) VALUES ('Business licenses and permits');
        INSERT INTO expenses_categories(name) VALUES ('Interest payments and bank fees');
        INSERT INTO expenses_categories(name) VALUES ('Membership fees');
        INSERT INTO expenses_categories(name) VALUES ('Professional fees and business services');
        INSERT INTO expenses_categories(name) VALUES ('Training and education');
        INSERT INTO expenses_categories(name) VALUES ('Refund');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'PVT-201', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'PVT-202', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'PVT-203', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'PVT-204', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'PVT-205', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'VIP-300', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'VIP-301', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'VIP-303', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'VIP-304', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'VIP-305', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'SEMI_PVT-1', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'SEMI_PVT-2', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'SEMI_PVT-3', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `inpd_rooms` (`id`, `name`, `charges`, `post_key`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'SEMI_PVT-4', '0', NULL, '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');   
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Jubilee Insurance', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'EFU', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'East West Insurance Co.', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'SPI Insurance Co.', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'United Insurance Co.', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'IGI Insurance', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Asia Insurance Co.', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'TPL Life Insurance', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'TPL Direct Insurance', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'UBL Insurance', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Health Econex', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        INSERT INTO `panel_companies` (`id`, `name`, `entered_by`, `is_deleted`, `created_on`, `modified_on`, `live_ref_number`, `is_synced`) VALUES (NULL, 'Shaheen Insurance', '1', '0', CURRENT_TIMESTAMP, NULL, NULL, '0');
        ";
        $quries = explode(';',$string);
        foreach($quries as $query){

            $trimedQuery = trim($query);

            if($trimedQuery != ''){
                $this->db->simple_query($trimedQuery);
            }

        }
    
    
    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table test_patients
        
    }

}
