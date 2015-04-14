/*                                                                           *\
______________________________Patch Information________________________________

Description: Increase desctription legth to 2500 chars
Data Integrity: Safe

Required: No

\*                                                                           */

ALTER TABLE `files` CHANGE COLUMN `description` `description` VARCHAR(2500);