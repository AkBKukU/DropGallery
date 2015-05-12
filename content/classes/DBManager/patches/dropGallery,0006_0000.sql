/*                                                                           *\
______________________________Patch Information________________________________

Description: Add Event to permanently delete old hidden files.
Data Integrity: Event the deletes rows from files_hidden

Required: Yes

\*                                                                           */
SET GLOBAL event_scheduler = ON;

DROP EVENT IF EXIST hidden_file_cleanup;

CREATE EVENT IF NOT EXIST hidden_file_cleanup
ON SCHEDULE EVERY MONTH
DO BEGIN
	DELETE FROM files_hidden WHERE 
	datetime_added < CURRENT_TIMESTAMP - INTERVAL 1 MONTH;
END