/*                                                                           *\
______________________________Patch Information________________________________

Description: Create Database Users
Data Integrity: Safe

Required: Yes


_______________________________CLEANUP COMMANDS________________________________

DROP USER 'read_only'@'localhost';
DROP USER 'read_write'@'localhost';
DROP USER 'admin'@'localhost';

\*                                                                           */



CREATE USER 'read_only'@'localhost' IDENTIFIED BY '[%]init_password[/%]';
CREATE USER 'read_write'@'localhost' IDENTIFIED BY '[%]init_password[/%]';
CREATE USER 'admin'@'localhost' IDENTIFIED BY '[%]init_password[/%]';



GRANT SELECT ON dropGallery.* TO
'read_only'@'localhost';

GRANT SELECT,INSERT,UPDATE,DELETE ON dropGallery.* TO
'read_write'@'localhost';



GRANT CREATE,CREATE TEMPORARY TABLES,CREATE VIEW,SELECT,INSERT,UPDATE,DELETE,ALTER,DROP,INDEX,TRIGGER,EVENT, CREATE ROUTINE ON dropGallery.* TO
'admin'@'localhost';
