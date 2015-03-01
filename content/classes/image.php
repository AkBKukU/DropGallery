<?php

// File and new size
$name = $_GET['file'];
$size = 0;
$size = $_GET['size'];
if($size>0){
	$newwidth = $size;
	$newheight = $newwidth;
}
else{
	$size = 200;
}

$galleryDir = '../content/gallery-images';
if(isset($_GET['folder'])){
    
    $folderDir = '/'.$_GET['folder'];
    $buttons[] = array(
                    'url' => 'index.php',
                    'text' => 'Back'
                    );
}else{
    
    $folderDir = '';
}
$filename = $galleryDir.$folderDir.'/'.$name;
$percent = 5;

// Content type
header('Content-Type: image/png');

//Setting Sizes
list($orig_width, $orig_height) = getimagesize($filename);
if($orig_width < $orig_height){
	$newheight = $size;
	$newwidth =  $newheight * ($orig_width / $orig_height);
}
else{
	$newwidth = $size;
	$newheight =  $newwidth * ($orig_height / $orig_width);
}

//Sets resized Image up
$avatar = imagecreatetruecolor($newwidth, $newheight);
imagealphablending($avatar, false);
$col=imagecolorallocatealpha($avatar,255,255,255,127);
imagefilledrectangle($avatar,0,0,$size, $size,$col);
imagealphablending($avatar,true);

if("PNG" == strtoupper(substr($filename,strlen($filename)-3))){
    $avatar_raw = imagecreatefrompng($filename);
}
if("JPG" == strtoupper(substr($filename,strlen($filename)-3)) || "JPEG" == strtoupper(substr($filename,strlen($filename)-4))){
    $avatar_raw = imagecreatefromjpeg($filename);
}
if("GIF" == strtoupper(substr($filename,strlen($filename)-3))){
    $avatar_raw = imagecreatefromgif($filename);
}

//Resizes
imagecopyresampled($avatar, $avatar_raw, 0, 0, 0, 0, $newwidth, $newheight, $orig_width, $orig_height);


// Output
imagesavealpha($avatar,true);
imagepng($avatar);
?>
