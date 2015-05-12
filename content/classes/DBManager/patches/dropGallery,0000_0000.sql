/*                                                                           *\
______________________________Patch Information________________________________

Description: Initial database creation
Data Integrity: Destroy All

Required: Yes

\*                                                                           */

SET FOREIGN_KEY_CHECKS = 0;



DROP TABLE IF EXISTS `files`;
CREATE TABLE files 
(
	id_file INTEGER NOT NULL AUTO_INCREMENT,
	quickhash VARCHAR(32) NOT NULL,
	title VARCHAR(80) NULL DEFAULT NULL,
	description VARCHAR(2500) NULL DEFAULT NULL,
	id_mimetype INTEGER NOT NULL COMMENT 'http://www.iana.org/assignments/media-types/media-types.xhtm',
	datetime_added DATETIME NOT NULL,
	filename VARCHAR(200) NOT NULL,
	filesize INTEGER NOT NULL,
	PRIMARY KEY (id_file)
);

DROP TABLE IF EXISTS `files_hidden`;
CREATE TABLE files_hidden 
(
	id_file INTEGER NOT NULL AUTO_INCREMENT,
	quickhash VARCHAR(32) NOT NULL,
	title VARCHAR(80) NULL DEFAULT NULL,
	description VARCHAR(500) NULL DEFAULT NULL,
	id_mimetype INTEGER NOT NULL COMMENT 'http://www.iana.org/assignments/media-types/media-types.xhtm',
	datetime_added DATETIME NOT NULL,
	datetime_deleted DATETIME NOT NULL,
	filename VARCHAR(200) NOT NULL,
	filesize INTEGER NOT NULL,
	PRIMARY KEY (id_file)
);


DROP TABLE IF EXISTS `file_tags`;
CREATE TABLE file_tags
(
	id_file INTEGER NOT NULL,
	id_tag INT NOT NULL,
	UNIQUE KEY (id_file, id_tag)
);

DROP TABLE IF EXISTS `tags`;
CREATE TABLE tags 
(
	id_tag INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(256) NOT NULL,
	PRIMARY KEY (id_tag),
	UNIQUE KEY (name)
);

DROP TABLE IF EXISTS `metadata`;
CREATE TABLE metadata
(
	id_metadata INTEGER NOT NULL AUTO_INCREMENT,
	id_metadata_type INT NOT NULL,
	data VARCHAR(200) NULL DEFAULT NULL,
	PRIMARY KEY (id_metadata)
);

DROP TABLE IF EXISTS `extra_data`;
CREATE TABLE extra_data 
(
	id_extra_data INTEGER NOT NULL AUTO_INCREMENT,
	name VARCHAR(150) NOT NULL,
	data VARCHAR(200) NULL DEFAULT NULL,
	PRIMARY KEY (id_extra_data)
);

DROP TABLE IF EXISTS `mimetypes`;
CREATE TABLE mimetypes 
(
	id_mimetype INTEGER NOT NULL AUTO_INCREMENT,
	mimetype VARCHAR(150) NOT NULL,
	PRIMARY KEY (id_mimetype),
	UNIQUE KEY (mimetype)
);

DROP TABLE IF EXISTS `file_metadata`;
CREATE TABLE file_metadata
(
	id_file INTEGER NOT NULL,
	id_metadata INTEGER NOT NULL,
	UNIQUE KEY (id_file, id_metadata)
);

DROP TABLE IF EXISTS `file_extra_data`;
CREATE TABLE file_extra_data
(
	id_file INTEGER NOT NULL,
	id_extra_data INTEGER NOT NULL,
	UNIQUE KEY (id_file, id_extra_data)
);

DROP TABLE IF EXISTS `metadata_type`;
CREATE TABLE metadata_type
(
	id_metadata_type INTEGER NOT NULL AUTO_INCREMENT,
	type VARCHAR(150) NOT NULL,
	name VARCHAR(200) NULL DEFAULT NULL,
	PRIMARY KEY (id_metadata_type)
);

ALTER TABLE `files` 
ADD CONSTRAINT fk_mimetype FOREIGN KEY (id_mimetype) REFERENCES `mimetypes` (`id_mimetype`);
ALTER TABLE `file_tags` 
ADD CONSTRAINT fk_file FOREIGN KEY (id_file) REFERENCES `files` (`id_file`);
ALTER TABLE `file_tags` 
ADD CONSTRAINT fk_tag FOREIGN KEY (id_tag) REFERENCES `tags` (`id_tag`);
ALTER TABLE `metadata` 
ADD CONSTRAINT fk_metadata_type FOREIGN KEY (id_metadata_type) REFERENCES `metadata_type` (`id_metadata_type`);
ALTER TABLE `file_metadata` 
ADD CONSTRAINT fk_file FOREIGN KEY (id_file) REFERENCES `files` (`id_file`);
ALTER TABLE `file_metadata` 
ADD CONSTRAINT fk_metadata FOREIGN KEY (id_metadata) REFERENCES `metadata` (`id_metadata`);
ALTER TABLE `file_extra_data` 
ADD CONSTRAINT fk_file FOREIGN KEY (id_file) REFERENCES `files` (`id_file`);
ALTER TABLE `file_extra_data` 
ADD CONSTRAINT fk_extra_data FOREIGN KEY (id_extra_data) REFERENCES `extra_data` (`id_extra_data`);

ALTER TABLE `id_extra_data` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

SET FOREIGN_KEY_CHECKS = 1;

