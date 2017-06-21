<?php
## SMS
## Absents
$editable = getPrvlg('att_absent_edit');
$readable = getPrvlg('att_absent_read');

if(isset($_GET['period_list'])){
	if(isset($_GET['con_id'])){
		$con = $_GET['con'];
		$con_id = $_GET['con_id'];
	} elseif(isset($_SESSION['cur_class'])){
		$con = 'class';
		$con_id = $_SESSION['cur_class'];
	} else {
		echo write_error('cant find subject');
	}
	$absent = new Absents($con, $con_id);
	echo $absent->getPeriods();
	exit;
}

if(isset($_GET['rate'])){
	
	if(isset($_GET['con_id'])){
		$absent = new Absents(safeGet('con'), safeGet('con_id'));
		if(isset($_GET['t'])){
			$table = $absent->loadRateTable('term', safeGet('t'));
			$chart = $absent->getRateChart('term', safeGet('t'));
		} elseif(isset($_GET['m'])){
			$table = $absent->loadRateTable('month', safeGet('m'));
			$chart = $absent->getRateChart('month', safeGet('m'));
		} else {		
			$table = $absent->loadRateTable();
			$chart = $absent->getRateChart();
		}
		
		echo write_html('table', 'width="100%"',
    		write_html('tr', '',
        		write_html('td', 'valign="top"',
            		$table
				).
				write_html('td', 'valign="top" width="400"',
            		$chart
				)
			)
		);
	} else{
		$con = isset($_REQUEST['con']) ? $_REQUEST['con'] : 0;
		$con_id = isset($_REQUEST['con_id']) ? $_REQUEST['con_id'] : 0;
		$absent = new Absents($con, $con_id);
		echo $absent->loadRate();
	}
	exit; 
}

if(isset($_GET['std_id'])){
	$absent = new Absents('student', safeGet('std_id'));
	if(isset($_GET['t'])){
		$table = $absent->loadStdLayout('term', safeGet('t'));
	} elseif(isset($_GET['m'])){
		$table = $absent->loadStdLayout('month', safeGet('m'));
	} else {		
		$table = $absent->loadStdLayout();
	}
	echo $table;
	exit;
}

/************* Student Absent ******************/
if(in_array($_SESSION['group'], array('student', 'parent')) || isset($_GET['std_id'])){
	$con = "std";
	$con_id =  isset($_GET['std_id']) ? $_GET['std_id'] : $_SESSION['std_id'];
	include('absents_by_student.php');
	include('absents_list.php');
	echo write_html('div', 'id="std_abs_div"', 
		$std_abs_toolbox.
		$std_abs_form.
		write_html('div', 'id="std_absent_list_div"', $absent_list_table)
	);
	exit; 
}

/************* chart ******************/
if(isset($_GET['chart'])){
	setJsonHeader();
	include('absents_chart.php');
	exit;
}

/************ OUT && permissons *************/
if(isset($_GET['permis'])){
	include('absents_perm_out.php');
	exit;
}

// upadet permisssion
if(isset($_GET['updateper'])){
	setJsonHeader();
	$permis_id = $_POST['id'];
	if(UpdateRowInTable('out_permis', $_POST, "id='$permis_id'", DB_year)){
		echo "{\"error\" : \"\", \"id\" : \"$permis_id\"}";
	} else {
		echo "{\"error\" : \"Error while updating\"}";
	}
	exit;	
}

if(isset($_GET['delper'])){
	setJsonHeader();
	if(getPrvlg('att_absent_edit')){
		$error = false;
		if(!do_query_edit( "DELETE FROM out_permis WHERE id=".$_POST['id'], DB_year)){
			$error = true;
		}
		if($error){
			echo json_encode(array('error' => $lang['error_while_updating'])); 
		} else {
			echo json_encode(array('error' => ''));
		}
	} else {
		echo json_encode(array('error' => $lang['no_privilege'])); 
	}	exit;
}

/************ Daily absent *************/
if(isset($_GET['daily'])){
	include('absents_daily_list.php');
	echo $absent_daily_toolbox.$absents_daily_list;
	exit;
}

// delete absents
if(isset($_GET['del'])){
	setJsonHeader();
	if(getPrvlg('att_absent_edit')){
		//$row_del_abs = do_query( "SELECT con_id FROM absents WHERE id=".$_POST['id'], DB_year);
		if(do_query_edit( "DELETE FROM absents WHERE id=".$_POST['id'], DB_year ) != false){
			//sendAbsentDelMsg($row_del_abs['con_id']);
			echo json_encode(array('error' => ''));
		} else {
			echo json_encode(array('error' => $lang['error'])); 
		}
	} else {
		echo json_encode(array('error' => $lang['no_privilege'])); 
	}
	exit;
}


/************ Absent By Lesson *************/
if(isset($_GET['absbylesson'])){
	include('absents_by_lesson.php');
	echo $abs_by_lesson_toolbox.$abs_by_lesson_list;
	exit;
}




if(isset($_GET['abslist'])){
	if(isset($_GET['con_id'])){
		$con = $_GET['con'];
		$con_id = $_GET['con_id'];
		include('absents_list.php');
		echo  $absent_list_table;
	} else{
		include('absents_list.php');
		$abs_list_toolbox=  write_html('div', 'class="toolbox"',
			write_html('a', 'rel="#absent_list_tab" class="print_but"', write_icon('print').$lang['print']).
			write_html('a', 'action="exportTable" rel="#absent_list_tab"',write_icon('disk'). $lang['export'])
		);
		echo write_html('div', 'id="absent_list_tab"',
			write_html('div', 'class="showforprint hidden"', 
				write_html('h2', '', $lang['absent_lists'])
			).
			$abs_list_toolbox.$absent_list_form.
			write_html('div', 'id="absent_list_div"', '')
		);
	}
	exit;
 
}



/********************** DEFAULT BODY ***********************************************/
if(isset($_GET['generate_certficate'])){
	include('absents_by_student.php');
	include('absents_list.php');
} else {
	require_once('absents_daily_list.php');
	echo write_html('div', 'class="tabs"',
		write_html('ul', '',
			write_html('li', '', write_html('a', 'href="#absents_daily_tab"', $lang['absents_by_day'])).
			write_html('li', '', write_html('a', 'href="index.php?module=absents&absbylesson"', $lang['absents_by_lesson'])).
			write_html('li', '', write_html('a', 'href="index.php?module=absents&abslist"', $lang['absent_lists'])).
			write_html('li', '', write_html('a', 'href="index.php?module=absents&rate"', $lang['attendance_rates']))
		).
		write_html('div', 'id="absents_daily_tab"', 
			$absent_daily_toolbox.
			$absents_daily_list
		)
	);
}
?>