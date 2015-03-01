<?php

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
	private $const;

	private $mimetype;
	private $max;
    private $qhash;
	private $HTMLPath;

    /*
     * constructor
     * 
     * Runs startup commands
     */
    public function __construct($imagePath, $mimetype, $qhash, $maxDimension, $HTMLPath,  $constrain = "both")
    {
    	$this->mimetype = $mimetype;
    	$this->max = $maxDimension;
        $this->qhash = $qhash;
    	$this->HTMLPath = $HTMLPath;
    	$jpg = file_exists($this->relPath.$this->const."_".$maxDimension."_".$qhash.".jpg");
    	$png = file_exists($this->relPath.$this->const."_".$maxDimension."_".$qhash.".png");
    	if(! $jpg || ! $png)
    	{
			list($this->imgWidth, $this->imgHeight) = getimagesize($imagePath);
			$this->aspectToWidth = $this->imgWidth / $this->imgHeight;
			$this->aspectToHeight = $this->imgHeight / $this->imgWidth;
			$this->getDimensions($maxDimension,$constrain);

	    	$this->image = $this->getImage($imagePath,$mimetype);
	    	$this->generateImage();
    	}
    }

    public function getThumb()
    {
    	$type = $this->saveImage();
		if ($type == "jpg")
		{
			return "/content/".$this->relPath.$this->const."_".$this->max."_".$this->qhash.".jpg\" data-orig=\"".$this->HTMLPath;	
		}else if ($type == "png")
		{
			return "/content/".$this->relPath.$this->const."_".$this->max."_".$this->qhash.".png\" data-orig=\"".$this->HTMLPath;	
		}else{
            return $this->HTMLPath."\" data-orig=\"".$this->HTMLPath;
        }

    }

    public function getDimensions($max,$constrain)
    {
    	switch ( strtoupper($constrain) ) {
    		case 'WH':
    		case 'XY':
    		case 'BOTH':

    			if ( $this->aspectToWidth < 1 )
    			{
	    			$this->outHeight = $this->aspectToHeight * $max;
	    			$this->outWidth = $max;
    			}else{
	    			$this->outWidth = $this->aspectToWidth * $max;
	    			$this->outHeight = $max;	
    			}
    			$this->const = "b";
    			break;

    		case 'H':
    		case 'Y':
    		case 'HEIGHT':
    			$this->outWidth = $this->aspectToWidth * $max;
    			$this->outHeight = $max;
    			$this->const = "h";
    			break;
    		
    		case 'W':
    		case 'X':
    		case 'WIDTH':
    			$this->outHeight = $this->aspectToHeight * $max;
    			$this->outWidth = $max;    			
    			$this->const = "w";
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    }

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