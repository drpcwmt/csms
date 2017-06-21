<?php
require_once('scripts/new_year_functions.php');
if(!getPrvlg('new_year')){die($lang['restrict_accses']);};
/*******************************************************************************************************/

// Step 1
if(isset($_POST['wizard_step']) && $_POST['wizard_step'] == 0){
	echo NewYear::datesScreen();

// Step 2
} elseif(isset($_POST['wizard_step']) && $_POST['wizard_step'] == 1){
	if(NewYear::createDB($_POST) == true){
		echo NewYear::transfereScreen();
	} else {
		echo write_error($lang['error_copy_db']).'<input type="hidden" name="wizard_step" id="wizard_step" value="1" />';
	}
	exit;
} elseif(isset($_POST['wizard_step']) && $_POST['wizard_step'] == 2){	
	$old_year = $_SESSION['year'];
	$new_year = ($_SESSION['year']+1);
	$error = false;
	if(MS_codeName!= 'sms_basic' && isset($_POST['copy_service'])){
		$services = true;
		if(copy_db_data(Db_prefix.$old_year.".services", Db_prefix.$new_year.".services")){
			copy_db_data(Db_prefix.$old_year.".services_subs", Db_prefix.$new_year.".services_subs") ;
		} else {
			$error = 'error copy service';
			$service = false;
		} 
	} else {
		$services = false;	
	}
		// Transfer Student grouped by old class
	if(!$error && isset($_POST['transfer_stds'])){
		$marks = isset($_POST['all_marks']) && $_POST['all_marks'] == 1 ? false : true;
		if(!NewYear::transferStudent($old_year, Db_prefix.$new_year, $services, $marks)){
			$error = 'Error transfer students';
		}

			// Copy terms
		if(MS_codeName!= 'sms_basic' && isset($_POST['copy_terms'])){
			if(NewYear::copyTerms(Db_prefix.$old_year, Db_prefix.$new_year, $_POST ) == false){
				$error = 'Error copy terms';
			}
		}
	
			// Copy Schedule
		if(MS_codeName!= 'sms_basic' && $services!= false && isset($_POST['copy_schedule'])){
			if(NewYear::copyScheduleStructure(Db_prefix.$old_year, Db_prefix.$new_year) == false){
				$error = 'Error copy Schedules';
			}
		}
			// Set the session to the new year
		//$_SESSION['year'] = $new_year;

			// Geneare groups by service oprional
		if(MS_codeName!= 'sms_basic' && !$error && $services!= false && isset($_POST['generate_optional_groups'])){
			if(NewYear::generatOptionalGroup( Db_prefix.$new_year) == false){
				$error = "Error generating optional groups";
			}
		}
			// Create Religion groups
		if(MS_codeName!= 'sms_basic' && !$error && isset($_POST['generate_religion_groups'])){
			NewYear::generatReligionGroup( Db_prefix.$new_year, ($services? array($_POST['ser_muslim'],$_POST['ser_christian']) : false) );
		}
		
	}

	if($error != false ){
		echo write_error($error).'<input type="hidden" name="wizard_step" id="wizard_step" value="error" />';
	//	$_SESSION['year'] = $old_year;
	} else {
			// Set session to the new db
		if($MS_settings['safems_server'] == 1){
			echo NewYear::SchoolFeesLayout();
		} else {
			echo write_html('h3', 'style="padding-top:50px; text-align:center"', $lang['new_year_finish']).
			'<input type="hidden" name="wizard_step" id="wizard_step" value="finish" />';
			$_SESSION['year'] = $new_year;
			echo SchoolReport::loadSchoolBalance($new_year);
			$_SESSION['year'] = $old_year;
		}
	}
	exit;
	
} elseif(isset($_POST['wizard_step']) && $_POST['wizard_step']==3){
	if($MS_settings['safems_server'] == 1){
		if(isset($_POST['fees_action']) && $_POST['fees_action'] == 1){
			Fees::applyNewYearIncreas($_POST['percent']);
		}
		$dates = $sms->getDates();
		foreach($dates as $date){
			$ins = array(
				'con'=>'',
				'con_id'=>0,
				'title'=> $date->title,
				'from'=> mktime(0,0,0, date('m', $date->from), date('d', $date>from), date('Y', $date>from)+1),
				'limit'=> mktime(0,0,0, date('m', $date->limit), date('d', $date>limit), date('Y', $date>limit)+1),
				'year' => $_SESSION['year'] + 1
			);
			do_insert_obj($ins, 'school_fees_dates', $sms->database);
		}
	}
	echo write_html('h3', 'style="padding-top:50px; text-align:center"', $lang['new_year_finish']).
	'<input type="hidden" name="wizard_step" id="wizard_step" value="finish" />';
	$_SESSION['year']++;
	echo SchoolReport::loadSchoolBalance();
	$_SESSION['year']--;
} 


