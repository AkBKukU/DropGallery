<?php 
require_once('DGSettings.php');

class DropGallery
{
	private $DG;


    /*
     * constructor
     * 
     * Runs startup commands
     */
    public function __construct()
    {
    	$this->DG = new DropGalleryMain();
		$this->DG->addType(DGSettings::$GALERY_DISPLAY_TYPE);

	}

	/*
	 * Prints the choosen type of gallery to th html
	 */
	public function gallery()
	{
		echo $this->DG->viewFolder();
	}

}
?>        
