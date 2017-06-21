<?php
/** Video Mangers
*
*/
require_once('ffprobe.php');

class Videos extends Files{
	private $ffmpegPath;
	private $root;

	public function __construct($link){
		
		parent::__construct($link);
		$this->path = docRoot.$this->path;
		if (substr(php_uname(), 0, 7) == "Windows"){
			$this->ffmpegPath = docRoot.'plugin/media/ffmpeg.exe';
			$this->path =  str_replace('/', '\\', $this->path);
		} else {
			$this->ffmpegPath = 'ffmpeg';
		}
		
		// infos

		try{
			$ffprobe = new ffprobe_ext($this->path);
			foreach( $ffprobe->getVideoInfo() as $key=>$val){
				$this->$key = $val;
			}
		} catch(Exception $e){
			//
			return $e;
		}
	//	print_r($this);
	}
	
	public function compress(){
		if(isset($this->frame_height)){
			global $MS_settings;
			$newheight = $MS_settings['conv_video_size'];
			$newWidth = ($newheight * $this->frame_width )/ $this->frame_height  ;
			$newheight = escapeshellarg($newheight);
			$newFile = escapeshellarg($this->dirname.'/'.$this->filename.'_copy.mp4');
			$cmd = $this->ffmpegPath." -y -i ".escapeshellarg($this->path)." -codec:v libx264 -codec:a mp3 -qscale 0 -vf scale=$newWidth:$newheight $newFile";
			$output= array();
			exec($cmd, $output, $retval);
			if(file_exists($this->dirname.'/'.$this->filename.'_copy.mp4') && filesize($this->dirname.'/'.$this->filename.'_copy.mp4') > 10){
				if(unlink($this->path)){
					rename($this->dirname.'/'.$this->filename.'_copy.mp4', $this->dirname.'/'.$this->filename.'.mp4');
				}
			}
		} else {
			return false;
		}
	}
	
	public function resize($h='', $w=''){
		if($h!='' && $w!=''){
			$newHeight = $h;
			$newWidth = $w;
		} elseif( $h !=''){
			$newHeight = $h;
			$newWidth = ($newHeight / $this->frame_height ) * $this->frame_width;
		} elseif( $w !=''){
			$newWidth = $w;
			$newHeight = ($newWidth / $this->frame_width ) * $this->frame_height;
		} else {
			return false;
		}
		
		$new_path = $this->dirname.'/'.$this->filename.'_resized.'.$this->extension;
		$cmd = $this->ffmpegPath.' -y -i '.$this->path.' -vf scale=$newWidth:$newHeight '.$new_path;
		$this->execInBackground($cmd);
	}
	
	public function convert($outputFormat){
		$new_path = $this->dirname.'/'.$this->filename.'.'.$outputFormat;
		$cmd = $this->ffmpegPath.' -y -i '.$this->path.' -c:v libx264 -c:a copy -preset ultrafast '.$new_path;
		$this->execInBackground($cmd);
	}
	
	public function getThumb($sec=10, $width=256){
		$cachDir = docRoot.'attachs/tmp/cache/';
		$cachFileName = $cachDir.md5($this->path).'.png';
		
		if(!file_exists($cachFileName)){
			//$new_path = $this->dirname.'/'.$this->filename.time().'.png';
			$cmd = "$this->ffmpegPath -y -ss 00:00:$sec -i ".escapeshellarg($this->path)." -vf scale=$width:-1 -vframes 1 ".escapeshellarg( $cachFileName);
			//$this->execInBackground($cmd);
			$output= array();
			exec($cmd, $output, $retval);
		}
		$data = @file_get_contents($cachFileName);
//		@unlink($new_path);	
		if($data != false){
			return 'data:image/png;base64,'.base64_encode($data);
		} else {
			return false;
		}
	}
	
	public function converToAudio(){
		$new_path = escapeshellarg($this->dirname.'/'.$this->filename.'.mp3');
		$cmd = "$this->ffmpegPath -y -i ".escapeshellarg($this->path)." -vn -ab 256 $new_path";
		$this->execInBackground($cmd);
	}
	
	public function convertToGif($sec=10){
		$new_path = escapeshellarg($this->dirname.'/'.$this->filename.'.gif');
		$cmd = "$this->ffmpegPath -y -i ".escapeshellarg($this->path)." -vf scale=$this->frame_width:-1 -t $sec -r $sec $new_path";
		$this->execInBackground($cmd);
	}
	
	
	public function splitVideo($time, $keep){
		$first = escapeshellarg($this->dirname.'/'.$this->filename.'-split-1.'.$this->extension);
		$second = escapeshellarg($this->dirname.'/'.$this->filename.'-split-2.'.$this->extension);
		$cmd = "$this->ffmpegPath -y -i ".escapeshellarg($this->path)." -t $time -c copy $first -ss $time -codec copy $second";
		$this->execInBackground($cmd);
	}
	
	public function rotateVideo($degree){
		if($degree == 90){
			$rotate = 'transpose=1';
		} elseif($degree == 180){
			$rotate = 'transpose=1,transpose=1';
		} elseif($degree == 270){
			$rotate = 'transpose=2';
		}
		if(isset($rotate)){
			$s = explode('/', $this->path);	
			unset($s[count($s)-1]);		
			$new_path = escapeshellarg(implode('/', $s).'/'.$this->filename.'-rotated.'.$this->extension);
			$cmd = "$this->ffmpegPath -y -i ".escapeshellarg($this->path)." -filter:v '$rotate' $new_path";
			$this->execInBackground($cmd);
		}
	}
	
	public function execInBackground($cmd) {
		if (substr(php_uname(), 0, 7) == "Windows"){
			echo $cmd;
			//shell_exec($cmd);
			$handle = popen("start /B ". $cmd, "r");
			$output = fread($handle, 2096);
			pclose($handle);
		}
		else {
			$output = array();
			echo $cmd;
			exec($cmd . " > /dev/null &", $output, $returnval);
			echo $returnval;  
		}
		return $output;
	}
}
	
	