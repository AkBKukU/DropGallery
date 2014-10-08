<?php 
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once($_SERVER['DOCUMENT_ROOT'].'/content/classes/class.SiteMain.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/content/imported/getid3/getid3.php');
$SM = new SiteMain();


$name = $_GET['file'];

$config = new ConfigHandler(__DIR__.'/'.'custom.cfg');

$galleryDir = 'content/gallery-images';
$htmlGalleryDir = '/gallery/content/gallery-images/';
if(isset($_GET['folder'])){
    
    $folderDir = '/'.$_GET['folder'];
    $folder = $_GET['folder'];
    $folderUrlDir = $_GET['folder'].'/';
    $folderDirB = $_GET['folder'].'/';
    $folderUrl = '&amp;folder='.$_GET['folder'];
    $buttons[] = array(
                    'url' => 'index.php?folder='.$_GET['folder'],
                    'text' => 'Back'
                    );
}else{
    
$buttons[] = array(
                'url' => '/gallery/',
                'text' => 'Back'
                );
    $folderDir = '';
    $folder = '';
    $folderUrlDir = '';
    $folderUrl = '';
    $folderDirB = '';
}

//--Read images from directory
if ($handle = opendir($galleryDir.$folderDir)) {
    while (false !== ($entry = readdir($handle))) {
        if ("PNG" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "JPG" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "SVG" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "JPEG" == strtoupper(substr($entry,strlen($entry)-4)) ||
            "GIF" == strtoupper(substr($entry,strlen($entry)-3)) || 
            "MP4" == strtoupper(substr($entry,strlen($entry)-3)) ){
            
                list($orig_width, $orig_height) = getimagesize($galleryDir.$folderDir.'/'.$entry);
                
                $size = round(filesize($galleryDir.$folderDir.'/'.$entry)/1024);
                if($size > 1024){$sizetext = round($size/1024,1)."MB";}else{$sizetext = $size."kB";}
                    
                    if( $config->getValue($entry.'-hidden') == 'Not found'){
                        $hidden = false;
                    }else{
                        $hidden = $config->getValue($entry.'-hidden');
                    }
                    
                    if($hidden != 'true'){
                    
                    
                        $files[] = array( 
                            'name' => $entry,
                            'x'    => $orig_width,
                            'y'    => $orig_height,
                            'size' => $sizetext
                        
                        );
                    }
                    
                
        }
        if(is_dir($galleryDir.$folderDir.'/'.$entry) && $entry != '.' && $entry != '..'){
            
            //--Generate sub-folder list
            $x = 0;
            $dir = new DirectoryIterator($galleryDir.$folderDir.'/'.$entry);
                foreach($dir as $file ){
                    
                    if( $config->getValue($file.'-hidden') == 'Not found'){
                        $hidden = false;
                    }else{
                        $hidden = $config->getValue($file.'-hidden');
                    }
                    
                    if($hidden != 'true'){$x ++;}
            }
            unset($file);
            $folders[] = array( 
                        'name' => $entry,
                        'fullDir' => $folderDirB.$entry,
                        'numItems'    => $x-2
                    
                    );
        }
        //echo $entry."<br />\n";
    }
    closedir($handle);
}
sort($files);

for($c=0;$c < count($files);$c++){
    
    if($name == $files[$c]['name']){$imageKey = $c;}
}
$folderNav = '';
if($imageKey != 0){
    
    $folderNav .= '<a class="objecthov" href="view.php?file='.$files[0]['name'].$folderUrl.'" title="First image in folder">|&lt;</a> ';
    $folderNav .= '<a class="objecthov" href="view.php?file='.$files[$imageKey-1]['name'].$folderUrl.'" title="Previous image in folder">&lt;</a> ';
    $leftShortcut = '
    shortcut.add("Left",function() {
                    window.location.href = "view.php?file='.$files[$imageKey-1]['name'].html_entity_decode($folderUrl).'#image";
            });';
}
if($imageKey != count($files)-1){
    
    $folderNav .= '<a class="objecthov" href="view.php?file='.$files[$imageKey+1]['name'].$folderUrl.'" title="Next image in folder">&gt;</a> ';
    $folderNav .= '<a class="objecthov" href="view.php?file='.$files[count($files)-1]['name'].$folderUrl.'" title="Last image in folder">&gt;|</a> ';
     $rightShortcut = '
    shortcut.add("Right",function() {
                    window.location.href = "view.php?file='.$files[$imageKey+1]['name'].html_entity_decode($folderUrl).'#image";
            });';
}


list($file['x'], $file['y']) = getimagesize($galleryDir.$folderDir.'/'.$name);
$size = round(filesize($galleryDir.$folderDir.'/'.$name)/1024);
if($size > 1024){$sizetext = round($size/1024,1)."MB";}else{$sizetext = $size."kB";}
$file['size'] = $sizetext;


$size = $_GET['size'];

if( $config->getValue($name.'-title') == 'Not found'){
    $file['propername'] = $name;
}else{
    $file['propername'] = $config->getValue($name.'-title');
}

if( $config->getValue($name.'-description') == 'Not found'){
    $file['description'] = "";
}else{
    $file['description'] = $config->getValue($name.'-description');
}

