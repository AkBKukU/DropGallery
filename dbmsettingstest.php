<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Settings test page</title>
</head>

<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


require_once('DropGallery.php');
//$DG = new DropGallery();

		$DBM = new DBManager(DGSettings::$DB_HOST,DGSettings::$DB_ADMIN_USER,DGSettings::$DB_ADMIN_PASS);
		$DBM->getPatchForm();


?>
</body>

</html>
