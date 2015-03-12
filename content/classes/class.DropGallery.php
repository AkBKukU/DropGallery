<?php
require_once('class.Utilities.php');
require_once('class.FileInfo.php');
require_once('class.ThumbGen.php');
require_once('class.HTMLGenerator.php');
require_once('getid3/getid3.php');
require_once('class.DropGalleryDBInterface.php');
//require_once('class.MySQLGalleryInterface.php');

class DropGallery{

    private $htmlPath = '/content/gallery-images';
    private $galleryPath;
    private $dirSubFolders;
    private $dirContents;
    private $db;

    public static $debugText = "";
    public static $debugEnable = true;

    private static $start_time;

    /*
     * constructor
     * 
     * Runs startup commands
     */
    public function __construct()
    {
    	DropGallery::$start_time = microtime(true);
    	$this->db = new DropGalleryDBInterface();
        $this->galleryPath = $_SERVER['DOCUMENT_ROOT'].$this->htmlPath;

        DropGallery::debug("Opening: ".$this->galleryPath);

        $this->getFileList();
        DropGallery::debug("Finished Filelist");
    }


    /*
     * Sets the display type for the gallery. 
     * Currently only flow is availible
     */
    public function addType($type)
    {
		switch (strtoupper($type)) {
			case 'FLOW':
echo '
<link rel=StyleSheet href="content/styles/DG.Flow.css" type="text/css">
<script type="text/javascript" src="content/js/DG.Flow.js" ></script>
';
				break;
			
			default:
				# code...
				break;
		}
    }

    /*
     * viewFolderNoDB
     * 
     * Loads a folder for viewing without using database
     */
    public function viewFolderNoDB()
    {
        return HTMLGenerator::flow($this->dirContents, $this->dirSubFolders);
    }


    /*
     * quickhash
     *
     * A cheap md5 hash that only uses the first and last KB and size of the file to save time
     */
    public function quickhash($filepath)
    {
    	$size = filesize($filepath);

		$fileHandle=fopen($filepath, 'r');
        $firstk=fread($fileHandle, 4096);
        fseek($fileHandle, $size - 4096);
        $lastk=fread($fileHandle, 4096);
        fclose($fileHandle);
        
        return md5($firstk.$size.$lastk);
    }


    /*
     * getFileList
     * 
     * Runs through files in folder and checks the against DB.
	 * 
     * TODO - Simplify this.
     */
    public function getFileList()
    {   
        $folders;
        $files;

        DropGallery::debug("Start get files");

        if ( ! file_exists($this->galleryPath) ) 
        {
            echo "File not found: ".$this->galleryPath;
            return;

        }elseif( ! is_dir($this->galleryPath))
        {

            echo "Path not a folder: ".$this->galleryPath;
            return;
        }
                
        $galdir = new DirectoryIterator($this->galleryPath);
        foreach( $galdir as $entry )
        {    
            if ( ! $entry->isDot() ) 
            {
                if ( $entry->isFile() )
                {

		        	$id=$this->quickhash($this->galleryPath.'/'.$entry->getFilename());
		        	$newFileTest = $this->db->checkForQuickHash($id);
		        	if($newFileTest)
		        	{
		        		$this->addNewFile($id,$entry->getFilename());
		        	}else
		        	{
		        		$this->dirContents[] = $this->getFileInfo($id,$entry->getFilename());
		        	}

                }
                elseif( $entry->isDir() )
                {
                    
                    //--Generate sub-folder list
                    $folderItems = 0;
                    $subdir = new DirectoryIterator($this->galleryPath.'/'.$entry);
                    foreach( $subdir as $subentry )
                    {    
                        if ( ! $subentry->isDot() ) 
                        {
                            $folderItems++;
                        }
                    }
                    unset($subdir);
                    $this->dirSubFolders[] = array( 
                                'name' => basename($subentry->getPath()),
                                'numItems'    => $folderItems
                            
                            );
                }
            }
        }

        DropGallery::debug("End get files");
    }




    /*
     * Gets file info amd sends it to the database interface to be added
     */
    public function addNewFile($id, $newFile)
    {
        
		$image = new FileInfo($this->galleryPath.'/'.$newFile);
		$this->db->addNewFile($id,$image->getTitle(),$image->getDescription() , $image->getMimetype() , $image->getFilename() , $image->getFilesize());
    }

    /*
     * Gets basic file info from db interface and returns it in easy to use variable
     */
    public function getFileInfo($id,$name)
    {
		$fileData = $this->db->getFileBasic($id);

		return array( 
            'name' => $name,
            'qhash' => $id,
            'title' => $fileData["title"],
            'mimetype' => $fileData["mimetype"],
            'path' => $this->galleryPath.'/'.$name,
            'htmlpath'    => $this->htmlPath.'/'.$name
        );
    }


    /*
     * Static debug function to add a message to the debug output
     */
    public static function debug($text)
    {
    	DropGallery::$debugText .= '<span class="debugTime">['.(microtime(true) - DropGallery::$start_time).']</span> '."\t".$text."\n";
    }

    /*
     * destructor 
     * 
     * Tests if debug should be show and prints it if so
     */
    function __destruct() {
        
        DropGallery::debug("Fin");
        if ( DropGallery::$debugEnable )
        {
	        echo '<pre id="dropGalleryDebug">'.DropGallery::$debugText.'</pre>';
        }
        
    }


    public function getGalleryPath()
    {
        return $this->galleryPath;
    }


}
?>
