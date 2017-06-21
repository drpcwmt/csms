<?php 

if(isset($_GET['thumbs'])){
	include('thumbs.php');
	exit;
}
if (isset($_GET['path'])) {
  $path = stripslashes(utf8_decode(urldecode($_GET['path'])));
} else {
	echo "no path";
	exit;
}

function open_image ($file) {
//	echo $file;

        # JPEG:
        $im = @imagecreatefromjpeg($file);
        if ($im !== false) { return $im; }

        # GIF:
        $im = @imagecreatefromgif($file);
        if ($im !== false) { return $im; }

        # PNG:
        $im = @imagecreatefrompng($file);
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


        return false;
}

$image = open_image($path);
if ($image === false) { die ('Unable to open image'); }

$width = imagesx($image);
$height = imagesy($image);

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
imagealphablending( $image_resized, false );
imagesavealpha( $image_resized, true );

imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

// Display resized image
while (@ob_end_clean());
header('Content-type: image/png');
imagepng($image_resized);
imagedestroy($image_resized);
//die();
?>