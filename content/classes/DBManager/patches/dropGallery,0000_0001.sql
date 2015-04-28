/*                                                                           *\
______________________________Patch Information________________________________

Description: Quickhash to ID function
Data Integrity: Safe

Required: Yes

\*                                                                           */
DROP FUNCTION IF EXISTS getFileIDFromQH;

CREATE FUNCTION getFileIDFromQH ( qh VARCHAR(32) )
RETURNS INTEGER
READS SQL DATA
BEGIN

DECLARE fileID INTEGER;

SET @qhash = qh;

SELECT files.id_file INTO fileID 
FROM files
WHERE files.quickhash = @qhash;

RETURN fileID;
END;
