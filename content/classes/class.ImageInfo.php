<?php
class FileInfo{

    private $filepath;
    public $data;
    private $fileExfi;

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
     * getFileInfo
     * 
     * Returns files info
     */
    public function getFileInfo()
    {
        return $this->data;
    }

    /*
     * loadFileInfo
     *
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

                    case 'image/jpeg':
                    case 'image/pjpeg':
                    case 'image/tiff':
                    case 'image/x-tiff':
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
     * loadBasicData
     * 
     * Gets generic file information
     */
    private function loadBasicData()
    {
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
     * loadExfiData
     * 
     * gets Exfi specific data. Mostly for camera info from jpegs
     */
    private function loadExfiData()
    {
        $exifData = exif_read_data($this->data['path']);

        $this->data['camera']['make'] = $exifData['Make'];
        $this->data['camera']['model'] = $exifData['Model'];
        $this->data['camera']['lens'] = $exifData['UndefinedTag:0xA434'];
        $this->data['camera']['focalLengthExact'] = $exifData['FocalLengthIn35mmFilm'];
        $this->data['camera']['focalLength'] = $this->data['camera']['focalLengthExact']." mm";
        $this->data['camera']['shutterSpeed'] = $exifData['ExposureTime'];
        $this->data['camera']['apatureExact'] = $exifData['FNumber'];
        $this->data['camera']['apature'] = 'f/'.round(floatval($this->data['camera']['apatureExact']),1);
        $this->data['camera']['ISO'] = $exifData['ISOSpeedRatings'];
        $this->data['camera']['dateTaken'] = $exifData['DateTimeOriginal'];
        $this->data['camera']['software'] = $exifData['Software'];

    }


    /*
     * loadIPTCKeywords
     *
     * Read keywords from IPTC header
     */
    private function loadIPTCKeywords()
    {
        list($this->data['width'], $this->data['height']) = getimagesize ( $this->data['path'], $iptcInfoRaw);

        # Test for loaded IPTC data
        if(is_array($iptcInfoRaw)) 
        {   
            # Parse data
            $iptc = iptcparse($iptcInfoRaw["APP13"]);
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
	
}
?>
