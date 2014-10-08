<?php 
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once($_SERVER['DOCUMENT_ROOT'].'/content/classes/class.SiteMain.php');
$SM = new SiteMain();
echo $SM->hPrintMeta("Gallery",'gallery.php');
echo $SM->hPrintHeader($buttons);
$image_path="content/gallery-images/20140920-DSC_0044.jpg";

$imageHandle=fopen($image_path, 'r');
$firstk=fread($imageHandle, 4096);
fseek($imageHandle, filesize($image_path) - 4096);
$lastk=fread($imageHandle, 4096);
fclose($imageHandle);
$id=md5($firstk.$lastk);

$imageData = exif_read_data($image_path);

echo '
				<div class="one object" id="image">
					<header><h3>Age of Linux.png</h3></header>
					<img width="100%" height="100%" alt="" src="content/gallery-images/20140920-DSC_0044.jpg"></a>
					<p></p>
					<footer><p>20140920-DSC_0044.jpg - 1278x808 - 1.4MB PNG Views: 66</p></footer>
				</div>
';

$size = getimagesize ( $image_path, $info);
if(is_array($info)) 
{
    $iptc = iptcparse($info["APP13"]);
    foreach (array_keys($iptc) as $s) 
    {
        $c = count ($iptc[$s]);
        for ($i=0; $i <$c; $i++)
        {
            if ($s == "2#025")
            {
                $keywords[] = $iptc[$s][$i];
            }
        } 
    }
} 
echo 
'<div class="object"> 
	<header>
		<h3>Picture Info</h3>
	</header>
	<table>
		<tr>
		<th> Name </th>
		<td> '.$imageData["FileName"].' </td>
		</tr>

        <tr>
        <th> Id </th>
        <td> '.$id.' </td>
        </tr>

		<tr>
		<th> Size </th>
		<td> '.Utilities::filesizeInterpreter($imageData["FileSize"]).' </td>
		</tr>

		<tr>
		<th> MimeType </th>
		<td> '.$imageData["MimeType"].' </td>
		</tr>

		<tr>
		<th> Resolution </th>
		<td> '.$imageData["COMPUTED"]["Width"].'x'.$imageData["COMPUTED"]["Height"].' </td>
		</tr>

        <tr>
        <th> Keywords </th>
        <td> ';
foreach ($keywords as $value) {
    echo '<a href="search/?keyword='.htmlentities($value).'" >'.$value.'</a>, ';
}

echo 
' </td>
        </tr>
	</table>

</div>
';

echo 
'<div class="object"> 
	<header>
		<h3>Shot Info</h3>
	</header>
	<table>
		<tr>
		<th> Camera Make </th>
		<td> '.$imageData["Make"].' </td>
		</tr>

		<tr>
		<th> Camera Model </th>
		<td> '.$imageData["Model"].' </td>
		</tr>

		<tr>
		<th> Lens Info </th>
		<td> '.$imageData["UndefinedTag:0xA434"].' </td>
		</tr>

		<tr>
		<th> Focal Length </th>
		<td> '.$imageData["FocalLengthIn35mmFilm"].' mm </td>
		</tr>

		<tr>
		<th> Shutter Speed </th>
		<td> '.$imageData["ExposureTime"].' </td>
		</tr>

		<tr>
		<th> Apature </th>
		<td> f/'.round(floatval($imageData["FNumber"]),1).' </td>
		</tr>

		<tr>
		<th> ISO </th>
		<td> '.$imageData["ISOSpeedRatings"].' </td>
		</tr>

		<tr>
		<th> Taken </th>
		<td> '.$imageData["DateTimeOriginal"].' </td>
		</tr>

		<tr>
		<th> Software </th>
		<td> '.$imageData["Software"].' </td>
		</tr>
	</table>

</div>
';

echo "Image info dump<pre>";
var_dump($imageData);


echo "</pre>";
echo $SM->fPrintFooter();
?>		
