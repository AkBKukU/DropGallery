<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once($_SERVER['DOCUMENT_ROOT'].'/content/classes/class.SiteMain.php');
$SM = new SiteMain();
echo $SM->hPrintMeta("Gallery",'gallery.php');
echo getcwd();
$galleryDir = 'content/gallery-images/';
$config = new ConfigHandler(getcwd().'/'.'custom.cfg');
if(isset($_GET['folder'])){
    
    $folderDir = '/'.$_GET['folder'];
    $folderDirB = $_GET['folder'].'/';
    $folderUrl = '&amp;folder='.$_GET['folder'];
}else{
    
    $folderDir = '';
    $folderUrl = '';
    $folderDirB = '';
}
    $buttons[] = array(
                    'url' => '/gallery/?'.$folderUrl,
                    'text' => 'Back'
                    );

echo $SM->hPrintHeader($buttons);

//--Read images from directory
if ($handle = opendir($galleryDir.$folderDir)) {
    while (false !== ($entry = readdir($handle))) {
        if ("PNG" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "JPG" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "JPEG" == strtoupper(substr($entry,strlen($entry)-4)) ||
            "MP4" == strtoupper(substr($entry,strlen($entry)-3)) ||
            "GIF" == strtoupper(substr($entry,strlen($entry)-3)) ){
            
                list($orig_width, $orig_height) = getimagesize($galleryDir.$entry);
                
                $size = round(filesize($galleryDir.$entry)/1024);
                if($size > 1024){$sizetext = round($size/1024,1)."MB";}else{$sizetext = $size."kB";}
                    $files[] = array( 
                        'name' => $entry,
                        'x'    => $orig_width,
                        'y'    => $orig_height,
                        'size' => $sizetext
                    
                    );
        }
    }
    closedir($handle);
}

sort($files);



if($_REQUEST['action']=='update' && $_SESSION['online'] == true){
    for($c=0;$c < count($files);$c++){
        
        if($_REQUEST[$c.'-propername'] != false){
            
            $config->setValue($files[$c]['name'].'-title',htmlentities($_REQUEST[$c.'-propername'], ENT_QUOTES));
        }
        
        if($_REQUEST[$c.'-description'] != false){
        
            $config->setValue($files[$c]['name'].'-description',htmlentities($_REQUEST[$c.'-description'], ENT_QUOTES));
        }
        
        if($_REQUEST[$c.'-hidden'] != false){
        
            $config->setValue($files[$c]['name'].'-hidden',$_REQUEST[$c.'-hidden']);
        }elseif($_REQUEST[$c.'-hidden'] == false){
        
            $config->setValue($files[$c]['name'].'-hidden',$_REQUEST[$c.'-hidden']);
        }
        
        if($_REQUEST[$c.'-delete'] != false){
            chown($galleryDir.$files[$c]['name'], 'akbkuku');
            chmod ( $galleryDir.$files[$c]['name'] , 777 );
            
            if(unlink($galleryDir.$files[$c]['name'])){
                echo 'Deleted: '.$galleryDir.$files[$c]['name'];
            }else{
                echo 'Failed to Delete: '.$galleryDir.$files[$c]['name'];
            }
        }
        
    }
    //echo '<pre>';
    //echo $config->dumpValues();
    //echo '</pre>';
}




//--Check for values in custom.cfg
for($c=0;$c < count($files);$c++){
    
    if( $config->getValue($files[$c]['name'].'-title') == 'Not found'){
        $files[$c]['propername'] = "";
    }else{
        $files[$c]['propername'] = $config->getValue($files[$c]['name'].'-title');
    }
    
    if( $config->getValue($files[$c]['name'].'-description') == 'Not found'){
        $files[$c]['description'] = "";
    }else{
        $files[$c]['description'] = $config->getValue($files[$c]['name'].'-description');
    }
    
    if( $config->getValue($files[$c]['name'].'-hidden') == 'Not found'){
        $files[$c]['hidden'] = "false";
    }else{
        $files[$c]['hidden'] = $config->getValue($files[$c]['name'].'-hidden');
    }
}



echo '
        
                
                <form method="post" class="inputTable" action="settings.php?action=update'.$folderUrl.'">
                    <table class="inputTable">
                        <tr>
                            <th colspan="2" class="inputHeader"><h3 class="inputHeader">File Settings</h3></th>
                        </tr>';

if($_SESSION['online'] == true){
           
    for($c=0;$c < count($files);$c++){
        echo '
                
                        <tbody>
                            <tr>
                                <th>File</th>
                                <td><p>'.$files[$c]['name'].'</p></td>
                            </tr>
                            
                            <tr>
                                <th><label for="'.$c.'-propername">Proper Name</label></th>
                                <td><input id="'.$c.'-propername" name="'.$c.'-propername" type="text" value="'.$files[$c]['propername'].'"></td>
                            </tr>
                            
                            <tr>
                                <th><label for="'.$c.'-description">Description</label></th>
                                <td><input id="'.$c.'-description" name="'.$c.'-description" type="text" value="'.$files[$c]['description'].'"></td>
                            </tr>
                            
                            <tr>
                                <th><label for="'.$c.'-hidden">Hidden</label></th>
                                <td><input id="'.$c.'-hidden" name="'.$c.'-hidden" type="checkbox" value="true" '; if($files[$c]['hidden']=='true'){echo ' checked="checked"';} echo ' ></td>
                            </tr>
                            
                            <tr>
                                <th><label for="'.$c.'-delete">Delete?</label></th>
                                <td><input id="'.$c.'-delete" name="'.$c.'-delete" type="checkbox" value="true" ></td>
                            </tr>
                        </tbody>
                
                ';
    }

    echo '
                        <tbody>
                            <tr>
                                <td colspan="2" class="savebutton"><input type="submit" value="Save Changes"  class="button"></td>
                            </tr>
                        </tbody>
                    </table>
                </form>';
                     
}else{
    echo '<p>You need to be logged in to make changes</p>';
}

echo $SM->fPrintFooter();
?>
