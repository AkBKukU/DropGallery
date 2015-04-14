/*                                                                           *\
______________________________Patch Information________________________________

Description: Test Patch
Data Integrity: Adds fake file

Required: No

\*                                                                           */

INSERT INTO files ( quickhash, title, description, id_mimetype, datetime_added, filename, filesize ) VALUES ( "quickhash", "title", "description", 1, NOW(), "filename", 168 ) 


-- Add a feild to deleted files table to store delete date. Event can delete month old entries permenantly