<?php
/** Image Mangers
*
*/

class Images extends Files{

	public function __construct($link){
		try {
			parent::__construct($link);
			$this->data = $this->open_image();
			$this->type = str_replace('image/', '', image_type_to_mime_type(exif_imagetype($this->path)));
			if($this->data === false || !in_array(strtolower($this->type), array("jpg", "jpeg", "gif", "png", "gd", "wbmp", "xbm", "xpm"))){
				throw new Exception('File formate not supported');
			}
		} catch(Exception $e){
			throw new Excption($e);
		}
	}
	
	public function open_image() {
		$file = $this->path;
        # JPEG:
        $im = @imagecreatefromjpeg($file);
        if ($im !== false) { return $im; }

        # GIF:
        $im = @imagecreatefromgif($file);
        if ($im !== false) { return $im; }

        # PNG:
        $im = @imagecreatefrompng($file);
        if ($im !== false) { return $im; }


      	# GD File:
        $im = @imagecreatefromgd($file);
        if ($im !== false) { return $im; }

        # GD2 File:
        $im = @imagecreatefromgd2($file);
        if ($im !== false) { return $im; }

        # WBMP:
        $im = @imagecreatefromwbmp($file);
        if ($im !== false) { return $im; }

        # XBM:
        $im = @imagecreatefromxbm($file);
        if ($im !== false) { return $im; }

        # XPM:
        $im = @imagecreatefromxpm($file);
        if ($im !== false) { return $im; }

        # Try and load from string:
		$opts = array( 'http'=>array( 'method'=>"GET",
		  'header'=>"Accept-language: en\r\n" .
		   "Cookie: ".session_name()."=".session_id()."\r\n" ) 
		);
		$context = stream_context_create($opts);
		session_write_close();   // this is the key
//		echo file_get_contents($file, false, $context); exit;
        $im = imagecreatefromstring(file_get_contents($file, false, $context));
        if ($im !== false) { return $im; }

        return false;
	}
	
	public function getHeight(){
		return imagesy($this->data);
	}
	
	public function getWidth(){
		return imagesx($this->data);
	}
	
	public function resize($h='', $w='', $propotions=true){
		if($propotions!= true && $w != '' && $h!=''){
			$new_width = $w;
			$new_height = $h;
		} elseif($w != '' && $h!=''){
			$wc = ($w / $this->getWidth());
			$hc = ($h / $this->getHeight());
			if($wc <= $hc) { 
				$new_width = $wc * $this->getWidth();
				$new_height = $wc * $this->getHeight();
			} else{ 
				$new_width = $hc * $this->getWidth();
				$new_height = $hc * $this->getHeight();
			}
		} elseif($w!=''){
			$wc = ($w / $this->getWidth());
			$new_width = $wc * $this->getWidth();
			$new_height = $wc * $this->getHeight();
		} elseif($h!= ''){
			$hc = ($h / $this->getHeight());
			$new_width = $hc * $this->getWidth();
			$new_height = $hc * $this->getHeight();
		}
		$image_resized = imagecreatetruecolor($new_width, $new_height);
		imagealphablending($image_resized, false);
		 imagesavealpha($image_resized, true);
		 imagecopyresampled($image_resized, $this->data, 0, 0, 0, 0, $new_width, $new_height, $this->getWidth(), $this->getHeight());
		$this->data = $image_resized;
	}
	
	public function setType($type){
		$this->type = $type;	
	}
	
	public function Display(){
		while (@ob_end_clean());
		header('Content-type: image/'.$this->type);
		switch(strtolower($this->type)){
			case "png":
				imagepng($this->data, NULL, 5);
			break;
			case "jpeg":
				imagejpeg($this->data, NULL, 50);
			break;
			case "gif":
				imagegif($this->data);
			break;
		}
		imagedestroy($this->data);
	}
	
	public function save($overwrite=true, $quality=NULL){
	//	while (@ob_end_clean());
	//	header('Content-type: image/'.$this->type);
		switch(strtolower($this->type)){
			case "png":
				imagepng($this->data, $this->path, 5);
			break;
			case "jpeg":
				imagejpeg($this->data, $this->path, 50);
			break;
			case "gif":
				imagegif($this->data, $this->path);
			break;
		}
		imagedestroy($this->data);
		return true;
	}

	public function getThumb($width=256){
		$this->resize('', $width);
		ob_start(); // Let's start output buffering.
			imagepng($this->data); //This will normally output the image, but because of ob_start(), it won't.
			$contents = ob_get_contents(); //Instead, output above is saved to $contents
		ob_end_clean(); //End the output buffer.
		if($contents!= false && $contents!=''){
			return "data:image/png;base64," . base64_encode($contents);
		} else {
			return false;
		}
	}
}