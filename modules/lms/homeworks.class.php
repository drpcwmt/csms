<?php
/** Homeworks
*
*/

class Homeworks{

	public function __construct($id){
		global $lang;
		if($id != ''){	
			$homework = do_query_obj("SELECT * FROM homeworks WHERE id=$id", LMS_Database);	
			if($homework->id != ''){
				foreach($homework as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}
	
	public function toList(){
		$homework  = $this;
		$homework->shared_attr  = ($note->shared == '1') ? 'shared="1"' : '';
		return fillTemplate("modules/lms/templates/homeworks_list.tpl", $homework);
	}
	
	static function _save($post, $lesson_id){
		global $sms;
		$answer = array();
		// if this is an answer
		if(isset($post['answer'])){
			$std_id = $_SESSION['user_id'];
			$homework_id = $post['id'];
			$fileds = array( 
				'answer_date' => time(),
				'answer ' => $post['answer'],
				'std_id' => $std_id,
				'homework_id' => $homework_id
			);
			$chk = do_query_resource("SELECT answer FROM homeworks_answer WHERE std_id=$std_id AND homework_id=$homework_id" ,LMS_Database);
			if(mysql_num_rows($chk) > 0){
				if(UpdateRowInTable("homeworks_answer", $fileds, "std_id=$std_id AND homework_id=$homework_id", LMS_Database)){
					$result = true;
				}
			} else { // new homework
				if(insertToTable("homeworks_answer", $fileds, LMS_Database)){
					$result = true;
				}
			}
				
			if($result){
				$answer['error'] = '';
				$answer['id'] = $homework_id;
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		} else {
			$lesson = new Lessons($lesson_id);
			if($lesson ->is_editable()){
				$answer_date = ($post['answer_date'] != '') ? $post['answer_date'] : '';
				$fileds = array( 
					'date' => dateToUnix($post['date']), 
					'answer_date' => $answer_date,
					'lesson_id' => $lesson_id,
					'marks ' => $post['marks'],
					'points' => $post['points'],
					'time' => timeToUnix($post['time']),
					'duration' => $post['duration']
				);
				if($post['homework'] != ''){
					$fileds['content'] = $post['homework'];
				} elseif(isset($post['exercise_id']) && $post['exercise_id'] != ''){
					$fileds['exercise_id'] = $post['exercise_id'];
				} 
				
				if($post['id'] !=''){ // edit homework
					if(UpdateRowInTable("homeworks", $fileds, "id=".$post['id'], LMS_Database)){
						$homework_id = $post['id'];
						$result = true;
					}
				} else { // new homework
					if(insertToTable("homeworks", $fileds, LMS_Database)){
						$homework_id = mysql_insert_id();
						$result = true;
					}
				}
				
				if(isset($homework_id)){
					$sql_link = array();
					do_query_edit("DELETE FROM homeworks_attachs WHERE homework_id=$homework_id", LMS_Database);
					if($post['attachements'] != ''){
						$links = explode(',', $post['attachements']);
						foreach($links as $link){
							$link != '' ? $sql_link[] = "($homework_id, '$link')" : '';
						}
						do_query_edit("INSERT INTO homeworks_attachs (homework_id, link) VALUES ". implode(',', $sql_link), LMS_Database);
					}
				}
				
				if($result){
					$fileds['id'] = $homework_id;
					$answer['id'] = $homework_id;
					$answer['error'] = "";
					$answer['html'] = fillTemplate("modules/lms/templates/homeworks_list.tpl", $fileds);
					if($sms->getSettings('alert_student_homework') == 1){
						systemMessages::sendHomeworkMessage($lesson->con, $lesson->con_id, $homework_id, dateToUnix($post['date']), '', '');
					}
				} else {
					$answer['id'] = "";
					$answer['error'] = $lang['error_updating'];
				}
			} else {
				$answer['id'] = "";
				$answer['error'] = $lang['no_privilege'];
			}
		}
		return $answer;
	}
	
	 static function _delete($post){
		$answer = array();
		$homework = do_query("SELECT service_id FROM homeworks WHERE id=".$_POST['delhomework'], LMS_Database);
		$lesson = new Lessons($homework['lesson_id']);
		if($lesson->is_editable()){
			$sql = "DELETE FROM homeworks WHERE id=".$_POST['delhomework'];
			if(do_query_edit($sql, LMS_Database)){
				$answer['id'] = $_POST['delhomework'];
				$answer['error'] = "";
				if($MS_settings['alert_student_homework'] == 1){
					SysmtemMessages::sendHomeworkDelMessage($_POST['delhomework']);
				}
			} else {
				$answer['id'] = "";
				$answer['error'] = $sql;
			}
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['no_privilege'];
		}
		return $answer;
	}
}
