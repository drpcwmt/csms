<?php 
## terms 

if(isset($_GET['term_list'])){
	if(isset($_GET['con_id'])){
		$con = $_GET['con'];
		$con_id = $_GET['con_id'];
	} elseif(isset($_SESSION['cur_class'])){
		$con = 'class';
		$con_id = $_SESSION['cur_class'];
	} else {
		echo write_error('cant find subject');
	}
	
	$terms_arr = Terms::getTermsSelect($con, $con_id);
	$f = array_merge($terms_arr, getPassedMonths());
	foreach( $f as $value => $name){
		echo write_html('option', 'value="'.$value.'"', $name);
	}
}
