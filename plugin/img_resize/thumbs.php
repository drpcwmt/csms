<?php
## thumbnails script
if(isset($_GET['std_id'])){
	$std_id = $_GET['std_id'];
	$image = do_query("SELECT thumb FROM student_data WHERE id= $std_id", DB_student);
	if($image == NULL){
		$default_img_path = 'assets/img/students.png';
		$image = @imagecreatefrompng($default_img_path);
	}
	$width = imagesx($image);
	$height = imagesy($image);
	
	if ($image === false) { die ('Unable to open image'); }
	if (isset($_GET['w']) && isset($_GET['h'])){
		$wc = ($_GET['w'] / $width);
		$hc = ($_GET['h'] / $height);
		
		if($wc <= $hc) { $w = $_GET['w'];}
		else{ $h = $_GET['h'];}
	}elseif(isset($_GET['w']) && !isset($_GET['h'])){
		$w = $_GET['w'];
	}elseif(isset($_GET['h']) && !isset($_GET['w'])){
		$h = $_GET['h'];
	}
	
	if (!isset($w) && !isset($h)) {
		$new_width = $width;
		$new_height = $height;
	}elseif (isset($w) && isset($h)) {
		$new_width = $w;
		$new_height = $h;
	}elseif (isset($w) && !isset($h)) {
		$new_width = $w;
		$new_height = $w * $height / $width;
	}elseif (isset($h) && !isset($w)) {
		$new_height = $h;
		$new_width = $h * $width / $height;
	}if(isset($_GET['both'])){
		$new_height = $_GET['h'];
		$new_width = $_GET['w'] ;
	}
	
	// Resample
	$image_resized = imagecreatetruecolor($new_width, $new_height);
	imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
	// Display resized image
	header('Content-type: image/jpeg');
	imagejpeg($image_resized);
	die();
}
?>