/*********** CLEAR CHANGES ********************************************************************************/
if(isset($_POST['resetyear'])){
	if(isset($_POST['new']) && $_POST['new']==1){
		$old_year = ($_SESSION['year']-1);
		$new_year = $_SESSION['year'];
	} else {
		$old_year = $_SESSION['year'];
		$new_year = ($_SESSION['year']+1);
	}
	if(!deleteDatabase(Db_prefix.$new_year)){
		echo json_encode(array('error' => $lang['error']));
	} else {
		$_SESSION['year'] = $old_year;
		do_query_edit("DELETE FROM years WHERE `year`=$new_year", DB_student);
		do_query_edit("UPDATE settings SET value='1' WHERE key_name='system_stat'", DB_student);
		echo json_encode(array('error' => ''));
	}
}

/*********** Finalize Year ********************************************************************************/
if(isset($_POST['finalize'])){
	if($_POST['finalize'] == 0){ //step 1
		echo NewYear::addRepearters();
	} elseif($_POST['finalize'] == 1) { // step 2
			// Update reapater student classes
		$students = do_query_array("SELECT std_id AS id FROM final_result WHERE result=0", Db_prefix.$_SESSION['year']);
		NewYear::removeStudents($students, $_SESSION['year']+1);
		if(isset($_POST["std_id"])){
			for($i=0; $i<count($_POST['std_id']);$i++){
				$ins =array(
					'std_id'=>	$_POST['std_id'][$i],
					'class_id'=> $_POST['class_id'][$i],
					'new_stat'=>0
				);
				do_insert_obj($ins, 'classes_std', Db_prefix.($_SESSION['year']+1));
				$stat = array('status'=> '1');
				do_update_obj($stat, 'id='.$_POST['std_id'][$i], 'student_data', MySql_Database);
				
			}
		}
		echo NewYear::addQuited();
	} elseif($_POST['finalize'] == 2) { // step 3
		$next_year = do_query_obj("SELECT * FROM years WHERE year=".($_SESSION['year']+1), MySql_Database); 
		$end_date = $next_year != false ? $next_year->begin_date : getYearSetting('end_date');
		$year_begin = getYearSetting('begin_date');
		$students = do_query_array("SELECT id FROM student_data WHERE status=0", MySql_Database);		
		$_SESSION['year']++;
		NewYear::removeStudents($students, $next_year->year);
		echo NewYear::getWaitingList();
		$_SESSION['year']--;
	} else {
		$_SESSION['year']++;
		echo '<input type="hidden" name="finalize" value="finish" />'.
		schoolReport::loadSchoolStatics();	
		$_SESSION['year']--;
	}
} elseif(isset($_GET['add_repeaters'])){
	$stds = strpos($_POST['std_ids'], ',') !== false ? explode(',', $_POST['std_ids']) : array($_POST['std_ids']);
	$result = true;
	foreach($stds as $std_id){
		$student = new Students($std_id);
		$class = $student->getClass();
		$level= $class->getLevel();
		
		$res = array(
			'std_id' => $std_id,
			'class_name'=> $class->getName(),
			'level_name'=> $level->getName(),
			'result'=>0
		);
		if(do_insert_obj($res, 'final_result', DB_year) == false){
			$result = false;
		}
	}
	
	if($result){
		echo json_encode(array('error' => ''));
	}
}

?>