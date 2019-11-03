<?php 

namespace AlissonBora;
 
class Image {
	  
	private $image;
	
	public function __construct($source)
	{ 
		if (extension_loaded('gd')) { 
			$this->load($source); 
		} 
		else { 
			throw new \Exception("GD extension not loaded or not exist"); 
		}
	}
	 
	public function resize($w, $h, $mode = "aspectRatio") 
	{ 
		$width = $this->getWidth();
		$height = $this->getHeight(); 
		
		if ($mode == "aspectRatio") { 
			$ratio = $width / $height; 
		   
		    if ($h == null) { 
			    $newWidth = $w; 
			    $newHeight = $newWidth / $ratio;  
			} 
			
			if ($w == null) { 
			    $newHeight = $h; 
				$newWidth = $newHeight * $ratio; 
			} 
			 
			if ($w != null && $h != null) {
			    if ($width > $height) { 
				    $newWidth = $w; 
				    $newHeight = $newWidth / $ratio; 
			    } 
			    elseif ($width < $height) { 
				    $newHeight = $h; 
				    $newWidth = $newHeight * $ratio; 
			    } 
			    else { 
				    $newWidth = $w; 
				    $newHeight = $h; 
			    } 
		    } 
		}
		else { 
	        $newWidth = $w; 
			$newHeight = $h;
		} 
		
		$tmp = imagecreatetruecolor($newWidth, $newHeight); 
        imagecopyresampled($tmp, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);  
        $this->image = $tmp;
    } 
     
    public function rotate($angle)
    {  	
        $this->image = imagerotate($this->image, $angle, 0);  
    } 
     
    public function colorize($red, $green, $blue)
    {   
        imagefilter($this->image, IMG_FILTER_COLORIZE, $red, $green, $blue); 
    } 
     
    public function negative()
    {   
        imagefilter($this->image, IMG_FILTER_NEGATE); 
    }  
    
    public function greyScale()
    {   
    	imagefilter($this->image, IMG_FILTER_GRAYSCALE);
    }  
    
    public function brightness($level)
    {   
        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $level);
    } 
     
    public function pixelate($size)
    {   
    	imagefilter($this->image, IMG_FILTER_PIXELATE, $size);
    } 
     
    public function contrast($level)
    {   
        imagefilter($this->image, IMG_FILTER_CONTRAST, $level);
    }  
     
    public function insert($image, $position, $x = 10, $y = 10) 
    { 
    	$tmp = imagecreatefrompng($image); 
        $width = imagesx($tmp);
        $height = imagesy($tmp); 
        
        if ($position) {
            switch ($position) { 
        	    case "center": 
                    $x = ceil(($this->getWidth() - $width) / 2); 
                    $y = ceil(($this->getHeight() - $height) / 2);
                    break;  
                case "top":  
                    $x = ceil(($this->getWidth() - $width) / 2); 
                    break;  
                case "bottom":  
                    $x = ceil(($this->getWidth() - $width) / 2); 
                    $y = $this->getHeight() - ($height + $y);
                    break; 
                case "left":  
                    $y = ceil(($this->getHeight() - $height) / 2);
                    break;  
                case "right": 
                    $x = $this->getWidth() - ($width + $x);
                    $y = ceil(($this->getHeight() - $height) / 2);
                    break; 
                case "top_left": 
                    break; 
                case "top_right":  
                    $x = $this->getWidth() - ($width + $x);
                    break;  
                case "bottom_left": 
                    $y = $this->getHeight() - ($height + $y);
                    break; 
                case "bottom_right":  
                    $x = $this->getWidth() - ($width + $x); 
                    $y = $this->getHeight() - ($height + $y);
                    break; 
                default: 
                    break;  
             }
         }
         
         imagecopy($this->image, $tmp, $x, $y, 0, 0, $width, $height);
         imagedestroy($tmp); 
     }
				
	public function save($path, $quality = null) 
	{   
		switch ($this->getExtension($path)) {
			case "jpg":
			    imagejpeg($this->image, $path, $quality);
			    break; 
			case "jpeg": 
			    imagejpeg($this->image, $path, $quality);
			    break; 
			case "png": 
			    imagepng($this->image, $path, $quality);
			    break;  
			case "gif":
			    imagegif($this->image, $path, $quality);
			    break; 
			case "webp": 
			    imagewebp($this->image, $path, $quality);
			    break; 
			default:
			    break; 
		} 
		
		$this->destroy();
    } 
     
    public function output($format, $quality = null) 
	{  
		switch ($format) { 
			case "jpg": 
			    header("Content-Type: image/jpeg");
			    imagejpeg($this->image, NULL, $quality);
			    break; 
			case "jpeg":  
			    header("Content-Type: image/jpeg");
			    imagejpeg($this->image, NULL, $quality);
			    break; 
			case "png":  
			    header("Content-Type: image/png");
			    imagepng($this->image, NULL, $quality);
			    break;  
			case "gif": 
			    header("Content-Type: image/gif");
			    imagegif($this->image, NULL, $quality);
			    break; 
			case "webp":  
			    header("Content-Type: image/webp");
			    imagewebp($this->image, NULL, $quality);
			    break; 
			default:
			    break; 
		}  
		 
		$this->destroy();
    } 
     
    private function load($source) 
    { 
    	if ($this->isUrl($source)) { 
			$this->image = imagecreatefromstring($this->getImageFromUrl($source)); 
		} 
		else {
		    $extension = $this->getExtension($source); 
		 
		    switch ($extension) { 
			    case "jpg": 
			        $this->image = imagecreatefromjpeg($source);
			        break; 
			    case "jpeg": 
			        $this->image = imagecreatefromjpeg($source);
			        break; 
			    case "png": 
			        $this->image = imagecreatefrompng($source);
			        break;  
			    case "gif":
			        $this->image = imagecreatefromgif($source);
			        break; 
			    case "webp": 
			        $this->image = imagecreatefromwebp($source);
			        break; 
			    default: 
			        $this->image = null; 
			    break; 
			} 
		}
	} 
	 
	private function getWidth() 
	{  
		return imagesx($this->image);
	} 
	
	private function getHeight() 
	{ 
		return imagesy($this->image); 
	} 
			
	private function getExtension($path)
	{ 
		return pathinfo($path, PATHINFO_EXTENSION); 
	} 
	 
	private function isUrl($str) 
	{ 
		return filter_var($str, FILTER_VALIDATE_URL) ? true : false; 
	}  
	 
	private function getImageFromUrl($url) 
	{ 
		$options = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false
             ]
        ]; 
        
		return file_get_contents($url, false, stream_context_create($options));
	} 
	 
	private function destroy() 
	{ 
		imagedestroy($this->image); 
	} 
	
}