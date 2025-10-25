CREATE DATABASE IF NOT EXISTS hospital_care DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'hc_user'@'%' IDENTIFIED BY 'hc_password';
GRANT ALL PRIVILEGES ON hospital_care.* TO 'hc_user'@'%';
FLUSH PRIVILEGES;

USE hospital_care;


-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: hospital_care
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aauth_groups`
--

DROP TABLE IF EXISTS `aauth_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `definition` text,
  `url` varchar(255) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aauth_perm_to_group`
--

DROP TABLE IF EXISTS `aauth_perm_to_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_perm_to_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `perm_id` int unsigned NOT NULL DEFAULT '0',
  `group_id` int unsigned NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  PRIMARY KEY (`perm_id`,`group_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aauth_perm_to_user`
--

DROP TABLE IF EXISTS `aauth_perm_to_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_perm_to_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `perm_id` int unsigned NOT NULL DEFAULT '0',
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  PRIMARY KEY (`perm_id`,`user_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aauth_perms`
--

DROP TABLE IF EXISTS `aauth_perms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_perms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  `perm_group` varchar(20) NOT NULL DEFAULT 'ANONYMOUS',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aauth_pms`
--

DROP TABLE IF EXISTS `aauth_pms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_pms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int unsigned NOT NULL,
  `receiver_id` int unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `date_sent` datetime DEFAULT NULL,
  `date_read` datetime DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aauth_system_variables`
--

DROP TABLE IF EXISTS `aauth_system_variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_system_variables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_key` varchar(100) NOT NULL,
  `value` text,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aauth_user_to_group`
--

DROP TABLE IF EXISTS `aauth_user_to_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_user_to_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `group_id` int unsigned NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aauth_user_variables`
--

DROP TABLE IF EXISTS `aauth_user_variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_user_variables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `data_key` varchar(100) NOT NULL,
  `value` text,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aauth_users`
--

DROP TABLE IF EXISTS `aauth_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_users` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_ultrasound_doc` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aauth_users_otp`
--

DROP TABLE IF EXISTS `aauth_users_otp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aauth_users_otp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `code` varchar(12) NOT NULL,
  `is_consumed` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `message` varchar(255) NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appointments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `live_ref_number` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `backups_table`
--

DROP TABLE IF EXISTS `backups_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backups_table` (
  `id` int NOT NULL AUTO_INCREMENT,
  `is_synced` int NOT NULL DEFAULT '0',
  `localpath` varchar(255) DEFAULT NULL,
  `synced_path` varchar(255) DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ci_exceptions`
--

DROP TABLE IF EXISTS `ci_exceptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ci_exceptions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `last_user_login_id` int DEFAULT NULL,
  `current_user_login_id` int DEFAULT NULL,
  `machine_name` varchar(100) NOT NULL,
  `machine_type` varchar(24) NOT NULL DEFAULT 'Default',
  `machine_unique_key` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=671 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clients_history`
--

DROP TABLE IF EXISTS `clients_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_login_id` int DEFAULT NULL,
  `activity_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dental_appointments`
--

DROP TABLE IF EXISTS `dental_appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dental_appointments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
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
  `appointment_notes` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dental_patient_file`
--

DROP TABLE IF EXISTS `dental_patient_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dental_patient_file` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `edited_amount` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3183 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dental_patients`
--

DROP TABLE IF EXISTS `dental_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dental_patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=123208 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dental_services`
--

DROP TABLE IF EXISTS `dental_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dental_services` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dental_transactions`
--

DROP TABLE IF EXISTS `dental_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dental_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3548 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dental_treatments`
--

DROP TABLE IF EXISTS `dental_treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dental_treatments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `diagnosis`
--

DROP TABLE IF EXISTS `diagnosis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diagnosis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dic_eng`
--

DROP TABLE IF EXISTS `dic_eng`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dic_eng` (
  `word` varchar(25) NOT NULL,
  `wordtype` varchar(20) NOT NULL,
  `definition` text NOT NULL,
  `used` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recipient` text NOT NULL,
  `sender` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `final_body` text NOT NULL,
  `header` text NOT NULL,
  `data` text NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emergency_patients`
--

DROP TABLE IF EXISTS `emergency_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emergency_patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `expires_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=352697 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emergency_services`
--

DROP TABLE IF EXISTS `emergency_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emergency_services` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emergency_transactions`
--

DROP TABLE IF EXISTS `emergency_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emergency_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72483 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emergency_treatments`
--

DROP TABLE IF EXISTS `emergency_treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emergency_treatments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72483 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expense_vouchers`
--

DROP TABLE IF EXISTS `expense_vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_vouchers` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `live_ref_number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46934 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `live_ref_number` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49910 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expenses_categories`
--

DROP TABLE IF EXISTS `expenses_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_synced` int NOT NULL DEFAULT '0',
  `live_ref_number` varchar(20) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_deleted` int DEFAULT '0',
  `pay_doc` int DEFAULT '1',
  `pay_others` int DEFAULT '1',
  `pay_users` int DEFAULT '1',
  `add_comments` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `followup_comments`
--

DROP TABLE IF EXISTS `followup_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `followup_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `followup_id` bigint NOT NULL,
  `comments` text,
  `time_to_call` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `health_card_patients`
--

DROP TABLE IF EXISTS `health_card_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `health_card_patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_patient_id` int NOT NULL,
  `patient_cnic` varchar(255) NOT NULL,
  `pateint_name` varchar(255) NOT NULL,
  `patient_contact_mobile` varchar(255) NOT NULL,
  `antenatal_status` int DEFAULT '0',
  `last_visit` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `owner_id` int NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inpatient_expense_transactions`
--

DROP TABLE IF EXISTS `inpatient_expense_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inpatient_expense_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=785 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inpatient_file`
--

DROP TABLE IF EXISTS `inpatient_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inpatient_file` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_visiting` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35830 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inpatient_transactions`
--

DROP TABLE IF EXISTS `inpatient_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inpatient_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `edited_amount` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13803 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inpatient_treatments`
--

DROP TABLE IF EXISTS `inpatient_treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inpatient_treatments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `live_ref_number` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inpd_rooms`
--

DROP TABLE IF EXISTS `inpd_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inpd_rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `charges` int NOT NULL DEFAULT '0',
  `post_key` varchar(255) DEFAULT NULL,
  `entered_by` int DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  `is_allotted` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inpd_services`
--

DROP TABLE IF EXISTS `inpd_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inpd_services` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_fileable` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inpt_patients`
--

DROP TABLE IF EXISTS `inpt_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inpt_patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=352697 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `laboratory_patients`
--

DROP TABLE IF EXISTS `laboratory_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laboratory_patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=352697 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_errors`
--

DROP TABLE IF EXISTS `log_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_errors` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marketing_patients_followup`
--

DROP TABLE IF EXISTS `marketing_patients_followup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marketing_patients_followup` (
  `id` int NOT NULL AUTO_INCREMENT,
  `assigned_to` int NOT NULL,
  `patient_id` bigint NOT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `next_call_time` datetime DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_visit` timestamp NULL DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `version` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modals`
--

DROP TABLE IF EXISTS `modals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_active` int NOT NULL DEFAULT '0',
  `show_on_every_load` int NOT NULL DEFAULT '0',
  `pulled_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_popups`
--

DROP TABLE IF EXISTS `models_popups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_popups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `body` text NOT NULL,
  `footer` text NOT NULL,
  `type` int NOT NULL DEFAULT '1' COMMENT '1 one time 2 everylogin 3 everytime',
  `status` int NOT NULL DEFAULT '1' COMMENT '1 active 2 deactivated',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_popups_views`
--

DROP TABLE IF EXISTS `models_popups_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_popups_views` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pulled_on` timestamp NULL DEFAULT NULL,
  `till` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opd_patients`
--

DROP TABLE IF EXISTS `opd_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `opd_patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=352697 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opd_services`
--

DROP TABLE IF EXISTS `opd_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `opd_services` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opd_transactions`
--

DROP TABLE IF EXISTS `opd_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `opd_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=233707 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opd_treatments`
--

DROP TABLE IF EXISTS `opd_treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `opd_treatments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=233707 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_options`
--

DROP TABLE IF EXISTS `page_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `is_public` int NOT NULL COMMENT '1 text 2 number 3 dropzone',
  `maximum_limit` int NOT NULL DEFAULT '2',
  `minimum_limit` int NOT NULL DEFAULT '255',
  `description` text NOT NULL,
  `editable` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `panel_companies`
--

DROP TABLE IF EXISTS `panel_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `panel_companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `entered_by` int DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `live_ref_number` varchar(20) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `panel_payments`
--

DROP TABLE IF EXISTS `panel_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `panel_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mr_no` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'PENDING',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  `amount_recieved` int DEFAULT '0',
  `amount_submitted` int NOT NULL DEFAULT '0',
  `is_bill_submitted` int NOT NULL DEFAULT '0',
  `payment_reference` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1467 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patients` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=352697 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `query_logging_table`
--

DROP TABLE IF EXISTS `query_logging_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `query_logging_table` (
  `id` int NOT NULL AUTO_INCREMENT,
  `operation` varchar(255) NOT NULL,
  `query_string` longblob NOT NULL,
  `target_table` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `rec_id` int DEFAULT NULL,
  `is_synced` int DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115048846 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reception_closings`
--

DROP TABLE IF EXISTS `reception_closings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reception_closings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_record` datetime NOT NULL,
  `user_id` int NOT NULL,
  `transactions_data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reception_counters`
--

DROP TABLE IF EXISTS `reception_counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reception_counters` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `modified_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reception_counters_closings`
--

DROP TABLE IF EXISTS `reception_counters_closings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reception_counters_closings` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `modified_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9551 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reception_counters_closings_transaction_elements`
--

DROP TABLE IF EXISTS `reception_counters_closings_transaction_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reception_counters_closings_transaction_elements` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `edited_amount` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=414569 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reception_counters_closings_transactions`
--

DROP TABLE IF EXISTS `reception_counters_closings_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reception_counters_closings_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `edited_amount` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=413875 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recestation_patients`
--

DROP TABLE IF EXISTS `recestation_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recestation_patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recestation_services`
--

DROP TABLE IF EXISTS `recestation_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recestation_services` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recestation_transactions`
--

DROP TABLE IF EXISTS `recestation_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recestation_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `edited_amount` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3054 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recestation_treatments`
--

DROP TABLE IF EXISTS `recestation_treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recestation_treatments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `live_ref_number` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3054 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `min_stock` int NOT NULL DEFAULT '2',
  `critical_stock` int NOT NULL DEFAULT '2',
  `suplier_name` varchar(255) NOT NULL,
  `suplier_contact` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_issue`
--

DROP TABLE IF EXISTS `stock_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_issue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `stock_id` int NOT NULL,
  `issue_to` varchar(255) NOT NULL,
  `stock` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_mesurements`
--

DROP TABLE IF EXISTS `test_mesurements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_mesurements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `test_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `unit` varchar(5) NOT NULL,
  `normal_value` int NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_services`
--

DROP TABLE IF EXISTS `test_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_services` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `modified_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_treatments`
--

DROP TABLE IF EXISTS `test_treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_treatments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `live_ref_number` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ultrasound_patients`
--

DROP TABLE IF EXISTS `ultrasound_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ultrasound_patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120748 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ultrasound_services`
--

DROP TABLE IF EXISTS `ultrasound_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ultrasound_services` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_fileable` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ultrasound_transactions`
--

DROP TABLE IF EXISTS `ultrasound_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ultrasound_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37305 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ultrasound_treatments`
--

DROP TABLE IF EXISTS `ultrasound_treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ultrasound_treatments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37305 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_viewd_models`
--

DROP TABLE IF EXISTS `user_viewd_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_viewd_models` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model_id` int NOT NULL,
  `user_id` int NOT NULL,
  `viewied_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xray_patients`
--

DROP TABLE IF EXISTS `xray_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `xray_patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_patient_id` int NOT NULL,
  `last_visit` datetime DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `live_ref_number` varchar(24) DEFAULT NULL,
  `is_synced` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=352697 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xray_services`
--

DROP TABLE IF EXISTS `xray_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `xray_services` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_fileable` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xray_transactions`
--

DROP TABLE IF EXISTS `xray_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `xray_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xray_treatments`
--

DROP TABLE IF EXISTS `xray_treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `xray_treatments` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `file_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25  9:03:50
