<?php
/*============================================================*\
    Name: Gallery 2 Preview
    Description: Development version of the gallery.
\*============================================================*/

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once($_SERVER['DOCUMENT_ROOT'].'/content/classes/class.SiteMain.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/content/imported/getid3/getid3.php');
$SM = new SiteMain();
echo $SM->hPrintMeta("Gallery",'gallery.php');

$galleryDir = 'content/gallery-images';
if(isset($_GET['folder'])){
    
    $folderDir = '/'.$_GET['folder'];
    $folderDirB = $_GET['folder'].'/';
    $folderUrl = '&amp;folder='.$_GET['folder'];
    $upDir = strrev(substr(strrev($folderDir),strpos(strrev($folderDir),'/')+1 )  ) ;
    $buttons[] = array(
                    'url' => '?folder='.$upDir,
                    'text' => 'Up'
                    );
}else{
    
    $folderDir = '';
    $folderUrl = '';
    $folderDirB = '';
}
if($_SESSION['online'] == true && $_SESSION['currentUser'] == 'akbkuku'){
    $buttons[] = array(
                    'url' => 'settings.php?'.$folderUrl,
                    'text' => 'Settings'
                    );
            
}
echo $SM->hPrintHeader($buttons);

$config = new ConfigHandler('./'.'custom.cfg');

$thumbSize = "256";

if($header->view == "Small Mobile"){
    $thumbSize = "100";
}

//--Read images from directory
if ($handle = opendir($galleryDir.$folderDir)) {
    while (false !== ($entry = readdir($handle))) {
        if(strtoupper($entry) == 'INFO.TXT')
        {
            echo '<p class="object galleryDesc">'.str_replace("\n", "\n<br />", file_get_contents($galleryDir.$folderDir.'/'.$entry)).'</p>';
        }
        if (
            "PNG" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "JPG" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "SVG" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "JPEG" == strtoupper(substr($entry,strlen($entry)-4)) ||
            "MP4" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "GIF" == strtoupper(substr($entry,strlen($entry)-3)) 
        ){
            
                list($orig_width, $orig_height) = getimagesize($galleryDir.$folderDir.'/'.$entry);
                
                $size = round(filesize($galleryDir.$folderDir.'/'.$entry)/1024);
                
                //Gigbyte size
                if($size > 1048576)
                {
                    $sizetext = round($size/1048576,1)."GB";
                }
                //Megabyte size
                elseif($size > 1024)
                {
                    $sizetext = round($size/1024,1)."MB";
                }
                //kilobyte size
                else
                {
                    $sizetext = $size."kB";
                }
                if( 
                    !file_exists($galleryDir.$folderDir.'/'.substr($entry,0,strlen($entry)-3)."mp4") &&
                    "MP4" != strtoupper(substr($entry,strlen($entry)-3))
                )
                {
                    $files[] = array( 
                        'name' => $entry,
                        'x'    => $orig_width,
                        'y'    => $orig_height,
                        'size' => $sizetext
                    
                    );
                }elseif( "MP4" == strtoupper(substr($entry,strlen($entry)-3)) )
                {
                    $getID3 = new getID3;
                    $videoID3 = $getID3->analyze($galleryDir.$folderDir.'/'.$entry);

                    $files[] = array( 
                        'name' => $entry,
                        'x'    => $videoID3['video']['resolution_x'],
                        'y'    => $videoID3['video']['resolution_y'],
                        'time' => $videoID3['playtime_string'],
                        'pic'  => substr($entry,0,strlen($entry)-3).'jpg',
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

//--Check for titles in custom.cfg
for($c=0;$c < count($files);$c++){
    
    if( $config->getValue($files[$c]['name'].'-title') == 'Not found'){
        $files[$c]['propername'] = strtoupper(substr($files[$c]['name'],0,1)).substr($files[$c]['name'],1,strlen($files[$c]['name'])-5);
    }else{
        $files[$c]['propername'] = $config->getValue($files[$c]['name'].'-title');
    }
    
    if( $config->getValue($files[$c]['name'].'-description') == 'Not found'){
        $files[$c]['description'] = "";
    }else{
        $files[$c]['description'] = $config->getValue($files[$c]['name'].'-description');
    }
    
    if( $config->getValue($files[$c]['name'].'-hidden') == 'Not found'){
        $files[$c]['hidden'] = false;
    }else{
        $files[$c]['hidden'] = $config->getValue($files[$c]['name'].'-hidden');
    }
}
 echo '
                
                <ul class="menuList">
                    ';
foreach($folders as $folder){
    
    echo '
                
                    <li class="menu object objecthov">
                        <a href="?folder='.rawurlencode($folder['fullDir']).'">
                            <header><h3>'.substr($folder['name'],0,30).'</h3></header>
                            <span class="thumb folder"></span>
                                
                            <footer><p> '.$folder['numItems'].' Items </p></footer>
                        </a>
                    </li>
                
                ';
    
}

for($c=0;$c < count($files);$c++){
    if($files[$c]['hidden'] != 'true'){
        if( "MP4" != strtoupper(substr($files[$c]['name'],strlen($files[$c]['name'])-3)) )
        {
        echo '
                
                    <li class="menu object objecthov notvideo">
                        <a href="view.php?file='.rawurlencode($files[$c]['name']).$folderUrl.'">
                            <header><h3>'.substr(html_entity_decode($files[$c]['propername']),0,30).'</h3></header>
                            <img class="thumb" alt="'.$files[$c]['name'].'" 
                                title="'.$files[$c]['description'].'" 
                                src="view/image.png?file='.rawurlencode($files[$c]['name']).$folderUrl.'&amp;size='.$thumbSize.'">
                                
                            <footer><p>'.$files[$c]['x'].'x'.$files[$c]['y'].' - '.$files[$c]['size'].' '. strtoupper(substr($files[$c]['name'],strlen($files[$c]['name'])-3)).'</p></footer>
                        </a>
                    </li>
                
                ';
        }
        else
        {
        echo '
                
                    <li class="menu object objecthov">
                        <a href="view.php?file='.rawurlencode($files[$c]['name']).$folderUrl.'">
                            <header><h3>'.substr(html_entity_decode($files[$c]['propername']),0,30).'</h3></header>
                            <div class="vidHolder">
                                <img class="thumb" alt="'.$files[$c]['name'].'" 
                                    title="'.$files[$c]['description'].'" 
                                    src="view/image.png?file='.rawurlencode(substr($files[$c]['name'],0,strlen($files[$c]['name'])-3)).'jpg'.$folderUrl.'&amp;size='.$thumbSize.'">
                                <p class="vidTime">'.$files[$c]['time'].'</p>
                            </div>
                            <footer><p>'.$files[$c]['x'].'x'.$files[$c]['y'].' - '.$files[$c]['size'].' '. strtoupper(substr($files[$c]['name'],strlen($files[$c]['name'])-3)).'</p></footer>
                        </a>
                    </li>
                
                ';

        }
    }
}

echo '
                </ul>';

echo $SM->fPrintFooter();
?>		
