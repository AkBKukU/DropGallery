<?php
require_once('class.Utilities.php');
require_once('class.FileInfo.php');
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

    /*
     * constructor
     * 
     * Runs startup commands
     */
    public function __construct()
    {
    	$this->db = new DropGalleryDBInterface();
        $this->galleryPath = $_SERVER['DOCUMENT_ROOT'].$this->htmlPath;

        $this->getFileList();
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
     * constructor
     * 
     * Runs startup commands
     */
    public function getQuickIds()
    {
        $count = count($this->dirContents);
        for ($i = 0; $i < $count; $i++) 
        {
            $imageHandle=fopen($this->dirContents[$i]['path'], 'r');
            $firstk=fread($imageHandle, 4096);
            fseek($imageHandle, filesize($this->dirContents[$i]['path']) - 4096);
            $lastk=fread($imageHandle, 4096);
            fclose($imageHandle);
            $this->dirContents[$i]['id']=md5($firstk.$lastk);
        }
        $count = count($this->dirSubFolders);
        for ($i = 0; $i < $count; $i++) 
        {
            $imageHandle=fopen($this->dirSubFolders[$i]['path'], 'r');
            $firstk=fread($imageHandle, 4096);
            fseek($imageHandle, filesize($this->dirSubFolders[$i]['path']) - 4096);
            $lastk=fread($imageHandle, 4096);
            fclose($imageHandle);
            $this->dirSubFolders[$i]['id']=md5($firstk.$lastk);
        }
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
     * constructor
     * 
     * Runs startup commands
     */
    public function getFileList()
    {   
        $folders;
        $files;


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
        	if (!is_dir($this->galleryPath.'/'.$entry->getFilename()))
        	{
	        	$id=$this->quickhash($this->galleryPath.'/'.$entry->getFilename());
	        	$newFileTest = $this->db->checkForQuickHash($id);
	        	if($newFileTest)
	        	{
	        		$this->addNewFile($id,$entry->getFilename());
	        	}
        	}
            if ( ! $entry->isDot() ) 
            {
                if ( $entry->isFile() )
                {
                    $info = pathinfo($entry->getPath());
                    $this->dirContents[] = array( 
                                'name' => $entry->getFilename(),
                                'title' => $entry->getFilename(),
                                'path' => $this->galleryPath.'/'.$entry->getFilename(),
                                'htmlpath'    => $this->htmlPath.'/'.$entry->getFilename()
                            );
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
    }


    /*
     * constructor
     * 
     * Runs startup commands
     */
    public function getGalleryPath()
    {
        return $this->galleryPath;
    }



    /*
     * constructor
     * 
     * Runs startup commands
     */
    public function addNewFile($id, $newFile)
    {
        
		$image = new FileInfo($this->galleryPath.'/'.$newFile);
		$this->db->addNewFile($id,$image->getTitle(),$image->getDescription() , $image->getMimetype() , $image->getFilename() , $image->getFilesize());
    }
}
?>
