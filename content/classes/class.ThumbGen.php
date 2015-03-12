<?php
/*                                                                            *\
                                  ThumbGen

Creates thumbnails on an adhoc use case. The files are not created until they 
are needed. It can make thumbnails of any size as long as the dimensions are 
smaller than the original image. The thumbnails take a single size value that 
constrains the thumbnail. The constraint either is the width or height to make 
the thumbnail or set to both will not let the image be larger in either 
dimension.

\*                                                                            */
class ThumbGen
{
	
	private $image;
	private $imgWidth;
	private $imgHeight;

	private $aspectToWidth;
	private $aspectToHeight;

	private $output;
	private $outWidth;
	private $outHeight;

	private $relPath = "generated/thumbnails/";
	private $thumbPath;
	private $thumbHTMLPath;
	private $const;

	private $mimetype;
	private $max;
    private $qhash;
	private $HTMLPath;
	private $support;
	private $use = true;

    /*
     * constructor
     * 
     * Determines if a thumbnail exists for given image and creates it if not
     */
    public function __construct($imagePath, $mimetype, $qhash, $maxDimension, $HTMLPath,  $constrain = "both")
    {
    	$this->mimetype = $mimetype;
    	$this->max = $maxDimension;
        $this->qhash = $qhash;
    	$this->HTMLPath = $HTMLPath;

    	$this->support = $this->supportCheck($this->mimetype);

    	if($this->support)
	    {
	    	$this->getConst($constrain);
	    	$this->getThumbFilePath();
	    	if(! file_exists($this->thumbPath) )
	    	{
				list($this->imgWidth, $this->imgHeight) = getimagesize($imagePath);
				$this->aspectToWidth = $this->imgWidth / $this->imgHeight;
				$this->aspectToHeight = $this->imgHeight / $this->imgWidth;
				$this->getDimensions($maxDimension,$constrain);

				if($this->imgWidth > $this->outWidth)
				{
			    	$this->image = $this->getImage($imagePath,$mimetype);
			    	$this->generateImage();
	    			$this->saveImage();	
				}else{
	    			$this->use = false;
				}
	    	}    		
    	}

    }


    /*
     * Checks if the image is a supported type to be generated
     */
    public function supportCheck($mimetype)
    {
    	switch ($mimetype)
		{
			case 'image/jpeg':
			case 'image/pjpeg':
			case 'image/x‑xbitmap':
			case 'image/x‑xbm':
			case 'image/x‑xpixmap':
			case 'image/vnd.wap.wbmp':
			case 'image/webp':
			case 'image/png':
			case 'image/gif':
				return true;
				break;

			default:
				return false;
				break;
		}
	
    }

    /*
     * Determines the thumbnails path using the constraint, size, id, and need for transparency
     */
    public function getThumbFilePath()
    {
    	$filename=$this->const."_".$this->max."_".$this->qhash;
    	$this->thumbPath= dirname(dirname(__FILE__))."/".$this->relPath.$filename;
    	$this->thumbHTMLPath= "/content/".$this->relPath.$filename;
    	switch ($this->mimetype)
		{
			case 'image/jpeg':
			case 'image/pjpeg':
			case 'image/x‑xbitmap':
			case 'image/x‑xbm':
			case 'image/x‑xpixmap':
			case 'image/vnd.wap.wbmp':
				$this->thumbPath .= ".jpg";
				$this->thumbHTMLPath .= ".jpg";
				break;

			case 'image/webp':
			case 'image/png':
			case 'image/gif':
				$this->thumbPath .= ".png";
				$this->thumbHTMLPath .= ".png";
				break;

			default:
				return false;
				break;
		}
	
    }

    /*
     * Returns URI for the thumbnail and an aditional data attribute for use with javascript to find the original image
     */
    public function getThumb()
    {
    	if ($this->support && $this->use)
    	{
			return $this->thumbHTMLPath."\" data-orig=\"".$this->HTMLPath;
    	}else{
            return $this->HTMLPath."\" data-orig=\"".$this->HTMLPath;
        }

    }

