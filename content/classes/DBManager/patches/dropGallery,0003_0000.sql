/*                                                                           *\
______________________________Patch Information________________________________

Description: Create veiw for getting basic file info
Data Integrity: Safe

Required: Yes

\*                                                                           */

DROP VIEW IF EXISTS file_basic_info;
CREATE VIEW file_basic_info AS SELECT files.title,files.description,files.filesize,mimetypes.mimetype from files JOIN mimetypes USING(id_mimetype);
