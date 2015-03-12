/*                                                                           *\
	                                 Add Tables
\*                                                                           */

CREATE TABLE files 
(
	id_file INTEGER NOT NULL AUTO_INCREMENT,
	quickhash VARCHAR(32) NOT NULL,
	title VARCHAR(80) NULL DEFAULT NULL,
	description VARCHAR(500) NULL DEFAULT NULL,
	id_mimetype INTEGER NOT NULL COMMENT 'http://www.iana.org/assignments/media-types/media-types.xhtm',
	datetime_added DATETIME NOT NULL,
	filename VARCHAR(200) NOT NULL,
	filesize INTEGER NOT NULL,
	PRIMARY KEY (id_file)
);

CREATE TABLE files_hidden 
(
	id_file INTEGER NOT NULL AUTO_INCREMENT,
	quickhash VARCHAR(32) NOT NULL,
	title VARCHAR(80) NULL DEFAULT NULL,
	description VARCHAR(500) NULL DEFAULT NULL,
	id_mimetype INTEGER NOT NULL COMMENT 'http://www.iana.org/assignments/media-types/media-types.xhtm',
	datetime_added DATETIME NOT NULL,
	filename VARCHAR(200) NOT NULL,
	filesize INTEGER NOT NULL,
	PRIMARY KEY (id_file)
);


CREATE TABLE file_tags
(
	id_file INTEGER NOT NULL,
	id_tag INT NOT NULL,
	UNIQUE KEY (id_file, id_tag)
);

CREATE TABLE tags 
(
	id_tag INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(80) NOT NULL,
	PRIMARY KEY (id_tag),
	UNIQUE KEY (name)
);

CREATE TABLE metadata
(
	id_metadata INTEGER NOT NULL AUTO_INCREMENT,
	id_metadata_type INT NOT NULL,
	data VARCHAR(200) NULL DEFAULT NULL,
	PRIMARY KEY (id_metadata)
);

CREATE TABLE extra_data 
(
	id_extra_data INTEGER NOT NULL AUTO_INCREMENT,
	type VARCHAR(150) NOT NULL,
	data VARCHAR(200) NULL DEFAULT NULL,
	PRIMARY KEY (id_extra_data)
);

CREATE TABLE mimetypes 
(
	id_mimetype INTEGER NOT NULL AUTO_INCREMENT,
	mimetype VARCHAR(150) NOT NULL,
	PRIMARY KEY (id_mimetype),
	UNIQUE KEY (mimetype)
);

CREATE TABLE file_metadata
(
	id_file INTEGER NOT NULL,
	id_metadata INTEGER NOT NULL,
	UNIQUE KEY (id_file, id_metadata)
);

CREATE TABLE file_extra_data
(
	id_file INTEGER NOT NULL,
	id_extra_data INTEGER NOT NULL,
	UNIQUE KEY (id_file, id_extra_data)
);

CREATE TABLE metadata_type
(
	id_metadata_type INTEGER NOT NULL AUTO_INCREMENT,
	type VARCHAR(150) NOT NULL,
	name VARCHAR(200) NULL DEFAULT NULL,
	PRIMARY KEY (id_metadata_type)
);


ALTER TABLE `files` 
ADD FOREIGN KEY (id_mimetype) REFERENCES `mimetypes` (`id_mimetype`);
ALTER TABLE `file_tags` 
ADD FOREIGN KEY (id_file) REFERENCES `files` (`id_file`);
ALTER TABLE `file_tags` 
ADD FOREIGN KEY (id_tag) REFERENCES `tags` (`id_tag`);
ALTER TABLE `metadata` 
ADD FOREIGN KEY (id_metadata_type) REFERENCES `metadata_type` (`id_metadata_type`);
ALTER TABLE `file_metadata` 
ADD FOREIGN KEY (id_file) REFERENCES `files` (`id_file`);
ALTER TABLE `file_metadata` 
ADD FOREIGN KEY (id_metadata) REFERENCES `metadata` (`id_metadata`);
ALTER TABLE `file_extra_data` 
ADD FOREIGN KEY (id_file) REFERENCES `files` (`id_file`);
ALTER TABLE `file_extra_data` 
ADD FOREIGN KEY (id_extra_data) REFERENCES `extra_data` (`id_extra_data`);
ALTER TABLE `metadata_type` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Change type to name for extra data
ALTER TABLE extra_data CHANGE 
type name VARCHAR(150);


