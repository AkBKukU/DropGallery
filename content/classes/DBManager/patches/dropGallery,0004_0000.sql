/*                                                                           *\
______________________________Patch Information________________________________

Description: Add Tag related procedures
Data Integrity: Safe

Required: Yes

\*                                                                           */
DROP PROCEDURE IF EXISTS add_file_tag;

CREATE PROCEDURE add_file_tag ( pinFileQH VARCHAR(32) , pinTag VARCHAR(256) )
BEGIN

DECLARE tagExists INTEGER;

SET @inFileID = getFileIDFromQH(pinFileQH);
SET @inTag = pinTag;


SELECT count(*) INTO tagExists 
FROM tags 
WHERE tags.name = @inTag;
IF (tagExists < 1) THEN
INSERT INTO tags (name) VALUES (@inTag);
END IF;

SELECT tags.id_tag INTO @tagID 
FROM tags 
WHERE tags.name = @inTag;

INSERT INTO file_tags (id_file , id_tag) VALUES ( @inFileID , @tagID );
END;



DROP PROCEDURE IF EXISTS tag_usage_report;

CREATE PROCEDURE tag_usage_report (  )
BEGIN
select tags.name, count(file_tags.id_tag) FROM tags JOIN file_tags USING (id_tag) GROUP BY tags.name;
END;

