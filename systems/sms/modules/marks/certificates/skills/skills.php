<?php
function builCertHtml($std_id){
	global $lang;
	$services = $GLOBALS['con_material'];
	$level_id = $GLOBALS['level_id'];
	$level_name = $GLOBALS['level_name'];
	$terms = $GLOBALS['terms'];
	$cur_term_no = $GLOBALS['cur_term_no'];
	$cur_term = $GLOBALS['cur_term'];
	$finals = $GLOBALS['finals'];

	return  Skills::createStudentSkillsTable( $std_id, new Terms($cur_term), new Services($service));
}
?>