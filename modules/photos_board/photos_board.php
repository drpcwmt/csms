<?php
## PhotoBoard ##

if(isset($_GET['con'])){
	$con = $_GET['con'];
	$con_id = $_GET['con_id'];
	$con_obj = $this_system->getAnyObjById($con, $con_id);
} else {
	exit;
}
$students = $con_obj->getStudents(array('1'));
$pic = '';
foreach($students as $student){
	$std_id = $student->id;
	$pic .= write_html('a','class="ui-corner-all hand hoverable itemPhoto" module="students" action="openStudent" std_id="'.$std_id.'" ', 
		'<img src="'.$student->getPhotoPath().'" alt="Photos" border="0" width="120" " />
		<span>'.$student->getName().'</span>'
	);
}


//  Body 
echo write_html('div', 'id="photos_board"',
	 write_html('div', 'class="ui-corner-all ui-state-highlight showforprint hidden" style="padding:10px; margin-bottom:10px"',
		write_html('h3', '', $lang['photos_board'].' '.$lang[$con].': '.$con_obj->getName())
	).
	write_html('div', ' class="toolbox"',
		write_html('a', 'action="print_tab" plugin="print"',
			write_html('span', 'class="ui-icon ui-icon-print"', '').
			$lang['print']
		)
	).
	write_html('div', 'class="ui-corner-all ui-widget-default" style="padding:10px;"', $pic)
);

?>