<?php
header("X-XSS-Protection: 0");
require_once('ui/header.php');

if(!isset($_REQUEST['print_content'])){
	die('no content');
}

$assets_files = '<link media="all" href="plugin/print/print.css" rel="stylesheet" type="text/css" />'.
'<script type="text/javascript" src="plugin/print/print.js"></script>';


$head = str_replace('assets/','assets/', $header). $assets_files;
$head .=(isset($_POST['orientation']) && $_POST['orientation'] == 2 ?
	'<style type="text/css" media="all" >@page {size: landscape};</style>'
:
	'<style type="text/css" media="all" >@page {size: portrait};</style>'
);
	
$head .= write_script('$(document).ready(function(){
	initPrintStyle()
	window.print();
//	window.close();
});');

$head .= '<html moznomarginboxes mozdisallowselectionprint>';
## print
if(strpos($_REQUEST['print_content'], '<page') === false){
	/*$body = write_html('table', 'width="100%" style="height:100%"', 
		write_html('tr', '',
			write_html('td', '', '<img src="attachs/img/header.jpg" width="100%" />')
		).
		write_html('tr', '',
			write_html('td', '', $_REQUEST['print_content'])
		).
		write_html('tr', '',
			write_html('td', 'style="height:100%" valign="bottom"', '<img src="attachs/img/footer.jpg" width="100%" />')
		)
		
	);*/
	$body =  write_html('page', 'class="page"',
		(isset($_REQUEST['header']) ? 
			write_html('page_header', 'class="page_header"', '<img src="attachs/img/header.jpg" width="100%" />')
		: '').
		(isset($_REQUEST['title']) ? 
			write_html('h2', 'style="text-align:center;"', $_REQUEST['title'])
		: '').
		write_html('div ', 'class="page_content"',
			$_REQUEST['print_content']
		).
		(isset($_REQUEST['signature']) ?
			write_html('table', 'width="100%"',
				write_html('tr', '',
					write_html('td', 'width="50%" valign="top"', write_html('h3', '',$lang['signature_label'].': ')).
					write_html('td', 'height="50"', '&nbsp;')
				)
			)
		: ''
	)).
		(isset($_REQUEST['footer']) ? 
			write_html('div', 'id="page_footer" class="page_footer" style="text-align:center;"','<img src="attachs/img/footer.jpg" width="100%" />')
		: '');
} else {
	$body = $_REQUEST['print_content'];
	if(isset($_REQUEST['signature'])){
		$body .= write_html('table', 'width="100%"',
			write_html('tr', '',
				write_html('td', 'width="50%" valign="top"', $lang['signature_label'].': ').
				write_html('td', 'height="50"', '&nbsp;')
			)
		);
	}
}


echo write_page($head, $body, '');
exit;
?>