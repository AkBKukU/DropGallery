<?php
//$DP = new DynamicPages();


//--How to use

//-----|Call Function|
//echo $footer->printf();

class Utilities{

	/*
	 * Takes a number of bytes and converts it to a readable string. Works up to GB.
	 */
    public static function filesizeInterpreter ($bytes)
    {
        //Gigbyte size
        if($bytes > 1073741824)
        {
            $sizetext = round($bytes/1073741824,1)."GB";
        }
        //Megabyte size
        elseif($bytes > 1048576)
        {
            $sizetext = round($bytes/1048576,1)."MB";
        }
        //kilobyte size
        elseif($bytes > 1024)
        {
            $sizetext = round($bytes/1024,1)."kB";
        }
        //kilobyte size
        else
        {
            $sizetext = $bytes."B";
        }

        return $sizetext;
    }
	
}
?>
