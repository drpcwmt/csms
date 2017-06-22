<?php


if(isset($_GET['con'])){
	$con = $_GET['con'];
	$con_id = $_GET['con_id'];	
}

/*********** Default body ***************/
if(isset($_GET['delete'])){ // delete Event
	$answer = array();
	if(isset($_POST['holidayid'])){
		if(getPrvlg('cal_holiday_edit')){
			$id = $_POST['holidayid'];
			if(!do_query_edit("DELETE FROM holidays WHERE id=$id", DB_year)){
				$answer['error'] = $lang['error_updating'];
			} else {
				$answer['error'] = '';
			}
		}  else {
			$answer['error'] = $lang['not_enough_privilege'];
		}
	} elseif(isset($_POST['event'])){
		if(getPrvlg('cal_event_edit')){
			$id = $_POST['event'];
			$event = new cEvents($id);
			$answer = $event->_delete();
		}  else {
			$answer['error'] = $lang['not_enough_privilege'];
		}
	}
	echo json_encode($answer);
	
} elseif(isset($_GET['eventsform'])){
	if(getPrvlg('cal_event_edit') || getPrvlg('cal_holiday_edit')){
		$date = $_GET['day'] != '' ? safeGet($_GET['day']) : mktime(0,0,0, date('m'), date('d'), date('Y'));
		echo cEvents::_new($date);
	} else {
		echo write_error($lang['not_enough_privilege']);
	}
} elseif(isset($_GET['openevent'])){ // edit Events
	$events_id = $_GET['event_id'];
	$event = new cEvents($events_id);
	echo $event->loadLayout();

} elseif(isset($_GET['save_event'])){
	if((getPrvlg('cal_event_edit') && $_POST['event_type'] != '0') || (getPrvlg('cal_holiday_edit') && $_POST['event_type'] == '0')){
		echo  json_encode(cEvents::_save($_POST));
	} else {
		echo write_error($lang['not_enough_privilege']);
	}
} elseif(isset($_GET['day'])){
	$date = dateToUnix(safeGet('day'));
	$calendar = new Calendars();
	echo $calendar->getDate($date);
	
} elseif(!isset($_GET['view']) || $_GET['view'] == 'year'){
	$calendar = new Calendars();
	echo $calendar->loadMainLayout().write_script('iniCalender()');
}
?>