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
require_once('content/classes/class.DropGallery.php');
$image_path="content/gallery-images/loaded.jpg";

$image = new FileInfo($image_path);
$DG = new DropGallery();

$imageHandle=fopen($image_path, 'r');
$firstk=fread($imageHandle, 4096);
fseek($imageHandle, filesize($image_path) - 4096);
$lastk=fread($imageHandle, 4096);
fclose($imageHandle);
$id=md5($firstk.$lastk);


$imageHandle=fopen("content/gallery-images/noise-red.jpg", 'r');
$firstk=fread($imageHandle, 4096);
fseek($imageHandle, filesize($image_path) - 4096);
$lastk=fread($imageHandle, 4096);
fclose($imageHandle);
$idred=md5($firstk.filesize("content/gallery-images/noise-red.jpg").$lastk);



$imageHandle=fopen("content/gallery-images/noise-blue.jpg", 'r');
$firstk=fread($imageHandle, 4096);
fseek($imageHandle, filesize($image_path) - 4096);
$lastk=fread($imageHandle, 4096);
fclose($imageHandle);
$idblue=md5($firstk.filesize("content/gallery-images/noise-blue.jpg").$lastk);

echo '
<link rel=StyleSheet href="content/styles/DG.Flow.css" type="text/css">
<script type="text/javascript" src="content/js/DG.Flow.js" ></script>
';

echo $DG->viewFolderNoDB();

echo 
'

<h1> Red: '.$idred.', Blue: '.$idblue.'</h1>

<div class="object" onload="hidedgBigView()" > 
	<header>
		<h3>Picture Info</h3>
	</header>
	<table>
		<tr>
		<th> Name </th>
		<td> '.$image->getTitle().' </td>
		</tr>

		<tr>
		<th> Description </th>
		<td> '.$image->getDescription().' </td>
		</tr>


        <tr>
        <th> Id </th>
        <td> '.$id.' </td>
        </tr>

		<tr>
		<th> Size </th>
		<td> '.Utilities::filesizeInterpreter($image->getFilesize()).' </td>
		</tr>

		<tr>
		<th> MimeType </th>
		<td> '.$image->getMimetype().' </td>
		</tr>

		<tr>
		<th> Resolution </th>
		<td> '.$image->getMetadata()['width'].'x'.$image->getMetadata()['height'].' </td>
		</tr>

        <tr>
        <th> Keywords </th>
        <td> ';
foreach ($image->getTags() as $value) 
{
    echo '<a href="search/?keyword='.htmlentities($value).'" >'.$value.'</a>, ';
}

echo 
' </td>
        </tr>
	</table>

</div>
';
$fstop = $image->getMetadata()['exfi:FNumber'];
if(preg_match('/(\d+)(?:\s*)([\/])(?:\s*)(\d+)/', $fstop, $matches) !== FALSE){

    $fstop = $matches[1] / $matches[3];
}
echo 
'<div class="object"> 
	<header>
		<h3>Shot Info</h3>
	</header>
	<table>
		<tr>
		<th> Camera Make </th>
		<td> '.$image->getMetadata()['exfi:Make'].' </td>
		</tr>

		<tr>
		<th> Camera Model </th>
		<td> '.$image->getMetadata()['exfi:Model'].' </td>
		</tr>

		<tr>
		<th> Lens Info </th>
		<td> '.$image->getMetadata()['exfi:UndefinedTag:0xA434'].' </td>
		</tr>

		<tr>
		<th> Focal Length </th>
		<td> '.$image->getMetadata()['exfi:FocalLengthIn35mmFilm'].' mm </td>
		</tr>

		<tr>
		<th> Shutter Speed </th>
		<td> '.$image->getMetadata()['exfi:ExposureTime'].' </td>
		</tr>

		<tr>
		<th> Apature </th>
		<td> f/'.$fstop.' </td>
		</tr>

		<tr>
		<th> ISO </th>
		<td> '.$image->getMetadata()['exfi:ISOSpeedRatings'].' </td>
		</tr>

		<tr>
		<th> Taken </th>
		<td> '.$image->getMetadata()['exfi:DateTimeOriginal'].' </td>
		</tr>

		<tr>
		<th> Software </th>
		<td> '.$image->getMetadata()['exfi:Software'].' </td>
		</tr>
	</table>

</div>
';

//echo "Image info dump<pre>";
//var_dump($imageData);


echo "</pre>";
?>		
</body>

</html>
