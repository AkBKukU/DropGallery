<?php

//     Includes
        require_once('content/classes/class.Utilities.php');
        require_once('content/classes/class.FileInfo.php');
        require_once('content/classes/class.ThumbGen.php');
        require_once('content/classes/class.HTMLGenerator.php');
        require_once('content/classes/getid3/getid3.php');
        require_once('content/classes/class.DropGalleryDBInterface.php');
		require_once('content/classes/class.DropGallery.php');

class DGSettings{


/*                                                                            *\
                            Drop Gallery Settings
\*                                                                            */

//            Gallery HTML location 
public static $GALERY_HTML_LOCATION = "/content/gallery-images/";

//            Gallery display type 
//            Types: flow
public static $GALERY_DISPLAY_TYPE = "flow";


//            Database connection 
public static $DB_HOST =     'localhost';
public static $DB_DATABASE = 'dropGallery';

//            Database admin user
public static $DB_ADMIN_USER = 'root';
public static $DB_ADMIN_PASS = 'password';


}?>