/*                                                                           *\
	                                 Add Data
\*                                                                           */

INSERT INTO mimetypes(mimetype) VALUES 
('image/jpeg'),
('text/plain'),
('image/png');

INSERT INTO metadata_type(type, name) VALUES 
('width','Width'),
('height','Height'),
('exif:Make','Camera Make'),
('exif:Model','Camera Model'),
('exif:UndefinedTag:0xA434','Lens Info'),
('exif:DateTimeOriginal','Date Taken')
;

INSERT INTO tags(name) VALUES 
('Nature'),
('Flower'),
('Cat'),
('Car'),
('Macro')
;

INSERT INTO files ( `quickhash` , `title` , `description` , `id_mimetype` , `datetime_added` , `filename` , `filesize` ) VALUES
('0123456789012345678901','Cat Picture','Look at th fluffy kitty!','1','2015-02-01T13:15','kitty.jpg','1684357'),
('0123456789012345678902','Fast Car','I bet it goes 185...','1','2015-02-01T13:15','maserati.jpg','1684357'),
('0123456789012345678903','Qouth the raven','Nevermore','3','2015-02-01T13:15','raven.txt','1684357');

INSERT INTO `metadata`(`id_metadata_type`, `data`) VALUES 
("1","320"),
("2","240"),
("3","Nikon"),
("4","FM10"),
("1","640"),
("2","480"),
("3","Nikon"),
("4","FM10");

INSERT INTO `extra_data`(`type`, `data`) VALUES 
("text sample","Once upon a midnight dreary, while I pondered, weak and weary,
Over many a quaint and curious volume of forgotten lore—
	  While I nodded, nearly napping, suddenly there came a tapping,
As of some o
");

INSERT INTO `file_metadata`(`id_file`, `id_metadata`) VALUES 
(1,1),
(1,2),
(1,3),
(1,4),
(2,5),
(2,6),
(2,7),
(2,8);

INSERT INTO `file_extra_data`(`id_file`, `id_extra_data`) VALUES 
(3,1);

INSERT INTO `file_tags`(`id_file`, `id_tag`) VALUES 
(1,1),
(1,3),
(2,4);

/*                                                                           *\
	                                 Test Queries
\*                                                                           */

-- Update text sample to caps
UPDATE extra_data SET type = 'Text Sample' WHERE type = "text sample";

-- Get File tags
SELECT  files.title, tags.name
FROM files JOIN file_tags USING(id_file) JOIN tags USING(id_tag);

-- Get File tags in single rows
SELECT  files.title, group_concat(tags.name)
FROM files JOIN file_tags USING(id_file) JOIN tags USING(id_tag)
GROUP BY files.title;

-- Get File Meta Data
SELECT  files.title, metadata_type.name, metadata.data
FROM files JOIN file_metadata USING(id_file)  JOIN metadata USING(id_metadata) JOIN metadata_type USING(id_metadata_type);

-- Get File Meta Data Count
SELECT  files.title, count(metadata.data) as "Metadata Values"
FROM files JOIN file_metadata USING(id_file)  JOIN metadata USING(id_metadata)
GROUP BY files.title;

-- Get Files with midnight in the sample text
SELECT  files.title, extra_data.type, extra_data.data
FROM files JOIN file_extra_data USING(id_file)  JOIN extra_data USING(id_extra_data)
WHERE extra_data.type = "Text Sample" AND extra_data.data LIKE "%midnight%";

-- Insert data to delete
INSERT INTO files ( `quickhash` , `title` , `description` , `id_mimetype` , `datetime_added` , `filename` , `filesize` ) VALUES
('0123456789012345678901','Delete Me!','I bet you can\'t delete me!','2',CURRENT_TIMESTAMP(),'delete.txt','4511');

-- Delete files with delete in the title
DELETE
FROM files 
WHERE title LIKE "%delete%";