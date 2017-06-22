<?php
/** Terms
*
*/

class Terms{
	public $con = '', $con_id='';
		
	public function __construct($term_id){
		global $lang;
		$this->DB_year = Db_prefix.$_SESSION['year'];
		if($term_id != ''){	
			$term = do_query_obj("SELECT * FROM terms WHERE id=$term_id", $this->DB_year);	
			if(isset($term->id)){
				foreach($term as $key =>$value){
					$this->$key = $value;
				}
				$this->name =  $term->title != '' ? $term->title : $lang['term'].'-'.$term->term_no;
			}	
		}	
	}
	
	public function getName(){
		return $this->name;
	}
	
	
	public function getExamsByTerm($service_id){
		$out = array();
		$con_arr = array("(exams.con='$this->con' AND exams.con_id='$this->con_id')");
		$parents = getParentsArr($this->con, $this->con_id);
		if($parents != false){
			foreach($parents as $array){
				$par_con =$array[0];
				$par_id= $array[1];
				if($par_con != 'level'){
				$con_arr[] = "(exams.con='$par_con' AND exams.con_id='$par_id')";
				}
			};
		}
		$sql = "SELECT id FROM exams
			WHERE exams.term_id=$this->id
			AND exams.service = $service_id
			AND ( ".implode(' OR ', $con_arr).") 
			GROUP BY exam_no
			ORDER BY FIELD(exams.con,'student', 'group', 'class', 'level'), exam_no ASC";
		$exams = do_query_array($sql, $this->DB_year);
		foreach($exams as $ex){
			$out[] = new exams($ex->id);
		}
		return $out;
			
	}
		
	static public function getTermsByCon($con, $con_id){
		global $sms;
		$obj = $sms->getAnyObjById($con, $con_id);
		$level = $obj->getLevel();
		$level_id = $level->id;
		$sql = "SELECT id FROM terms WHERE level_id=$level_id ORDER BY term_no ASC";
		$r = do_query_array($sql, Db_prefix.$_SESSION['year']);	
		if(count($r)> 0){
			$terms = array();
			foreach($r as $t){
				$terms[] = new terms($t->id);
			}
			return $terms;
		} else {
			return false;
		}
	}

	static public function getCurentTerm($con, $con_id){
		global $sms;
		$now = time();
		$obj = $sms->getAnyObjById($con, $con_id);
		$level_id = $obj->getLevel()->id;
		$sql = "SELECT * FROM terms WHERE level_id=$level_id AND begin_date<=$now AND end_date>$now";
		$term = do_query_obj($sql, Db_prefix.$_SESSION['year']);
		if(isset($term->id )){
			return new terms($term->id);
		} else {
			$sql = "SELECT * FROM terms WHERE level_id=$level_id AND end_date<$now ORDER BY term_no DESC LIMIT 1";
			$term = do_query_obj($sql, Db_prefix.$_SESSION['year']);
			if(isset($term->id)){
				return new terms($term->id);
			} else {
				return false;
			}
		}
	}
	
	static public function getTermByDate($level_id, $date){
		$terms = terms::getTermsByCon('level', $level_id);
		if($terms != false && count($terms) > 0){
			foreach($terms as $term){
				if($term->begin_date<= $date && $term->end_date >= $date){
					return $term;
				}
			}
			return false;
		} else {
			return false;
		}
	}
	
	static public function getTermByno($level_id, $term_no){
		$terms = terms::getTermsByCon('level', $level_id);
		if($terms != false && count($terms) > 0){
			foreach($terms as $term){
				if($term->term_no=$term_no){
					return $term;
				}
			}
		}
		return false;
	}
	
	static function approveTerm($term_id){
		$error = false;
		if(getPrvlg('mark_approv')){
			if(do_query_edit("UPDATE terms SET approved=1 WHERE id=$term_id", $this->DB_year)){
				do_query_edit("UPDATE exams SET approved=1 WHERE term_id=$term_id", $this->DB_year);
			} else {
				$error = $lang['error_updating'];
			}
		} else {
			$error = $lang['no_privilege'];
		}
		$answer = array();
		if(!$error){
			$answer['id'] = $term_id;
		} else {
			$answer['id'] = "";			$answer['error'] = "";

			$answer['error'] = $error;
		}
		return json_encode($answer);
	}

