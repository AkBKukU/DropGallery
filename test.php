<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>1 2 3 4 5</title>
</head>

<body>




<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');



/*                                                                            *\
                              Drop Gallery
\*                                                                            */
require_once('DropGallery.php');
$DG = new DropGallery();

$DG->gallery();

/*                                                                            *\
                              /Drop Gallery
\*                                                                            */

?>		
</body>

</html>
