<?php
##login_header
$header = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>'.Ms_systemname.'</title>
	<link href="assets/css/themes/'.MS_theme.'/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/common.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/special.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/'.MS_doc_direction.'.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="assets/js/jquery-1.8.3.js"></script>
	<script type="text/javascript" src="assets/js/jquery-ui-1.9.2.custom.min.js"></script>
	<script type="text/javascript" src="assets/js/globals.js"></script>
	<script type="text/javascript" src="assets/js/init.js"></script>'.
	(defined("MapsApiKey") ?
		'<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&language=ar-AR&libraries=places&key='.MapsApiKey.'"></script>'
	: '');
?>