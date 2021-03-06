<?php 
require_once('DGSettings.php');

class DropGallery
{
	private $DG;
	private $DBM;


    /*
     * constructor
     * 
     * Runs startup commands
     */
    public function __construct()
    {
		$this->DBM = new DBManager(DGSettings::$DB_HOST,DGSettings::$DB_ADMIN_USER,DGSettings::$DB_ADMIN_PASS);
    	$this->DG = new DropGalleryMain();
		$this->DG->addType(DGSettings::$GALERY_DISPLAY_TYPE);
		//$this->DBM->displayPublicAlert("Open Settings","dbmsettingstest.php");

		$DBM = new DBManager(DGSettings::$DB_HOST,DGSettings::$DB_ADMIN_USER,DGSettings::$DB_ADMIN_PASS);
		$DBM->displayPublicAlert("Go to Manager","dbmsettingstest.php");
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