if( $config->getValue($name.'-hidden') == 'Not found'){
    $file['hidden'] = "false";
}else{
    $file['hidden'] = $config->getValue($name.'-hidden');
}


if( $config->getValue($name.'-viewcount') == 'Not found'){
    $config->setValue($name.'-viewcount',"1");
    $viewcount = 1;
}else{
    $viewcount = intval( $config->getValue($name.'-viewcount') );
    $viewcount++;
    $config->setValue($name.'-viewcount',$viewcount);
}
$isTooLarge = "false";

list($orig_width, $orig_height) = getimagesize($galleryDir.$folderDir.'/'.$name); 
$frameWidth = $orig_width;
if($size == 'double'){$frameWidth = ($orig_width *2);}
else{
    if("Yes" == $_GET['sizeoveride']){;}else{
        if($orig_width > 1200){$frameWidth = 1200; $isTooLarge = "true";}
    }
}



echo $SM->hPrintMeta("Gallery",'gallery.php',$file['propername'],'<link href="//vjs.zencdn.net/4.5/video-js.css" rel="stylesheet">
<script src="//vjs.zencdn.net/4.5/video.js"></script>');
                
if($size != 'double'){
    $buttons[] = array(
                    'url' => '?file='.$name.'&amp;size=double&amp;folder='.$_GET['folder'],
                    'text' => 'Double'
                    );
}
if("true" == $isTooLarge){
    $buttons[] = array(
                    'url' => '?file='.$name.'&amp;sizeoveride=Yes&amp;folder='.$_GET['folder'],
                    'text' => 'Full Size'
                    );
}
if("Yes" == $_GET['sizeoveride'] || $size == 'double'){
    $buttons[] = array(
                    'url' => '?file='.$name.'&amp;folder='.$_GET['folder'],
                    'text' => 'Normal Size'
                    );
    
}
echo $SM->hPrintHeader($buttons);

?>        
<?php
$filename = $htmlGalleryDir.$folderUrlDir.rawurlencode($name);



echo '
<script type="text/javascript" src="shortcuts.js"></script>
<script type="text/javascript">
'.$leftShortcut.'
'.$rightShortcut.'
</script>
                <div class="object" id="galleryNav">
                    <p>'.$folderNav.'</p>
                </div>
                ';
        
    
    

if($file['hidden'] != 'true'){



    if($size == 'double'){
        
        if($orig_width > $orig_height){
            $source = 'view/image.png?file='.$folderUrlDir.$name.'&amp;size='.($orig_width * 2);
        }else{
            $source = 'view/image.png?file='.$folderUrlDir.$name.'&amp;size='.($orig_height * 2);
        
        }

    }else{
        
        if($_SESSION['pageView'] == 'Small Mobile' && "true" == $isTooLarge){
            $source = 'view/image.png?file='.$folderUrlDir.$name.'&amp;size=400';
        
        }elseif($_SESSION['pageView'] == 'Large Mobile' && "true" == $isTooLarge){
            $source = 'view/image.png?file='.$folderUrlDir.$name.'&amp;size=600';
        
        }elseif("true" == $isTooLarge){
            $source = 'view/image.png?file='.$folderUrlDir.$name.'&amp;size=1200';
        
        }else{
            $source =$filename;
        }
    }
    if ( strtoupper(substr($name,strlen($name)-3)) != 'MP4')
	{
            echo '
                <div class="one object" id="image">
                    <header><h3>'.$file['propername'].'</h3></header>
                    <a href="'.$source.'"><img width="100%" height="100%" alt="'.$file['description'].'" src="'.$source.'"></a>
                    <p>'.$file['description'].'</p>
                    <footer><p>'.$folderUrlDir.$name.' - '.$file['x'].'x'.$file['y'].' - '.$file['size'].' '. strtoupper(substr($name,strlen($name)-3)).' Views: '.$viewcount.'</p></footer>
                </div>
                
                ';

	}
	else
	{
                    $getID3 = new getID3;
                    $videoID3 = $getID3->analyze($galleryDir.$folderDir.'/'.$name);

			 echo '
                 <div class="one object" id="image">
                     <header><h3>'.$file['propername'].'</h3></header>
                     <video id="example_video_1" class="video-js vjs-default-skin vjs-big-play-centered"
			  controls preload="auto" width="100%"  height="720px"
                poster="'.$htmlGalleryDir.$folderUrlDir.substr($name,0,strlen($name)-3).'jpg'.'"
			  data-setup=\'{"controls": true, "autoplay": false, "preload": "auto"}\'>
			 <source src="'.$htmlGalleryDir.$folderUrlDir.$name.'" type=\'video/mp4\' />
			</video>
<p>'.$file['description'].': <a href="'.$htmlGalleryDir.$folderUrlDir.$name.'">Save</a></p>
<footer><p>'.$folderUrlDir.$name.' - Duration: '.$videoID3['playtime_string'].' - '.$videoID3['video']['resolution_x'].'x'.$videoID3['video']['resolution_y'].' - '.$file['size'].' '. strtoupper(substr($name,strlen($name)-3)).' Views: '.$viewcount.'</p></footer>
                </div>
			              
							                  ';

	}
}

echo $SM->fPrintFooter();
?>        
