<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//--How to use



class DropGalleryDBInterface{

    //--Declare Feilds
    public $mysqli;
    public $stmt;

    /*
     * Constructor 
     * 
     * Connects to database
     */
    public function __construct()
    {
    	$host = 'localhost';
    	$user = 'root';
    	$pass = 'password';
    	$database = 'dropGallery';
        //--Begin sql connection
        $this->mysqli = new mysqli($host, $user, $pass,$database);
        
        //--Error check and Output
        if ($this->mysqli->connect_errno) {
            echo "Error: Failed to connect to MySQL(" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
        }
        
        
    }
    
    public function checkForQuickHash($hash)
    {
    	$stmt = $this->mysqli->prepare("
SELECT files.quickhash 
FROM files
WHERE files.quickhash = ?
    	");
    	$error = $this->mysqli->error;
    	if($error == "")
    	{	
	    	$stmt->bind_param("s", $hash);
    		$stmt->execute();
    		$stmt->bind_result($quickhash);
    		$stmt->fetch();
    		if ($quickhash == $hash )
    		{
				return false;
    		}else{
				return true;
    		}
    		$stmt->close();
    	}else{
    		echo $error;
			return false;
    	}
    }


    
    public function checkMimetype($mimetype)
    {
    	$firstRun=true;
    	$tryAgain=false;
    	while( ($firstRun) || ($tryAgain) )
    	{
    		$firstRun=false;
	$stmt = $this->mysqli->prepare("
SELECT mimetypes.mimetype, mimetypes.id_mimetype
FROM mimetypes
WHERE mimetypes.mimetype = ?
	    	");
	    	$error = $this->mysqli->error;
	    	if($error == "")
	    	{	
		    	$stmt->bind_param("s", $mimetype);
	    		$stmt->execute();
	    		$stmt->bind_result($mimetypeBack, $idMimetype);
	    		$stmt->fetch();
	    		if ($mimetypeBack == $mimetype )
	    		{
					return $idMimetype;
	    		}else if(!$tryAgain){

	    			$tryAgain=true;
	    			$this->addMimetype($mimetype);
	    		}else{
	    			echo $error;
					return false;
	    		}
	    		$stmt->close();
	    	}else{
	    		echo $error;
				return false;
	    	}
    	}
    	
    }

    
    public function addMimetype($mimetype)
    {
    	$stmt = $this->mysqli->prepare("
INSERT INTO mimetypes(mimetype) VALUES 
(?);
    	");
    	$error = $this->mysqli->error;
    	if($error == "")
    	{	
	    	$stmt->bind_param("s", $mimetype);
    		$stmt->execute();
    		$stmt->close();
    	}else{
    		echo $error;
			return false;
    	}
    }
    
    public function addNewFile($id,$title,$description , $mimetype , $filename , $filesize)
    {
    	$id_mimetype = $this->checkMimetype($mimetype);
    	$stmt = $this->mysqli->prepare("
INSERT INTO files ( quickhash , title , description , id_mimetype , datetime_added , filename , filesize ) VALUES
( ?,?,?,?,CURRENT_TIMESTAMP(),?,?);
    	");
    	$error = $this->mysqli->error;
    	if($error == "")
    	{	
	    	$stmt->bind_param("sssisi", $id,$title,$description , $id_mimetype , $filename , $filesize);
    		$stmt->execute();
    		$stmt->close();
    	}else{
    		echo $error;
			return false;
    	}
    }

    
    public function getFileBasic($id)
    {
    	$stmt = $this->mysqli->prepare('
SELECT files.title
FROM files 
WHERE files.quickhash = ?
');
    	$error = $this->mysqli->error;
    	if($error == "")
    	{	
	    	$stmt->bind_param("s", $id);
    		$stmt->execute();
    		$stmt->bind_result($title);
    		$stmt->fetch();
    		$stmt->close();
    		return $title;
    	}else{
    		echo $error;
			return false;
    	}
    }



    /*
     * destructor 
     * 
     * Closes database conection
     */
    function __destruct() {
        
        //--Disconnect from database
        mysqli_close($this->mysqli);
    }
}

?>