	static function unApproveTerm($term_id){
		$error = false;
		if(getPrvlg('mark_approv')){
			if(!do_query_edit("UPDATE terms SET approved=0 WHERE id=$term_id", $this->DB_year)){
				$error = $lang['error_updating'];
			}
		} else {
			$error = $lang['no_privilege'];
		}
		$answer = array();
		if(!$error){
			$answer['id'] = $term_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $error;
		}
		return json_encode($answer);
	}
	
	static function getTermsSelect($con, $con_id){
		global $lang;
		$out = array();
		$terms = terms::getTermsByCon($con, $con_id);
		if($terms != false){
			foreach($terms as $term){
				$out[$term->id] = $term->title != '' ? $term->title : $lang['term'].'-'.$term->term_no;
			}
		}
		return $out;
	}
		
	//  material => term (percent)
	public function get_mat_term_per($service, $con, $con_id){
		$this->con = $con;
		$this->con_id = $con_id;
		$exams = $this->getExamsByTerm($service->id);
		$total_res = 0;
		foreach($exams as $exam){
			if($con == 'student'){
				$results = $exam->getExamResults();
				$result = isset($results[$con_id]) ? $results[$con_id] / $exam->max : 0;
			} else {
				$statics = $exam->getStatics();
				$result = $statics->avg / ($exam->max > 0 ? $exam->max : 1);
			}
			$total_res = $total_res + ($result * $exam->value);
		}
		return round($total_res,1);

		/*if($con == 'student'){
			$result = 0;
			if($service != false && is_object($service)){
				$sql = "SELECT exams.*, exams_results.results FROM exams, exams_results
				WHERE exams_results.std_id=$con_id
				AND exams_results.exam_id=exams.id ".
				($this->id !=0 ? "AND exams.term_id=$this->id " : '').
				"AND exams.service=$service->id
				GROUP BY exams.exam_no";
				$exams = do_query_array($sql, $this->DB_year);
				foreach($exams as$exam){
					$exam_per = $exam->results / $exam->max;
					$result = $result + ($exam_per * $exam->value);
				}
			}
			return round($result, 1);
		} else {
			$sql = "SELECT id, value, max FROM exams
			WHERE exams.con='$con'
			AND exams.con_id=$con_id
			AND exams.term_id=$this->id
			AND exams.service = $service->id
			GROUP BY exam_no";
			$exams = do_query_array($sql, $this->DB_year);
			$total = 0;
			foreach($exams as $exam){
				$ex = new exams($exam->id);
				$statics = $ex->getStatics();
				$value = $exam->value;
				$max = $exam->max > 0 ? $exam->max : 1;
				$per = $statics->avg/$max;
				$total = $total + ($per * $value);
			}
			return round($total,1);
		}*/
	}
	
	// material => term (grad)
	public function get_mat_term_grad($service, $con, $con_id){
		$con_arr = array("(exams.con='$con' AND exams.con_id='$con_id')");
		$parents = getParentsArr($con, $con_id);
		if($parents != false){
			foreach($parents as $array){
				$par_con =$array[0];
				$par_id= $array[1];
				if($par_con != 'level'){
				$con_arr[] = "(exams.con='$par_con' AND exams.con_id='$par_id')";
				}
			};
		}
		
		$sql = "SELECT COUNT(exams_results.results) AS count, exams_results.results
			FROM exams, exams_results 
			WHERE exams.term_id=$this->id 
			AND service=$service->id 
			AND ( ".implode(' OR ', $con_arr).") ".
			($con == 'student' ? " AND exams_results.std_id=$con_id " : '').
			"AND exams.id=exams_results.exam_id
			GROUP BY exams_results.results
			ORDER BY FIELD(exams.con, 'student', 'group', 'class', 'level'), count ASC
			LIMIT 1";
		$results = do_query_obj($sql, DB_year);
		return isset($results->results) ? $results->results : '';
	}
	
