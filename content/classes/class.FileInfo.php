<?php
class FileInfo{

	private $filepath;
	public $data;
	private $fileExfi;
	
	private $metadata;
	private $tags;

	private $title;
	private $description;
	private $mimetype;
	private $dateGathered;
	private $filename;
	private $filesize;

	/*
	 * constructor
	 * 
	 * Runs startup commands
	 */
	public function __construct($filepath)
	{
		if (file_exists($filepath)) 
		{
			$this->filepath = $filepath;
			$this->loadFileInfo();
		}else{
			echo "File not found.";
		}

	}

	/*
	 * Returns files info
	 */
	public function getFileInfo()
	{
		return $this->data;
	}

	
	/*
	 * Read metadata from files.
	 */
	private function loadFileInfo()
	{
		$this->loadBasicData();
		switch ($this->data['mimetypeType']) 
		{

			case 'image':
				list($this->data['width'], $this->data['height']) = getimagesize ( $this->filepath );
				$this->data['resolution'] = $this->data['width'].'x'.$this->data['height'];

				switch ($this->data['mimetype']) 
				{
					case 'image/jpeg':
					case 'image/pjpeg':
					case 'image/tiff':
					case 'image/x-tiff':

						$this->loadExfiData();
						$this->loadIPTCKeywords();
						break;

					case 'image/png':
					case 'image/jp2':
					case 'image/jpx':
					case 'image/jpm':
					case 'image/mj2':
						$this->loadIPTCKeywords();
						break;

					
					default:
						# code...
						break;
				}
				break;
			
			default:
				# code...
				break;
		}
	}

	/*
	 * Gets generic file information
	 */
	private function loadBasicData()
	{
		$this->filename = basename($this->filepath);
		$this->title = $this->filename;
		$this->description = $this->filename;
		$this->mimetype = mime_content_type($this->filepath);
		$this->dateGathered = date('Y-m-d').'T'.date('h:i:s');
		$this->filesize = filesize($this->filepath);

		$this->data['path'] = $this->filepath;
		$this->data['name'] = basename($this->data['path']);

		$this->data['sizeExact'] = filesize($this->data['path']);
		$this->data['size'] = Utilities::filesizeInterpreter($this->data['sizeExact']);

		$this->data['accessDateExact'] = fileatime($this->data['path']);
		$this->data['accessDate'] = date("F d Y H:i:s.", $this->data['accessDateExact']);

		$this->data['modifyDateExact'] = filemtime($this->data['path']);
		$this->data['modifyDate'] = date("F d Y H:i:s.", $this->data['modifyDateExact']);

		$this->data['mimetype'] = mime_content_type($this->data['path']);
		$this->data['mimetypeType'] = explode("/", $this->data['mimetype'])[0];


	}

	/*
	 * WARNING - Reursion is started here!
	 *
	 * gets Exfi specific data. Mostly for camera info from jpegs
	 */
	private function loadExfiData()
	{
		$exifData = @exif_read_data($this->data['path']);
		$result = $this->flattenArray("exfi",$exifData,":");

		//var_dump($result);

		foreach($result as $key=>$value)
		{	   
			$this->metadata[$key] = $value;
		}
	//var_dump($this->metadata);
		//var_dump($exifData);

	}

	/*
	 * WARNING - Reursive function
	 *
	 * Flatens multi dimensional array
	 */
	private function flattenArray($name,$array,$delim)
	{
		$result = array();
		foreach($array as $key=>$value)
		{
			if(is_array($value))
			{
				$this->flattenArray($name.$delim.$key,$value,$delim);
			}else{
				$result[$name.$delim.$key] = $value;
			}
		}
	return $result;
	}


	/*
	 * Read keywords from IPTC header
	 */
	private function loadIPTCKeywords()
	{
		list($this->metadata['width'], $this->metadata['height']) = getimagesize ( $this->data['path'], $iptcInfoRaw);

		//var_dump(iptcparse($iptcInfoRaw["APP13"]));

		// Test for loaded IPTC data
		if(is_array($iptcInfoRaw) && isset($iptcInfoRaw["APP13"])) 
		{   
			// Parse data
			$iptc = iptcparse($iptcInfoRaw["APP13"]);

			if(isset($iptc["2#025"]))
			{
				$this->tags = $iptc["2#025"];
			}
			
			if(isset($iptc["2#005"]))
			{
				$this->title = $iptc["2#005"][0];
			}

			if(isset($iptc["2#120"]))
			{
				$this->description = $iptc["2#120"][0];
			}

			//var_dump($iptc);
			foreach (array_keys($iptc) as $data) 
			{
				$c = count ($iptc[$data]);
				for ($i=0; $i <$c; $i++)
				{
					# Check for keyword id
					if ($data == "2#025")
					{
						$this->data['keywords'][] = $iptc[$data][$i];
					}
				} 
			}
		} 
	}
	
	public function getFilename()
	{
		return $this->filename;
	}
	
	public function getFilesize()
	{
		return $this->filesize;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
		
	public function getDescription()
	{
		return $this->description;
	}

	public function getMimetype()
	{
		return $this->mimetype;
	}
		
	public function getDescriptions()
	{
		return $this->description;
	}
	
	public function getMetadata()
	{
		return $this->metadata;
	}
	
	public function getTags()
	{
		return $this->tags;
	}

	
}
?>
