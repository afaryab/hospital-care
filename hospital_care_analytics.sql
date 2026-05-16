-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Apr 14, 2026 at 09:25 PM
-- Server version: 8.0.45
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital_care_analytics`
--

-- --------------------------------------------------------

--
-- Table structure for table `aauth_groups`
--

CREATE TABLE `aauth_groups` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `definition` text,
  `url` varchar(255) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_perms`
--

CREATE TABLE `aauth_perms` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  `perm_group` varchar(20) NOT NULL DEFAULT 'ANONYMOUS',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_perm_to_group`
--

CREATE TABLE `aauth_perm_to_group` (
  `id` int NOT NULL,
  `perm_id` int UNSIGNED NOT NULL DEFAULT '0',
  `group_id` int UNSIGNED NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_perm_to_user`
--

CREATE TABLE `aauth_perm_to_user` (
  `id` int NOT NULL,
  `perm_id` int UNSIGNED NOT NULL DEFAULT '0',
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_pms`
--

CREATE TABLE `aauth_pms` (
  `id` int NOT NULL,
  `sender_id` int UNSIGNED NOT NULL,
  `receiver_id` int UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `date_sent` datetime DEFAULT NULL,
  `date_read` datetime DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_system_variables`
--

CREATE TABLE `aauth_system_variables` (
  `id` int NOT NULL,
  `data_key` varchar(100) NOT NULL,
  `value` text,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_users`
--

CREATE TABLE `aauth_users` (
  `id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `communication_email` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `banned_message` varchar(100) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `last_login_attempt` datetime DEFAULT NULL,
  `forgot_exp` text,
  `remember_time` datetime DEFAULT NULL,
  `remember_exp` text,
  `verification_code` text,
  `totp_secret` varchar(16) DEFAULT NULL,
  `ip_address` text,
  `login_attempts` int DEFAULT '0',
  `profile_img_path` varchar(255) NOT NULL DEFAULT 'public/img/avatar.png',
  `profile_img_id` int DEFAULT NULL,
  `short_story` text,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `parent_id` int NOT NULL,
  `act_as_id` int DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `is_super_admin` int NOT NULL DEFAULT '0',
  `is_receptionist` int NOT NULL DEFAULT '0',
  `reception_id` int NOT NULL DEFAULT '0',
  `can_change_reception` int NOT NULL DEFAULT '0',
  `is_doctor` int NOT NULL DEFAULT '0',
  `is_nurse` int NOT NULL DEFAULT '0',
  `is_xray_tech` int NOT NULL DEFAULT '0',
  `is_opd_doctor` int NOT NULL DEFAULT '0',
  `is_inpatient_doctor` int NOT NULL DEFAULT '0',
  `is_emergency_doctor` int NOT NULL DEFAULT '0',
  `is_accountant` int NOT NULL DEFAULT '0',
  `change_password` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  `feild` varchar(255) DEFAULT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `opd_charges_type` int NOT NULL DEFAULT '0',
  `opd_charges_amount` int NOT NULL DEFAULT '0',
  `salery_amount` int DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `is_dentist` int DEFAULT '0',
  `is_ultrasound_doc` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_users_otp`
--

CREATE TABLE `aauth_users_otp` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `code` varchar(12) NOT NULL,
  `is_consumed` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_user_to_group`
--

CREATE TABLE `aauth_user_to_group` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `group_id` int UNSIGNED NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_user_variables`
--

CREATE TABLE `aauth_user_variables` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `data_key` varchar(100) NOT NULL,
  `value` text,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `message` varchar(255) NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `treatment_id` int DEFAULT NULL,
  `type` varchar(11) NOT NULL,
  `appointment_notes` varchar(255) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `patient_id` int NOT NULL,
  `opd_patient_id` int NOT NULL,
  `entered_by` int NOT NULL,
  `status` int NOT NULL DEFAULT '2',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `live_ref_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `backups_table`
--

CREATE TABLE `backups_table` (
  `id` int NOT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `localpath` varchar(255) DEFAULT NULL,
  `synced_path` varchar(255) DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ci_exceptions`
--

CREATE TABLE `ci_exceptions` (
  `id` int NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` int DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `method` varchar(45) DEFAULT NULL,
  `get` text,
  `post` text,
  `files` text,
  `is_ajax` tinyint DEFAULT NULL,
  `is_cli` tinyint DEFAULT NULL,
  `user_agent` text,
  `session_data` text,
  `stack_trace` text,
  `sql_query` text,
  `sql_error` text,
  `created_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int NOT NULL,
  `last_user_login_id` int DEFAULT NULL,
  `current_user_login_id` int DEFAULT NULL,
  `machine_name` varchar(100) NOT NULL,
  `machine_type` varchar(24) NOT NULL DEFAULT 'Default',
  `machine_unique_key` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `clients_history`
--

CREATE TABLE `clients_history` (
  `id` int NOT NULL,
  `user_login_id` int DEFAULT NULL,
  `activity_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dental_appointments`
--

CREATE TABLE `dental_appointments` (
  `id` int UNSIGNED NOT NULL,
  `doctor_id` int NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `patient_id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `file_id` int NOT NULL,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `entered_by` int NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `live_ref_number` varchar(20) DEFAULT NULL,
  `appointment_notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dental_patients`
--

CREATE TABLE `dental_patients` (
  `id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dental_patient_file`
--

CREATE TABLE `dental_patient_file` (
  `id` int NOT NULL,
  `panel_id` int DEFAULT NULL,
  `treatment_by` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `dental_patient_id` bigint NOT NULL,
  `status` varchar(50) NOT NULL,
  `patient_discomfort` varchar(13) DEFAULT NULL,
  `patient_bleed_excess` varchar(13) DEFAULT NULL,
  `already_medication` varchar(255) DEFAULT NULL,
  `patient_smoker` varchar(255) DEFAULT NULL,
  `patient_smoking_frequency` varchar(255) DEFAULT NULL,
  `is_diabetic` varchar(255) DEFAULT NULL,
  `tuberculosis` varchar(255) DEFAULT NULL,
  `hepatitis` varchar(255) DEFAULT NULL,
  `epilepsy` varchar(255) DEFAULT NULL,
  `rheumatic` varchar(255) DEFAULT NULL,
  `hiv` varchar(255) DEFAULT NULL,
  `is_heart_patient` varchar(255) DEFAULT NULL,
  `is_allergietic` varchar(255) DEFAULT NULL,
  `prefer_anesthetic` varchar(255) DEFAULT NULL,
  `is_pregnant` varchar(13) DEFAULT NULL,
  `patient_discomfirt_start` varchar(255) DEFAULT NULL,
  `patient_is_first_visit` varchar(20) DEFAULT NULL,
  `patient_last_visit` varchar(255) DEFAULT NULL,
  `patient_last_visit_process` text,
  `patient_physician` varchar(255) DEFAULT NULL,
  `patient_physician_phone` varchar(255) DEFAULT NULL,
  `patient_last_examination` text,
  `patient_under_medical` varchar(20) DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `room_id` int DEFAULT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `panel_name` varchar(255) DEFAULT NULL,
  `file_orignal_charges` int NOT NULL DEFAULT '0',
  `file_charges` int NOT NULL DEFAULT '0',
  `declared_loss` int NOT NULL DEFAULT '0',
  `declared_loss_by` int NOT NULL DEFAULT '0',
  `file_charges_paid` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `open_on` timestamp NULL DEFAULT NULL,
  `closed_on` datetime DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `edited_amount` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dental_services`
--

CREATE TABLE `dental_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `charges` int NOT NULL,
  `charges_including_tax` int NOT NULL DEFAULT '1',
  `tax_rate` int NOT NULL DEFAULT '0',
  `post_key` varchar(255) NOT NULL,
  `is_doctor_selectable` int NOT NULL DEFAULT '0',
  `is_multiple` int NOT NULL DEFAULT '0',
  `is_quantityable` int NOT NULL DEFAULT '0',
  `is_fileable` int NOT NULL DEFAULT '0',
  `fix_amount` int NOT NULL DEFAULT '0',
  `entered_by` int NOT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dental_transactions`
--

CREATE TABLE `dental_transactions` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `treatment_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `service_id` int NOT NULL,
  `amount_in_num` int NOT NULL,
  `amount_in_figure` text NOT NULL,
  `payment_type` varchar(11) NOT NULL,
  `payment_refference` text NOT NULL,
  `receaved_by` int NOT NULL,
  `submitted_for_accounts` int NOT NULL,
  `submitted_for_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts` int NOT NULL,
  `cleared_by_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts_by` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `units` int NOT NULL DEFAULT '1',
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` datetime DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `reception_transaction_id` int NOT NULL,
  `doctor_voucher_id` int DEFAULT NULL,
  `edited_amount` int DEFAULT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dental_treatments`
--

CREATE TABLE `dental_treatments` (
  `id` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `patient_id` bigint NOT NULL,
  `dental_patient_id` bigint NOT NULL,
  `patient_discomfort` varchar(13) DEFAULT NULL,
  `patient_bleed_excess` varchar(13) DEFAULT NULL,
  `already_medication` varchar(255) DEFAULT NULL,
  `patient_smoker` varchar(255) DEFAULT NULL,
  `patient_smoking_frequency` varchar(255) DEFAULT NULL,
  `is_diabetic` varchar(255) DEFAULT NULL,
  `tuberculosis` varchar(255) DEFAULT NULL,
  `hepatitis` varchar(255) DEFAULT NULL,
  `epilepsy` varchar(255) DEFAULT NULL,
  `rheumatic` varchar(255) DEFAULT NULL,
  `hiv` varchar(255) DEFAULT NULL,
  `is_heart_patient` varchar(255) DEFAULT NULL,
  `is_allergietic` varchar(255) DEFAULT NULL,
  `prefer_anesthetic` varchar(255) DEFAULT NULL,
  `is_pregnant` varchar(13) DEFAULT NULL,
  `patient_discomfirt_start` varchar(255) DEFAULT NULL,
  `patient_is_first_visit` varchar(20) DEFAULT NULL,
  `patient_last_visit` varchar(255) DEFAULT NULL,
  `patient_last_visit_process` text,
  `patient_physician` varchar(255) DEFAULT NULL,
  `patient_physician_phone` varchar(255) DEFAULT NULL,
  `patient_last_examination` text,
  `patient_under_medical` varchar(20) DEFAULT NULL,
  `treatment_diagnosis_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_files` longtext,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `treatment_by` int DEFAULT NULL,
  `treatment_charges` varchar(255) NOT NULL DEFAULT '0',
  `treatment_payed` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `diagnosis`
--

CREATE TABLE `diagnosis` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dic_eng`
--

CREATE TABLE `dic_eng` (
  `word` varchar(25) NOT NULL,
  `wordtype` varchar(20) NOT NULL,
  `definition` text NOT NULL,
  `used` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
  `id` int NOT NULL,
  `recipient` text NOT NULL,
  `sender` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `final_body` text NOT NULL,
  `header` text NOT NULL,
  `data` text NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_patients`
--

CREATE TABLE `emergency_patients` (
  `id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `expires_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_services`
--

CREATE TABLE `emergency_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `charges` int NOT NULL,
  `charges_including_tax` int NOT NULL DEFAULT '1',
  `tax_rate` int NOT NULL DEFAULT '0',
  `post_key` varchar(255) NOT NULL,
  `is_doctor_selectable` int NOT NULL DEFAULT '0',
  `is_multiple` int NOT NULL DEFAULT '0',
  `is_quantityable` int NOT NULL DEFAULT '0',
  `is_fileable` int NOT NULL DEFAULT '0',
  `fix_amount` int NOT NULL DEFAULT '0',
  `entered_by` int NOT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_transactions`
--

CREATE TABLE `emergency_transactions` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `treatment_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `service_id` int NOT NULL,
  `amount_in_num` int NOT NULL,
  `amount_in_figure` text NOT NULL,
  `payment_type` varchar(11) NOT NULL,
  `payment_refference` text NOT NULL,
  `receaved_by` int NOT NULL,
  `submitted_for_accounts` int NOT NULL,
  `submitted_for_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts` int NOT NULL,
  `cleared_by_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts_by` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `units` int NOT NULL DEFAULT '1',
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` datetime DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `reception_transaction_id` int NOT NULL,
  `edited_amount` int DEFAULT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_treatments`
--

CREATE TABLE `emergency_treatments` (
  `id` int NOT NULL,
  `patient_id` bigint NOT NULL,
  `emergency_patient_id` bigint NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_files` longtext,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `treatment_by` int DEFAULT NULL,
  `treatment_charges` int NOT NULL DEFAULT '0',
  `treatment_payed` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `expire_on` timestamp NULL DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `voucher_id` int DEFAULT NULL,
  `amount_received_num` int NOT NULL,
  `amount_received_words` varchar(255) NOT NULL,
  `payment_type` varchar(6) NOT NULL,
  `payment_reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `cleared_by_accounts` int NOT NULL,
  `cleared_by_accounts_on` datetime DEFAULT NULL,
  `cleared_by_accounts_by` int NOT NULL,
  `submitted_for_accounts` int NOT NULL,
  `submitted_for_accounts_on` datetime DEFAULT NULL,
  `category_id` int NOT NULL,
  `payed_to_employee` tinyint(1) NOT NULL DEFAULT '0',
  `payed_to` int DEFAULT NULL,
  `payed_to_other` varchar(100) DEFAULT NULL,
  `receaved_by` int NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `live_ref_number` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `expenses_categories`
--

CREATE TABLE `expenses_categories` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `live_ref_number` varchar(20) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_deleted` int DEFAULT '0',
  `pay_doc` int DEFAULT '1',
  `pay_others` int DEFAULT '1',
  `pay_users` int DEFAULT '1',
  `add_comments` int DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `expense_vouchers`
--

CREATE TABLE `expense_vouchers` (
  `id` int NOT NULL,
  `exp_category_id` int NOT NULL,
  `inpatient_file_id` int DEFAULT NULL,
  `exp_amount_numbers` int NOT NULL,
  `exp_amount_words` varchar(255) NOT NULL,
  `payed_to_employee` tinyint(1) NOT NULL DEFAULT '0',
  `employee_id` int DEFAULT NULL,
  `payed_to_others` varchar(50) DEFAULT NULL,
  `expense_notes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `live_ref_number` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `followup_comments`
--

CREATE TABLE `followup_comments` (
  `id` int NOT NULL,
  `followup_id` bigint NOT NULL,
  `comments` text,
  `time_to_call` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `health_card_patients`
--

CREATE TABLE `health_card_patients` (
  `id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `patient_cnic` varchar(255) NOT NULL,
  `pateint_name` varchar(255) NOT NULL,
  `patient_contact_mobile` varchar(255) NOT NULL,
  `antenatal_status` int DEFAULT '0',
  `last_visit` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int NOT NULL,
  `path` varchar(255) NOT NULL,
  `owner_id` int NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `inpatient_expense_transactions`
--

CREATE TABLE `inpatient_expense_transactions` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int DEFAULT NULL,
  `file_id` int NOT NULL,
  `amount_in_num` int NOT NULL,
  `amount_in_figure` text NOT NULL,
  `payment_type` varchar(11) NOT NULL,
  `payment_refference` text NOT NULL,
  `receaved_by` int NOT NULL,
  `submitted_for_accounts` int NOT NULL,
  `submitted_for_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts` int NOT NULL,
  `cleared_by_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts_by` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `units` int NOT NULL DEFAULT '1',
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` datetime DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `reception_transaction_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `inpatient_file`
--

CREATE TABLE `inpatient_file` (
  `id` int NOT NULL,
  `panel_id` int DEFAULT NULL,
  `treatment_by` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `inpatient_patient_id` bigint NOT NULL,
  `status` varchar(50) NOT NULL,
  `patient_discomfort` varchar(13) DEFAULT NULL,
  `patient_bleed_excess` varchar(13) DEFAULT NULL,
  `already_medication` varchar(255) DEFAULT NULL,
  `patient_smoker` varchar(255) DEFAULT NULL,
  `patient_smoking_frequency` varchar(255) DEFAULT NULL,
  `is_diabetic` varchar(255) DEFAULT NULL,
  `tuberculosis` varchar(255) DEFAULT NULL,
  `hepatitis` varchar(255) DEFAULT NULL,
  `epilepsy` varchar(255) DEFAULT NULL,
  `rheumatic` varchar(255) DEFAULT NULL,
  `hiv` varchar(255) DEFAULT NULL,
  `is_heart_patient` varchar(255) DEFAULT NULL,
  `is_allergietic` varchar(255) DEFAULT NULL,
  `prefer_anesthetic` varchar(255) DEFAULT NULL,
  `is_pregnant` varchar(13) DEFAULT NULL,
  `patient_discomfirt_start` varchar(255) DEFAULT NULL,
  `patient_is_first_visit` varchar(20) DEFAULT NULL,
  `patient_last_visit` varchar(255) DEFAULT NULL,
  `patient_last_visit_process` text,
  `patient_physician` varchar(255) DEFAULT NULL,
  `patient_physician_phone` varchar(255) DEFAULT NULL,
  `patient_last_examination` text,
  `patient_under_medical` varchar(20) DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `room_id` int DEFAULT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `panel_name` varchar(255) DEFAULT NULL,
  `file_orignal_charges` int NOT NULL DEFAULT '0',
  `file_charges` int NOT NULL DEFAULT '0',
  `declared_loss` int NOT NULL DEFAULT '0',
  `declared_loss_by` int NOT NULL DEFAULT '0',
  `file_charges_paid` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `open_on` timestamp NULL DEFAULT NULL,
  `closed_on` datetime DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `edited_amount` int DEFAULT NULL,
  `is_visiting` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `inpatient_transactions`
--

CREATE TABLE `inpatient_transactions` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int DEFAULT NULL,
  `file_id` int NOT NULL,
  `amount_in_num` int NOT NULL,
  `amount_in_figure` text NOT NULL,
  `payment_type` varchar(11) NOT NULL,
  `payment_refference` text NOT NULL,
  `receaved_by` int NOT NULL,
  `submitted_for_accounts` int NOT NULL,
  `submitted_for_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts` int NOT NULL,
  `cleared_by_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts_by` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `units` int NOT NULL DEFAULT '1',
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` datetime DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `reception_transaction_id` int NOT NULL,
  `edited_amount` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `inpatient_treatments`
--

CREATE TABLE `inpatient_treatments` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `file_id` int DEFAULT NULL,
  `inpatient_patient_id` bigint NOT NULL,
  `treatment_diagnosis_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_files` longtext,
  `treatment_by` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `inpd_rooms`
--

CREATE TABLE `inpd_rooms` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `charges` int NOT NULL DEFAULT '0',
  `post_key` varchar(255) DEFAULT NULL,
  `entered_by` int DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `is_allotted` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `inpd_services`
--

CREATE TABLE `inpd_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `charges` int NOT NULL,
  `charges_including_tax` int NOT NULL DEFAULT '1',
  `tax_rate` int NOT NULL DEFAULT '0',
  `post_key` varchar(255) NOT NULL,
  `is_doctor_selectable` int NOT NULL DEFAULT '0',
  `is_multiple` int NOT NULL DEFAULT '0',
  `is_quantityable` int NOT NULL DEFAULT '0',
  `fix_amount` int NOT NULL DEFAULT '0',
  `entered_by` int NOT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `is_fileable` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `inpt_patients`
--

CREATE TABLE `inpt_patients` (
  `id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `laboratory_patients`
--

CREATE TABLE `laboratory_patients` (
  `id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `log_errors`
--

CREATE TABLE `log_errors` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `status_code` varchar(5) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `logged_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_path` varchar(255) DEFAULT NULL,
  `line_number` varchar(255) DEFAULT NULL,
  `request_array` text NOT NULL,
  `post_array` text NOT NULL,
  `get_array` text NOT NULL,
  `server_array` text NOT NULL,
  `error` text NOT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_patients_followup`
--

CREATE TABLE `marketing_patients_followup` (
  `id` int NOT NULL,
  `assigned_to` int NOT NULL,
  `patient_id` bigint NOT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `next_call_time` datetime DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_visit` timestamp NULL DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `version` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `modals`
--

CREATE TABLE `modals` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_active` int NOT NULL DEFAULT '0',
  `show_on_every_load` int NOT NULL DEFAULT '0',
  `pulled_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `models_popups`
--

CREATE TABLE `models_popups` (
  `id` int NOT NULL,
  `title` text NOT NULL,
  `body` text NOT NULL,
  `footer` text NOT NULL,
  `type` int NOT NULL DEFAULT '1' COMMENT '1 one time 2 everylogin 3 everytime',
  `status` int NOT NULL DEFAULT '1' COMMENT '1 active 2 deactivated',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `models_popups_views`
--

CREATE TABLE `models_popups_views` (
  `id` int NOT NULL,
  `model_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pulled_on` timestamp NULL DEFAULT NULL,
  `till` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `opd_patients`
--

CREATE TABLE `opd_patients` (
  `id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `opd_services`
--

CREATE TABLE `opd_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `charges` int NOT NULL,
  `charges_including_tax` int NOT NULL DEFAULT '1',
  `tax_rate` int NOT NULL DEFAULT '0',
  `post_key` varchar(255) NOT NULL,
  `is_doctor_selectable` int NOT NULL DEFAULT '0',
  `is_multiple` int NOT NULL DEFAULT '0',
  `is_quantityable` int NOT NULL DEFAULT '0',
  `is_fileable` int NOT NULL DEFAULT '0',
  `fix_amount` int NOT NULL DEFAULT '0',
  `entered_by` int NOT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `opd_transactions`
--

CREATE TABLE `opd_transactions` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `treatment_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `service_id` int NOT NULL,
  `amount_in_num` int NOT NULL,
  `amount_in_figure` text NOT NULL,
  `payment_type` varchar(11) NOT NULL,
  `payment_refference` text NOT NULL,
  `receaved_by` int NOT NULL,
  `submitted_for_accounts` int NOT NULL,
  `submitted_for_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts` int NOT NULL,
  `cleared_by_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts_by` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `units` int NOT NULL DEFAULT '1',
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` datetime DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `reception_transaction_id` int NOT NULL,
  `doctor_voucher_id` int DEFAULT NULL,
  `edited_amount` int DEFAULT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `opd_treatments`
--

CREATE TABLE `opd_treatments` (
  `id` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `patient_id` bigint NOT NULL,
  `opd_patient_id` bigint NOT NULL,
  `patient_discomfort` varchar(13) DEFAULT NULL,
  `patient_bleed_excess` varchar(13) DEFAULT NULL,
  `already_medication` varchar(255) DEFAULT NULL,
  `patient_smoker` varchar(255) DEFAULT NULL,
  `patient_smoking_frequency` varchar(255) DEFAULT NULL,
  `is_diabetic` varchar(255) DEFAULT NULL,
  `tuberculosis` varchar(255) DEFAULT NULL,
  `hepatitis` varchar(255) DEFAULT NULL,
  `epilepsy` varchar(255) DEFAULT NULL,
  `rheumatic` varchar(255) DEFAULT NULL,
  `hiv` varchar(255) DEFAULT NULL,
  `is_heart_patient` varchar(255) DEFAULT NULL,
  `is_allergietic` varchar(255) DEFAULT NULL,
  `prefer_anesthetic` varchar(255) DEFAULT NULL,
  `is_pregnant` varchar(13) DEFAULT NULL,
  `patient_discomfirt_start` varchar(255) DEFAULT NULL,
  `patient_is_first_visit` varchar(20) DEFAULT NULL,
  `patient_last_visit` varchar(255) DEFAULT NULL,
  `patient_last_visit_process` text,
  `patient_physician` varchar(255) DEFAULT NULL,
  `patient_physician_phone` varchar(255) DEFAULT NULL,
  `patient_last_examination` text,
  `patient_under_medical` varchar(20) DEFAULT NULL,
  `treatment_diagnosis_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_files` longtext,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `treatment_by` int DEFAULT NULL,
  `treatment_charges` varchar(255) NOT NULL DEFAULT '0',
  `treatment_payed` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `page_options`
--

CREATE TABLE `page_options` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `is_public` int NOT NULL COMMENT '1 text 2 number 3 dropzone',
  `maximum_limit` int NOT NULL DEFAULT '2',
  `minimum_limit` int NOT NULL DEFAULT '255',
  `description` text NOT NULL,
  `editable` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panel_companies`
--

CREATE TABLE `panel_companies` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `entered_by` int DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panel_payments`
--

CREATE TABLE `panel_payments` (
  `id` int NOT NULL,
  `mr_no` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'PENDING',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `amount_recieved` int DEFAULT '0',
  `amount_submitted` int NOT NULL DEFAULT '0',
  `is_bill_submitted` int NOT NULL DEFAULT '0',
  `payment_reference` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int NOT NULL,
  `pateint_name` varchar(255) NOT NULL,
  `gender` varchar(1) DEFAULT NULL,
  `age_group` int NOT NULL,
  `age_days` int NOT NULL,
  `age_dob` datetime(1) NOT NULL,
  `patient_address` blob,
  `guardian` varchar(255) DEFAULT NULL,
  `relation` varchar(11) DEFAULT NULL,
  `patient_contact_mobile` varchar(255) DEFAULT NULL,
  `patient_contact_res` varchar(255) DEFAULT NULL,
  `patient_contact_office` varchar(255) DEFAULT NULL,
  `patient_cnic` varchar(255) DEFAULT NULL,
  `patient_email` varchar(255) DEFAULT NULL,
  `patient_profession` varchar(255) DEFAULT NULL,
  `patient_school` varchar(255) DEFAULT NULL,
  `patient_grade` varchar(255) DEFAULT NULL,
  `patient_mother` varchar(255) DEFAULT NULL,
  `patient_mother_occupation` varchar(255) DEFAULT NULL,
  `patient_mother_office_address` text,
  `patient_mother_phone` varchar(255) DEFAULT NULL,
  `patient_father` varchar(255) DEFAULT NULL,
  `patient_father_occupation` varchar(255) DEFAULT NULL,
  `patient_father_office_address` text,
  `patient_father_phone` varchar(255) DEFAULT NULL,
  `patient_reference` varchar(255) DEFAULT NULL,
  `last_visit` datetime DEFAULT NULL,
  `next_appointment` datetime DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `query_logging_table`
--

CREATE TABLE `query_logging_table` (
  `id` int NOT NULL,
  `operation` varchar(255) NOT NULL,
  `query_string` longblob NOT NULL,
  `target_table` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `rec_id` int DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `reception_closings`
--

CREATE TABLE `reception_closings` (
  `id` int NOT NULL,
  `date_record` datetime NOT NULL,
  `user_id` int NOT NULL,
  `transactions_data` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `reception_counters`
--

CREATE TABLE `reception_counters` (
  `id` int NOT NULL,
  `counter_name` varchar(100) NOT NULL,
  `client_id` int DEFAULT NULL,
  `is_opd_allowed` int NOT NULL DEFAULT '1',
  `is_emergency_allowed` int NOT NULL DEFAULT '1',
  `is_inpatient_allowed` int NOT NULL DEFAULT '1',
  `is_followup_allowed` int NOT NULL DEFAULT '1',
  `is_allowed_to_pay_voucher` int NOT NULL DEFAULT '1',
  `is_allowed_to_pay_from_petty_cash` int NOT NULL DEFAULT '1',
  `cash_on_counter` int NOT NULL DEFAULT '0',
  `cheques_on_counter` int NOT NULL DEFAULT '0',
  `card_slips_on_counter` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `reception_counters_closings`
--

CREATE TABLE `reception_counters_closings` (
  `id` int NOT NULL,
  `counter_id` int NOT NULL,
  `reception_id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` varchar(11) NOT NULL DEFAULT 'OPEN',
  `opening_amount` int NOT NULL DEFAULT '0',
  `closing_amount` int NOT NULL DEFAULT '0',
  `closing_amount_cash` int NOT NULL DEFAULT '0',
  `closing_amount_card` int NOT NULL DEFAULT '0',
  `closing_amount_creditcard` int NOT NULL DEFAULT '0',
  `closing_amount_atm` int NOT NULL DEFAULT '0',
  `expense_payed` int NOT NULL DEFAULT '0',
  `is_cash_recieved` int NOT NULL DEFAULT '0',
  `cash_recieved_amount` int DEFAULT NULL,
  `cash_recieved_by` varchar(255) DEFAULT NULL,
  `cash_recieving_difference` int DEFAULT '0',
  `cash_recieving_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `reception_counters_closings_transactions`
--

CREATE TABLE `reception_counters_closings_transactions` (
  `id` int NOT NULL,
  `counter_id` int NOT NULL,
  `amount` int NOT NULL DEFAULT '0',
  `orignal_amount` int DEFAULT '0',
  `patient_id` int DEFAULT '0',
  `user_id` int DEFAULT '0',
  `customer_payed` int NOT NULL,
  `change` int NOT NULL,
  `type` varchar(11) NOT NULL,
  `income_or_expence` varchar(11) NOT NULL DEFAULT 'INCOME',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime DEFAULT NULL,
  `edited_amount` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `reception_counters_closings_transaction_elements`
--

CREATE TABLE `reception_counters_closings_transaction_elements` (
  `id` int NOT NULL,
  `counter_id` int NOT NULL,
  `closing_transaction_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `amount` int NOT NULL DEFAULT '0',
  `original_amount` int NOT NULL DEFAULT '0',
  `type` varchar(11) NOT NULL DEFAULT 'CASH',
  `income_or_expence` varchar(11) NOT NULL DEFAULT 'INCOME',
  `department_transaction_id` int NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` datetime DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `doctor_service_seq_id` int DEFAULT NULL,
  `serial_number_doctor` int DEFAULT NULL,
  `serial_number_service` int DEFAULT NULL,
  `serial_number_doctor_nd_service` int DEFAULT NULL,
  `edited_amount` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `recestation_patients`
--

CREATE TABLE `recestation_patients` (
  `id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `recestation_services`
--

CREATE TABLE `recestation_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `charges` int NOT NULL,
  `charges_including_tax` int NOT NULL DEFAULT '1',
  `tax_rate` int NOT NULL DEFAULT '0',
  `post_key` varchar(255) NOT NULL,
  `is_doctor_selectable` int NOT NULL DEFAULT '0',
  `is_multiple` int NOT NULL DEFAULT '0',
  `is_quantityable` int NOT NULL DEFAULT '0',
  `fix_amount` int NOT NULL DEFAULT '0',
  `entered_by` int NOT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `recestation_transactions`
--

CREATE TABLE `recestation_transactions` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `mr_no` int NOT NULL,
  `treatment_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `service_id` int NOT NULL,
  `amount_in_num` int NOT NULL,
  `amount_in_figure` text NOT NULL,
  `payment_type` varchar(11) NOT NULL,
  `payment_refference` text NOT NULL,
  `receaved_by` int NOT NULL,
  `submitted_for_accounts` int NOT NULL,
  `submitted_for_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts` int NOT NULL,
  `cleared_by_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts_by` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `units` int NOT NULL DEFAULT '1',
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` datetime DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `reception_transaction_id` int NOT NULL,
  `doctor_voucher_id` int DEFAULT NULL,
  `edited_amount` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `recestation_treatments`
--

CREATE TABLE `recestation_treatments` (
  `id` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `patient_id` bigint NOT NULL,
  `mr_no` bigint NOT NULL,
  `opd_patient_id` bigint NOT NULL,
  `patient_discomfort` varchar(13) DEFAULT NULL,
  `patient_bleed_excess` varchar(13) DEFAULT NULL,
  `already_medication` varchar(255) DEFAULT NULL,
  `patient_smoker` varchar(255) DEFAULT NULL,
  `patient_smoking_frequency` varchar(255) DEFAULT NULL,
  `is_diabetic` varchar(255) DEFAULT NULL,
  `tuberculosis` varchar(255) DEFAULT NULL,
  `hepatitis` varchar(255) DEFAULT NULL,
  `epilepsy` varchar(255) DEFAULT NULL,
  `rheumatic` varchar(255) DEFAULT NULL,
  `hiv` varchar(255) DEFAULT NULL,
  `is_heart_patient` varchar(255) DEFAULT NULL,
  `is_allergietic` varchar(255) DEFAULT NULL,
  `prefer_anesthetic` varchar(255) DEFAULT NULL,
  `is_pregnant` varchar(13) DEFAULT NULL,
  `patient_discomfirt_start` varchar(255) DEFAULT NULL,
  `patient_is_first_visit` varchar(20) DEFAULT NULL,
  `patient_last_visit` varchar(255) DEFAULT NULL,
  `patient_last_visit_process` text,
  `patient_physician` varchar(255) DEFAULT NULL,
  `patient_physician_phone` varchar(255) DEFAULT NULL,
  `patient_last_examination` text,
  `patient_under_medical` varchar(20) DEFAULT NULL,
  `treatment_diagnosis_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_files` longtext,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `treatment_by` int DEFAULT NULL,
  `treatment_charges` varchar(255) NOT NULL DEFAULT '0',
  `treatment_payed` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `min_stock` int NOT NULL DEFAULT '2',
  `critical_stock` int NOT NULL DEFAULT '2',
  `suplier_name` varchar(255) NOT NULL,
  `suplier_contact` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `stock_issue`
--

CREATE TABLE `stock_issue` (
  `id` int NOT NULL,
  `stock_id` int NOT NULL,
  `issue_to` varchar(255) NOT NULL,
  `stock` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `test_mesurements`
--

CREATE TABLE `test_mesurements` (
  `id` int NOT NULL,
  `test_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `unit` varchar(5) NOT NULL,
  `normal_value` int NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `test_services`
--

CREATE TABLE `test_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `shrt_code` varchar(8) NOT NULL,
  `charges` int NOT NULL,
  `charges_including_tax` int NOT NULL DEFAULT '1',
  `tax_rate` int NOT NULL DEFAULT '0',
  `sample` varchar(42) NOT NULL,
  `reporting_time` varchar(30) DEFAULT NULL,
  `is_multiple` int NOT NULL DEFAULT '0',
  `entered_by` int DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `test_treatments`
--

CREATE TABLE `test_treatments` (
  `id` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `patient_id` bigint NOT NULL,
  `emergency_patient_id` bigint NOT NULL,
  `patient_discomfort` varchar(13) DEFAULT NULL,
  `patient_bleed_excess` varchar(13) DEFAULT NULL,
  `already_medication` varchar(255) DEFAULT NULL,
  `patient_smoker` varchar(255) DEFAULT NULL,
  `patient_smoking_frequency` varchar(255) DEFAULT NULL,
  `is_diabetic` varchar(255) DEFAULT NULL,
  `tuberculosis` varchar(255) DEFAULT NULL,
  `hepatitis` varchar(255) DEFAULT NULL,
  `epilepsy` varchar(255) DEFAULT NULL,
  `rheumatic` varchar(255) DEFAULT NULL,
  `hiv` varchar(255) DEFAULT NULL,
  `is_heart_patient` varchar(255) DEFAULT NULL,
  `is_allergietic` varchar(255) DEFAULT NULL,
  `prefer_anesthetic` varchar(255) DEFAULT NULL,
  `is_pregnant` varchar(13) DEFAULT NULL,
  `patient_discomfirt_start` varchar(255) DEFAULT NULL,
  `patient_is_first_visit` varchar(20) DEFAULT NULL,
  `patient_last_visit` varchar(255) DEFAULT NULL,
  `patient_last_visit_process` text,
  `patient_physician` varchar(255) DEFAULT NULL,
  `patient_physician_phone` varchar(255) DEFAULT NULL,
  `patient_last_examination` text,
  `patient_under_medical` varchar(20) DEFAULT NULL,
  `treatment_diagnosis_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_files` longtext,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `treatment_by` int NOT NULL,
  `treatment_charges` varchar(255) NOT NULL DEFAULT '0',
  `treatment_payed` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `expire_on` timestamp NULL DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ultrasound_patients`
--

CREATE TABLE `ultrasound_patients` (
  `id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ultrasound_services`
--

CREATE TABLE `ultrasound_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `charges` int NOT NULL,
  `charges_including_tax` int NOT NULL DEFAULT '1',
  `tax_rate` int NOT NULL DEFAULT '0',
  `post_key` varchar(255) NOT NULL,
  `is_doctor_selectable` int NOT NULL DEFAULT '0',
  `is_multiple` int NOT NULL DEFAULT '0',
  `is_quantityable` int NOT NULL DEFAULT '0',
  `fix_amount` int NOT NULL DEFAULT '0',
  `entered_by` int NOT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `is_fileable` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ultrasound_transactions`
--

CREATE TABLE `ultrasound_transactions` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `treatment_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `service_id` int NOT NULL,
  `amount_in_num` int NOT NULL,
  `amount_in_figure` text NOT NULL,
  `payment_type` varchar(11) NOT NULL,
  `payment_refference` text NOT NULL,
  `receaved_by` int NOT NULL,
  `submitted_for_accounts` int NOT NULL,
  `submitted_for_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts` int NOT NULL,
  `cleared_by_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts_by` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `units` int NOT NULL DEFAULT '1',
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` datetime DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `reception_transaction_id` int NOT NULL,
  `doctor_voucher_id` int DEFAULT NULL,
  `edited_amount` int DEFAULT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ultrasound_treatments`
--

CREATE TABLE `ultrasound_treatments` (
  `id` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `patient_id` bigint NOT NULL,
  `ultrasound_patient_id` bigint NOT NULL,
  `patient_discomfort` varchar(13) DEFAULT NULL,
  `patient_bleed_excess` varchar(13) DEFAULT NULL,
  `already_medication` varchar(255) DEFAULT NULL,
  `patient_smoker` varchar(255) DEFAULT NULL,
  `patient_smoking_frequency` varchar(255) DEFAULT NULL,
  `is_diabetic` varchar(255) DEFAULT NULL,
  `tuberculosis` varchar(255) DEFAULT NULL,
  `hepatitis` varchar(255) DEFAULT NULL,
  `epilepsy` varchar(255) DEFAULT NULL,
  `rheumatic` varchar(255) DEFAULT NULL,
  `hiv` varchar(255) DEFAULT NULL,
  `is_heart_patient` varchar(255) DEFAULT NULL,
  `is_allergietic` varchar(255) DEFAULT NULL,
  `prefer_anesthetic` varchar(255) DEFAULT NULL,
  `is_pregnant` varchar(13) DEFAULT NULL,
  `patient_discomfirt_start` varchar(255) DEFAULT NULL,
  `patient_is_first_visit` varchar(20) DEFAULT NULL,
  `patient_last_visit` varchar(255) DEFAULT NULL,
  `patient_last_visit_process` text,
  `patient_physician` varchar(255) DEFAULT NULL,
  `patient_physician_phone` varchar(255) DEFAULT NULL,
  `patient_last_examination` text,
  `patient_under_medical` varchar(20) DEFAULT NULL,
  `treatment_diagnosis_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_files` longtext,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `treatment_by` int DEFAULT NULL,
  `treatment_charges` varchar(255) NOT NULL DEFAULT '0',
  `treatment_payed` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `user_viewd_models`
--

CREATE TABLE `user_viewd_models` (
  `id` int NOT NULL,
  `model_id` int NOT NULL,
  `user_id` int NOT NULL,
  `viewied_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `xray_patients`
--

CREATE TABLE `xray_patients` (
  `id` int NOT NULL,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `xray_services`
--

CREATE TABLE `xray_services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `fix_amount` int NOT NULL DEFAULT '0',
  `charges` varchar(11) DEFAULT NULL,
  `charges_including_tax` int NOT NULL DEFAULT '1',
  `tax_rate` int NOT NULL DEFAULT '0',
  `post_key` varchar(255) DEFAULT NULL,
  `is_doctor_selectable` int NOT NULL DEFAULT '0',
  `is_multiple` int NOT NULL DEFAULT '0',
  `entered_by` int DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `is_fileable` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `xray_transactions`
--

CREATE TABLE `xray_transactions` (
  `id` int NOT NULL,
  `treatment_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int DEFAULT NULL,
  `service_id` int NOT NULL,
  `amount_in_num` int NOT NULL,
  `amount_in_figure` text NOT NULL,
  `payment_type` varchar(11) NOT NULL,
  `payment_refference` text NOT NULL,
  `receaved_by` int NOT NULL,
  `submitted_for_accounts` int NOT NULL,
  `submitted_for_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts` int NOT NULL,
  `cleared_by_accounts_on` timestamp NULL DEFAULT NULL,
  `cleared_by_accounts_by` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `units` int NOT NULL DEFAULT '1',
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` datetime DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `reception_transaction_id` int NOT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `xray_treatments`
--

CREATE TABLE `xray_treatments` (
  `id` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `patient_id` bigint NOT NULL,
  `xray_patient_id` bigint NOT NULL,
  `patient_discomfort` varchar(13) DEFAULT NULL,
  `patient_bleed_excess` varchar(13) DEFAULT NULL,
  `already_medication` varchar(255) DEFAULT NULL,
  `patient_smoker` varchar(255) DEFAULT NULL,
  `patient_smoking_frequency` varchar(255) DEFAULT NULL,
  `is_diabetic` varchar(255) DEFAULT NULL,
  `tuberculosis` varchar(255) DEFAULT NULL,
  `hepatitis` varchar(255) DEFAULT NULL,
  `epilepsy` varchar(255) DEFAULT NULL,
  `rheumatic` varchar(255) DEFAULT NULL,
  `hiv` varchar(255) DEFAULT NULL,
  `is_heart_patient` varchar(255) DEFAULT NULL,
  `is_allergietic` varchar(255) DEFAULT NULL,
  `prefer_anesthetic` varchar(255) DEFAULT NULL,
  `is_pregnant` varchar(13) DEFAULT NULL,
  `patient_discomfirt_start` varchar(255) DEFAULT NULL,
  `patient_is_first_visit` varchar(20) DEFAULT NULL,
  `patient_last_visit` varchar(255) DEFAULT NULL,
  `patient_last_visit_process` text,
  `patient_physician` varchar(255) DEFAULT NULL,
  `patient_physician_phone` varchar(255) DEFAULT NULL,
  `patient_last_examination` text,
  `patient_under_medical` varchar(20) DEFAULT NULL,
  `treatment_diagnosis_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_files` longtext,
  `service_id` int DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `treatment_by` int DEFAULT NULL,
  `treatment_charges` varchar(255) NOT NULL DEFAULT '0',
  `treatment_payed` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT NULL,
  `expire_on` timestamp NULL DEFAULT NULL,
  `will_occure_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) NOT NULL,
  `file_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aauth_groups`
--
ALTER TABLE `aauth_groups`
  ADD KEY `id` (`id`);

--
-- Indexes for table `aauth_perms`
--
ALTER TABLE `aauth_perms`
  ADD KEY `id` (`id`);

--
-- Indexes for table `aauth_perm_to_group`
--
ALTER TABLE `aauth_perm_to_group`
  ADD PRIMARY KEY (`perm_id`,`group_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `aauth_perm_to_user`
--
ALTER TABLE `aauth_perm_to_user`
  ADD PRIMARY KEY (`perm_id`,`user_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `aauth_pms`
--
ALTER TABLE `aauth_pms`
  ADD KEY `id` (`id`);

--
-- Indexes for table `aauth_system_variables`
--
ALTER TABLE `aauth_system_variables`
  ADD KEY `id` (`id`);

--
-- Indexes for table `aauth_users`
--
ALTER TABLE `aauth_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aauth_users_otp`
--
ALTER TABLE `aauth_users_otp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aauth_user_to_group`
--
ALTER TABLE `aauth_user_to_group`
  ADD PRIMARY KEY (`user_id`,`group_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `aauth_user_variables`
--
ALTER TABLE `aauth_user_variables`
  ADD KEY `id` (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `backups_table`
--
ALTER TABLE `backups_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ci_exceptions`
--
ALTER TABLE `ci_exceptions`
  ADD KEY `id` (`id`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD KEY `id` (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients_history`
--
ALTER TABLE `clients_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dental_appointments`
--
ALTER TABLE `dental_appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dental_patients`
--
ALTER TABLE `dental_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dental_patient_file`
--
ALTER TABLE `dental_patient_file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dental_services`
--
ALTER TABLE `dental_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dental_transactions`
--
ALTER TABLE `dental_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dental_treatments`
--
ALTER TABLE `dental_treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diagnosis`
--
ALTER TABLE `diagnosis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emails`
--
ALTER TABLE `emails`
  ADD KEY `id` (`id`);

--
-- Indexes for table `emergency_patients`
--
ALTER TABLE `emergency_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergency_services`
--
ALTER TABLE `emergency_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergency_transactions`
--
ALTER TABLE `emergency_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergency_treatments`
--
ALTER TABLE `emergency_treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses_categories`
--
ALTER TABLE `expenses_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_vouchers`
--
ALTER TABLE `expense_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `followup_comments`
--
ALTER TABLE `followup_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `health_card_patients`
--
ALTER TABLE `health_card_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD KEY `id` (`id`);

--
-- Indexes for table `inpatient_expense_transactions`
--
ALTER TABLE `inpatient_expense_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inpatient_file`
--
ALTER TABLE `inpatient_file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inpatient_transactions`
--
ALTER TABLE `inpatient_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inpatient_treatments`
--
ALTER TABLE `inpatient_treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inpd_rooms`
--
ALTER TABLE `inpd_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inpd_services`
--
ALTER TABLE `inpd_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inpt_patients`
--
ALTER TABLE `inpt_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laboratory_patients`
--
ALTER TABLE `laboratory_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_errors`
--
ALTER TABLE `log_errors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketing_patients_followup`
--
ALTER TABLE `marketing_patients_followup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modals`
--
ALTER TABLE `modals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `models_popups`
--
ALTER TABLE `models_popups`
  ADD KEY `id` (`id`);

--
-- Indexes for table `models_popups_views`
--
ALTER TABLE `models_popups_views`
  ADD KEY `id` (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opd_patients`
--
ALTER TABLE `opd_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opd_services`
--
ALTER TABLE `opd_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opd_transactions`
--
ALTER TABLE `opd_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opd_treatments`
--
ALTER TABLE `opd_treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_options`
--
ALTER TABLE `page_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `panel_companies`
--
ALTER TABLE `panel_companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `panel_payments`
--
ALTER TABLE `panel_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `query_logging_table`
--
ALTER TABLE `query_logging_table`
  ADD KEY `id` (`id`);

--
-- Indexes for table `reception_closings`
--
ALTER TABLE `reception_closings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reception_counters`
--
ALTER TABLE `reception_counters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reception_counters_closings`
--
ALTER TABLE `reception_counters_closings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reception_counters_closings_transactions`
--
ALTER TABLE `reception_counters_closings_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reception_counters_closings_transaction_elements`
--
ALTER TABLE `reception_counters_closings_transaction_elements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recestation_patients`
--
ALTER TABLE `recestation_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recestation_services`
--
ALTER TABLE `recestation_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recestation_transactions`
--
ALTER TABLE `recestation_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recestation_treatments`
--
ALTER TABLE `recestation_treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_issue`
--
ALTER TABLE `stock_issue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_mesurements`
--
ALTER TABLE `test_mesurements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_services`
--
ALTER TABLE `test_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_treatments`
--
ALTER TABLE `test_treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ultrasound_patients`
--
ALTER TABLE `ultrasound_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ultrasound_services`
--
ALTER TABLE `ultrasound_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ultrasound_transactions`
--
ALTER TABLE `ultrasound_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ultrasound_treatments`
--
ALTER TABLE `ultrasound_treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_viewd_models`
--
ALTER TABLE `user_viewd_models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xray_patients`
--
ALTER TABLE `xray_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xray_services`
--
ALTER TABLE `xray_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xray_transactions`
--
ALTER TABLE `xray_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xray_treatments`
--
ALTER TABLE `xray_treatments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aauth_groups`
--
ALTER TABLE `aauth_groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aauth_perms`
--
ALTER TABLE `aauth_perms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aauth_perm_to_group`
--
ALTER TABLE `aauth_perm_to_group`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aauth_perm_to_user`
--
ALTER TABLE `aauth_perm_to_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aauth_pms`
--
ALTER TABLE `aauth_pms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aauth_system_variables`
--
ALTER TABLE `aauth_system_variables`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aauth_users`
--
ALTER TABLE `aauth_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aauth_users_otp`
--
ALTER TABLE `aauth_users_otp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aauth_user_to_group`
--
ALTER TABLE `aauth_user_to_group`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aauth_user_variables`
--
ALTER TABLE `aauth_user_variables`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `backups_table`
--
ALTER TABLE `backups_table`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ci_exceptions`
--
ALTER TABLE `ci_exceptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients_history`
--
ALTER TABLE `clients_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_appointments`
--
ALTER TABLE `dental_appointments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_patients`
--
ALTER TABLE `dental_patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_patient_file`
--
ALTER TABLE `dental_patient_file`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_services`
--
ALTER TABLE `dental_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_transactions`
--
ALTER TABLE `dental_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_treatments`
--
ALTER TABLE `dental_treatments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `diagnosis`
--
ALTER TABLE `diagnosis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_patients`
--
ALTER TABLE `emergency_patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_services`
--
ALTER TABLE `emergency_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_transactions`
--
ALTER TABLE `emergency_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_treatments`
--
ALTER TABLE `emergency_treatments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses_categories`
--
ALTER TABLE `expenses_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_vouchers`
--
ALTER TABLE `expense_vouchers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followup_comments`
--
ALTER TABLE `followup_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `health_card_patients`
--
ALTER TABLE `health_card_patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inpatient_expense_transactions`
--
ALTER TABLE `inpatient_expense_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inpatient_file`
--
ALTER TABLE `inpatient_file`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inpatient_transactions`
--
ALTER TABLE `inpatient_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inpatient_treatments`
--
ALTER TABLE `inpatient_treatments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inpd_rooms`
--
ALTER TABLE `inpd_rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inpd_services`
--
ALTER TABLE `inpd_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inpt_patients`
--
ALTER TABLE `inpt_patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laboratory_patients`
--
ALTER TABLE `laboratory_patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_errors`
--
ALTER TABLE `log_errors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_patients_followup`
--
ALTER TABLE `marketing_patients_followup`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modals`
--
ALTER TABLE `modals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `models_popups`
--
ALTER TABLE `models_popups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `models_popups_views`
--
ALTER TABLE `models_popups_views`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_patients`
--
ALTER TABLE `opd_patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_services`
--
ALTER TABLE `opd_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_transactions`
--
ALTER TABLE `opd_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_treatments`
--
ALTER TABLE `opd_treatments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_options`
--
ALTER TABLE `page_options`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `panel_companies`
--
ALTER TABLE `panel_companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `panel_payments`
--
ALTER TABLE `panel_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `query_logging_table`
--
ALTER TABLE `query_logging_table`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reception_closings`
--
ALTER TABLE `reception_closings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reception_counters`
--
ALTER TABLE `reception_counters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reception_counters_closings`
--
ALTER TABLE `reception_counters_closings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reception_counters_closings_transactions`
--
ALTER TABLE `reception_counters_closings_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reception_counters_closings_transaction_elements`
--
ALTER TABLE `reception_counters_closings_transaction_elements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recestation_patients`
--
ALTER TABLE `recestation_patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recestation_services`
--
ALTER TABLE `recestation_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recestation_transactions`
--
ALTER TABLE `recestation_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recestation_treatments`
--
ALTER TABLE `recestation_treatments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_issue`
--
ALTER TABLE `stock_issue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_mesurements`
--
ALTER TABLE `test_mesurements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_services`
--
ALTER TABLE `test_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_treatments`
--
ALTER TABLE `test_treatments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ultrasound_patients`
--
ALTER TABLE `ultrasound_patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ultrasound_services`
--
ALTER TABLE `ultrasound_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ultrasound_transactions`
--
ALTER TABLE `ultrasound_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ultrasound_treatments`
--
ALTER TABLE `ultrasound_treatments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_viewd_models`
--
ALTER TABLE `user_viewd_models`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `xray_patients`
--
ALTER TABLE `xray_patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `xray_services`
--
ALTER TABLE `xray_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `xray_transactions`
--
ALTER TABLE `xray_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `xray_treatments`
--
ALTER TABLE `xray_treatments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
