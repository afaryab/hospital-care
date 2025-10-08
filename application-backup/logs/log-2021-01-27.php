<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2021-01-27 00:23:09 --> Query error: Unknown column 'orignal_amount' in 'field list' - Invalid query: INSERT INTO `reception_counters_closings_transactions` (`counter_id`, `amount`, `orignal_amount`, `customer_payed`, `change`, `income_or_expence`, `patient_id`, `user_id`, `type`) VALUES ('1', '500', '500', '500', '0', 'INCOME', NULL, '2', 'CARD')
ERROR - 2021-01-27 00:24:17 --> Query error: Unknown column 'customer_payed' in 'field list' - Invalid query: INSERT INTO `reception_counters_closings_transactions` (`counter_id`, `amount`, `orignal_amount`, `customer_payed`, `change`, `income_or_expence`, `patient_id`, `user_id`, `type`) VALUES ('1', '500', '500', '500', '0', 'INCOME', NULL, '2', 'CARD')
ERROR - 2021-01-27 00:24:43 --> Query error: Unknown column 'change' in 'field list' - Invalid query: INSERT INTO `reception_counters_closings_transactions` (`counter_id`, `amount`, `orignal_amount`, `customer_payed`, `change`, `income_or_expence`, `patient_id`, `user_id`, `type`) VALUES ('1', '500', '500', '500', '0', 'INCOME', NULL, '2', 'CARD')
ERROR - 2021-01-27 00:25:12 --> Query error: Unknown column 'patient_id' in 'field list' - Invalid query: INSERT INTO `reception_counters_closings_transactions` (`counter_id`, `amount`, `orignal_amount`, `customer_payed`, `change`, `income_or_expence`, `patient_id`, `user_id`, `type`) VALUES ('1', '500', '500', '500', '0', 'INCOME', NULL, '2', 'CARD')
ERROR - 2021-01-27 00:25:44 --> Query error: Unknown column 'user_id' in 'field list' - Invalid query: INSERT INTO `reception_counters_closings_transactions` (`counter_id`, `amount`, `orignal_amount`, `customer_payed`, `change`, `income_or_expence`, `patient_id`, `user_id`, `type`) VALUES ('1', '500', '500', '500', '0', 'INCOME', NULL, '2', 'CARD')
ERROR - 2021-01-27 00:26:27 --> Query error: Unknown column 'reception_transaction_id' in 'field list' - Invalid query: INSERT INTO `emergency_transactions` (`patient_id`, `doctor_id`, `service_id`, `amount_in_num`, `amount_in_figure`, `payment_type`, `payment_refference`, `receaved_by`, `submitted_for_accounts`, `cleared_by_accounts`, `treatment_id`, `units`, `reception_transaction_id`) VALUES ('5', NULL, '1', 500, '', 'CARD', '', '2', 0, 0, 3, 0, 1)
ERROR - 2021-01-27 00:29:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 00:29:49 --> Query error: Unknown column 'type' in 'field list' - Invalid query: INSERT INTO `reception_counters_closings_transactions` (`counter_id`, `amount`, `orignal_amount`, `customer_payed`, `change`, `income_or_expence`, `patient_id`, `user_id`, `type`) VALUES ('1', '50500', '50500', '51000', '500', 'INCOME', 1, '2', 'CARD')
ERROR - 2021-01-27 00:32:12 --> Query error: Unknown column 'reception_transaction_id' in 'field list' - Invalid query: INSERT INTO `inpatient_transactions` (`patient_id`, `doctor_id`, `amount_in_num`, `amount_in_figure`, `payment_type`, `payment_refference`, `receaved_by`, `submitted_for_accounts`, `cleared_by_accounts`, `file_id`, `units`, `reception_transaction_id`) VALUES ('3', '5', 15000, '', 'CARD', '', '2', 0, 0, 1, 0, 2)
ERROR - 2021-01-27 00:32:51 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 00:33:33 --> Query error: Column 'department_transaction_id' cannot be null - Invalid query: INSERT INTO `reception_counters_closings_transaction_elements` (`counter_id`, `closing_transaction_id`, `patient_id`, `user_id`, `amount`, `department_transaction_id`, `type`) VALUES ('1', 1, 1, '2', 7000, NULL, 'PATH')
ERROR - 2021-01-27 00:52:04 --> Query error: Column 'patient_id' cannot be null - Invalid query: INSERT INTO `reception_counters_closings_transaction_elements` (`counter_id`, `closing_transaction_id`, `patient_id`, `user_id`, `amount`, `department_transaction_id`, `type`) VALUES ('1', 3, NULL, '2', 500, 3, 'OPD')
ERROR - 2021-01-27 14:41:41 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 15:30:26 --> Query error: Column 'last_visit' cannot be null - Invalid query: INSERT INTO `marketing_patients_followup` (`assigned_to`, `patient_id`, `patient_name`, `status`, `last_visit`) VALUES (1, '1', 'Veda Bonner', 'OPEN', NULL)
ERROR - 2021-01-27 15:36:20 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:20 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:20 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:20 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:20 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:25 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:31 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:37 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:43 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:49 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:36:55 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:01 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:07 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:13 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:19 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:25 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:31 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:37 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:43 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:49 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:37:55 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:01 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:07 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:13 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:19 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:25 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:31 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:37 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:43 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:49 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:38:55 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:39:01 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:39:07 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:39:13 --> Query error: Table 'lamp.aauth_users' doesn't exist - Invalid query: SELECT *
FROM `aauth_users`
WHERE `id` IS NULL
ERROR - 2021-01-27 15:40:00 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:08:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:08:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:08:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:09:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:09:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:09:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:09:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:09:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:09:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:10:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:10:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:10:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:10:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:10:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:10:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:11:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:11:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:11:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:11:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:11:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:11:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:12:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:12:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:12:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:12:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:12:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:12:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:13:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:13:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:13:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:13:43 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:14:43 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:15:43 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:16:43 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:17:43 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:17:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:17:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:26:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:27:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:28:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:29:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:30:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:30:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:31:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:31:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:32:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:32:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:33:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:33:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:34:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:34:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:35:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:35:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:36:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:36:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:37:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:37:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:38:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:38:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:39:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:39:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:40:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:40:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:41:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:41:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:42:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:42:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:43:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:43:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:44:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:44:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:45:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:45:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:46:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:46:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 17:47:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:47:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:47:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:47:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:48:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:48:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:48:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:48:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:48:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:48:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:48:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:48:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:49:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:49:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:49:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:49:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:49:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:49:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:49:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:49:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:50:01 --> Cron is running check at 27-01-21
ERROR - 2021-01-27 17:50:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:50:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:50:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:50:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:50:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:50:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:50:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:51:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:51:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:51:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:51:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:51:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:51:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:51:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:51:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:52:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:52:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:52:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:52:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:52:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:52:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:52:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:52:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:53:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:53:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:53:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:53:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:53:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:53:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:53:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:53:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:54:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:54:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:54:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:54:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:54:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:54:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:54:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:54:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:55:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:55:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:55:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:55:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:55:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:55:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:55:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:55:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:56:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:56:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:56:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:56:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:56:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:56:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:56:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:56:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:57:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:57:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:57:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:57:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:57:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:57:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:57:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:57:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:58:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:58:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:58:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:58:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:58:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:58:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:58:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:58:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 17:59:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 17:59:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 17:59:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:59:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:59:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:59:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:59:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 17:59:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 18:00:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 18:00:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 18:00:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:00:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:00:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:00:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:00:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:00:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:01:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 18:01:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 18:01:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 18:01:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:01:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:01:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:01:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:01:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 18:02:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 18:02:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 18:02:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:02:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:02:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:02:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:02:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:02:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:03:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 18:03:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 18:03:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 18:03:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:03:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:03:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:03:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:03:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 18:04:02 --> Cron is running check at 27-01-21
ERROR - 2021-01-27 18:04:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 18:04:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 18:04:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:04:22 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:04:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:04:42 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:04:52 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 18:05:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 18:05:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 18:05:02 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:05:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 18:05:19 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
SUCCESS - 2021-01-27 18:06:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 18:06:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 18:07:01 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 18:07:01 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
SUCCESS - 2021-01-27 18:08:02 --> Cron is running check at 27-01-21
SUCCESS - 2021-01-27 18:08:02 --> SetFollowUP | SetFollowUP Follow up scanned at 27-01-21
ERROR - 2021-01-27 18:40:20 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:42:46 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:42:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:43:06 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:43:16 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:43:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:43:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:43:46 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:43:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:44:06 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:44:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:44:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:44:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:44:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:44:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:45:06 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:45:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:45:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:45:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:45:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:45:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:46:06 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:46:16 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:46:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:46:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:46:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:46:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:47:06 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:47:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:47:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:47:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:47:46 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:47:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:48:06 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:48:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:48:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:48:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:48:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:48:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:49:06 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:49:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:49:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:49:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:49:46 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:49:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:50:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:50:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:50:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:50:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:50:46 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:50:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:51:06 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:51:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:51:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:51:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:51:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:51:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:52:06 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:52:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:52:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:52:36 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:52:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:52:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:53:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:53:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:53:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:53:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:53:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:53:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:54:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:54:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:54:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:54:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:54:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:54:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:55:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:55:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:55:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:55:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:55:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:55:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:56:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:56:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:56:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:56:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:56:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:56:57 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:57:07 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:57:17 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:57:27 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:57:37 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:57:47 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:58:25 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
ERROR - 2021-01-27 23:59:25 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'lamp.opd_transactions.created_on' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT created_on, SUM(amount_in_num) AS amount
FROM `opd_transactions`
WHERE date(created_on) BETWEEN "2021-01-21" AND "2021-01-27"
GROUP BY date(created_on)