	//  material => term (points)
	public function get_mat_term_points($service, $con, $con_id){
		$exams = $this->getExamsByTerm($service->id);
		$total_res = 0;
		foreach($exams as $exam){
			if($con == 'student'){
				$results = $exam->getExamResults();
				$result = $results[$con_id];
			} else {
				$statics = $exam->getStatics();
				$result = $statics->avg;
			}
			$total_res += $result;
		}
		return round($total_res,1);
	}

	// material => term (avg)
	public function get_mat_term_avg($service, $con, $con_id){
		$exams = $this->getExamsByTerm($service->id);
		$total_coef = 0;
		$total_res = 0;
		foreach($exams as $exam){
			$total_coef += $exam->coef;
			if($con == 'student'){
				$results = $exam->getExamResults();
				$result = isset($results[$con_id]) ? $results[$con_id] : '';
			} else {
				$statics = $exam->getStatics();
				$result = $statics->avg;
			}
			$total_res += $result * $exam->coef;
		}
		return round($total_res / ($total_coef > 0 ? $total_coef : 1) ,1);;
	}
	
	// appreciation : return min max, and avg
	public function get_class_term_statics($service){
		$out = new stdClass();
		$out->min = 0;
		$out->max = 0;
		$out->avg = 0;
		$calc_type = marks::getLevelCalcType($service->level_id);
		$class = array();
		$skills_vals = array();
		if($calc_type == "skills"){
			$con_arr = array("(exams.con='$this->con' AND exams.con_id='$this->con_id')");
			$parents = getParentsArr($this->con, $this->con_id);
			if($parents != false){
				foreach($parents as $array){
					$par_con =$array[0];
					$par_id= $array[1];
					if($par_con != 'level'){
					$con_arr[] = "(exams.con='$par_con' AND exams.con_id='$par_id')";
					}
				};
			}
			$query = do_query_array("SELECT MIN(exams_results.results) AS min, MAX(exams_results.results) as max FROM exams, exams_results 
				WHERE exams.service=$service->id
				AND exams.term_id=$this->id
				AND exams.id=exams_results.exam_id
				AND exams_results.results IS NOT NULL
				AND ( ".implode(' OR ', $con_arr).") 
				GROUP BY FIELD(exams.con, 'student', 'group', 'class', 'level'), exam_no ASC
				ORDER BY exam_no", $this->DB_year);
			$gradding = new gradding($service->level_id);
			$grads = $gradding->getGraddinArray();
			$min='';
			$max='';
			$i =1;
			if($grads != false && $query!=false){
				foreach($grads as $key => $value){
					if($i == $query['min']){
						$max = $key;
					}
					if($i == $query['max']){
						$min = $key;
					}
					$i++;
				}
			}
			$out->min = $min;
			$out->max = $max;
			$out->avg = $this->get_mat_term_grad($service, $this->con, $this->con_id);
		} else {
			if($this->con == "student"){
				$std = new Students($this->con_id);
				$students = getStdIds('class', $std->getClass()->id);
			} else {
				$students = getStdIds($this->con, $this->con_id);
			}
			foreach($students as $std_id){
				if($calc_type == "per"){
					$val = $this->get_mat_term_per($service, 'student', $std_id);
				} elseif($calc_type == "moyen"){
					$val = $this->get_mat_term_avg($service, 'student', $std_id);
				} elseif($calc_type == "marks"){
					$val = $this->get_mat_term_points($service, 'student', $std_id);	
				}
				if($val != false && $val !=0){
					$class[] = $val;
				}
			}
			if(count($class) > 0){
				$min = min($class);
				$max = max($class);
				$avg = array_sum($class)/count($class);
				$out->min = $min;
				$out->max = $max;
				$out->avg = $avg;
			} 
		}
		return $out;
	}
}