    /*
     * Determines the method to be used to constrain the size of the thumbnail
     */ 
    public function getConst($constrain)
    {
    	switch ( strtoupper($constrain) ) {

    		case 'H':
    		case 'Y':
    		case 'HEIGHT':
    			$this->const = "h";
    			break;
    		
    		case 'W':
    		case 'X':
    		case 'WIDTH':		
    			$this->const = "w";
    			break;
    		
    		case 'WH':
    		case 'XY':
    		case 'BOTH':
    		default:
    			$this->const = "b";
    			break;
    	}
    }

    /*
     * Calculates thumbnail dimensions based on constraint
     */ 
    public function getDimensions($max)
    {
    	switch ( $this->const ) {
    		case 'b':

    			if ( $this->aspectToWidth < 1 )
    			{
	    			$this->outHeight = $this->aspectToHeight * $max;
	    			$this->outWidth = $max;
    			}else{
	    			$this->outWidth = $this->aspectToWidth * $max;
	    			$this->outHeight = $max;	
    			}
    			break;

    		case 'h':
    			$this->outWidth = $this->aspectToWidth * $max;
    			$this->outHeight = $max;
    			break;
    		
    		case 'w':
    			$this->outHeight = $this->aspectToHeight * $max;
    			$this->outWidth = $max;  
    			break;
    		
    		default:
    			
    			break;
    	}
    }

    /*
     * Writes the thumbnail image to a file
     */ 
    public function saveImage()
    {
    	if(explode("/", $this->mimetype)[0] == 'image')
    	{
	    	switch (explode("/", $this->mimetype)[1]) 
			{

				case 'jpeg':
				case 'pjpeg':
				case 'x‑xbitmap':
				case 'x‑xbm':
				case 'x‑xpixmap':
				case 'vnd.wap.wbmp':
    				imagejpeg($this->output,dirname(dirname(__FILE__))."/".$this->relPath.$this->const."_".$this->max."_".$this->qhash.".jpg",$this->calcQuality($this->max));
    				return "jpg";
					break;

				case 'webp':
				case 'png':
				case 'gif':
    				imagepng($this->output,dirname(dirname(__FILE__))."/".$this->relPath.$this->const."_".$this->max."_".$this->qhash.".png",9);
    				return "png";
					break;

				default:
					# code...
					break;
			}
			
    	}
    }

    /*
     * Calculate the quality of the jpeg thumbnail to use with a limit of 20
     */ 
    public function calcQuality($max)
    {
    	$qual = ($max / 500)*100 + 20;
    	if ( $qual > 100  )
    	{
    		return 100;
    	}else{
    		return $qual;
    	}

    }


    /*
     * Creates the thumbnail image from the file
     */ 
    public function generateImage()
    {
		$this->output = imagecreatetruecolor($this->outWidth, $this->outHeight);
		imagealphablending($this->output, false);
		$col=imagecolorallocatealpha($this->output,255,255,255,127);
		imagefilledrectangle($this->output,0,0,$this->outWidth, $this->outHeight,$col);
		imagealphablending($this->output,true);
		imagecopyresampled($this->output, $this->image, 0, 0, 0, 0, $this->outWidth, $this->outHeight, $this->imgWidth, $this->imgHeight);
        imagesavealpha($this->output,true);
    }

    /*
     * Load the raw image from the file based on the mimetype
     */ 
    public function getImage($imagePath,$mimetype)
    {
    	$imageImport;
    	if(explode("/", $mimetype)[0] == 'image')
    	{
	    	switch (explode("/", $mimetype)[1]) 
			{

				case 'jpeg':
				case 'pjpeg':
    				$imageImport = imagecreatefromjpeg($imagePath);
					break;

				case 'x‑xbitmap':
				case 'x‑xbm':
    				$imageImport = imagecreatefromxbm($imagePath);
					break;

				case 'x‑xpixmap':
    				$imageImport = imagecreatefromxpm($imagePath);
					break;

				case 'png':
    				$imageImport = imagecreatefrompng($imagePath);
					break;

				case 'gif':
    				$imageImport = imagecreatefromgif($imagePath);
					break;
					
				case 'vnd.wap.wbmp':
    				$imageImport = imagecreatefromwbmp($imagePath);
					break;

				case 'webp':
    				$imageImport = imagecreatefromwebp($imagePath);
					break;
				
				default:
					# code...
					break;
			}
			
    	}

    	return $imageImport;
		
    }


}
?>