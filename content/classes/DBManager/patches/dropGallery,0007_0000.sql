/*                                                                           *\
______________________________Patch Information________________________________

Description: Data Access procedures
Data Integrity: Safe

Required: Yes

\*                                                                           */


/** add_file
  * Inserts a new file into the files table
  */
DROP PROCEDURE IF EXISTS add_file;
CREATE PROCEDURE add_file ( inQuickhash VARCHAR(32), inTitle VARCHAR(80) , inDescription VARCHAR(2500), inId_mimetype INTEGER, inFilename VARCHAR(200), inFilesize INTEGER  )
BEGIN

INSERT INTO files ( quickhash , title , description , id_mimetype , datetime_added , filename , filesize ) VALUES
( inQuickhash,inTitle,inDescription,inId_mimetype,CURRENT_TIMESTAMP(),inFilename,inFilesize);

END;


/** add_mimetype
  * Inserts a new mimtype
  */
DROP PROCEDURE IF EXISTS add_mimetype;
CREATE PROCEDURE add_mimetype ( inMimetype VARCHAR(150) )
BEGIN

INSERT INTO mimetypes(mimetype) VALUES (inMimetype);

END;


/** get_mimetype
  * Return the mimetype id and name
  */
DROP PROCEDURE IF EXISTS get_mimetype;
CREATE PROCEDURE get_mimetype ( inMimetype VARCHAR(150) )
BEGIN

SELECT mimetypes.mimetype, mimetypes.id_mimetype
FROM mimetypes
WHERE mimetypes.mimetype = inMimetype;

END;


/** check_file_quickhash
  * Sends back the same info if it exists
  */
DROP PROCEDURE IF EXISTS check_file_quickhash;
CREATE PROCEDURE check_file_quickhash ( inQuickhash VARCHAR(32) )
BEGIN

SELECT files.quickhash 
FROM files
WHERE files.quickhash = inQuickhash;

END;


/** get_file_basic
  * Returns basic information about a file
  */
DROP PROCEDURE IF EXISTS  get_file_basic;
CREATE PROCEDURE get_file_basic ( inQuickhash VARCHAR(32) )
BEGIN

SELECT files.title, mimetypes.mimetype
FROM files JOIN mimetypes USING(id_mimetype)
WHERE files.quickhash = inQuickhash

END;


/** get_file_tags
  * A subquery to get the tags associated with a file
  */
DROP PROCEDURE IF EXISTS  get_file_tags;
CREATE PROCEDURE get_file_tags ( inQuickhash VARCHAR(32) )
BEGIN

select distinct files.id_file, files.title , contags.ctags from 
files join file_tags using(id_file) join 
(
	select group_concat(tags.name) as ctags, file_tags.id_file from tags join file_tags using(id_tag) group by id_file
) as contags on (contags.id_file = file_tags.id_file)
WHERE files.quickhash = inQuickhash;

END;

