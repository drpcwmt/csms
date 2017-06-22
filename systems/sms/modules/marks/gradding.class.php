<?php
/** Gradding
*
*/

class gradding{
	public $name = '',
	$id='',
	$scales= array();
	
	
	public function __construct($level_id = false, $id=''){
		if($level_id != false){
			$gradding = do_query_obj("SELECT gradding.* FROM gradding, levels WHERE levels.gradding=gradding.id AND levels.id=$level_id", DB_student);
			if(isset($gradding->id)){
				$this->name = $gradding->name;
				$this->id = $gradding->id;
			}
		} else {
			if($id !=''){
				$gradding = do_query_obj("SELECT * FROM gradding WHERE id=$id", DB_student);
				if(isset($gradding->id)){
					$this->name = $gradding->name;
					$this->id = $gradding->id;
				}
			}
		}
	}


	public function getGraddinArray(){
		if(count($this->scales) == 0){
			$sql = "SELECT * FROM gradding_points WHERE gradding_id=".$this->id." ORDER BY `min` DESC";
			$scales = do_query_array($sql, DB_student);
			if(count($scales) > 0) {
				$this->scales = $scales;
				return $scales;
			} else {
				return false;
			}
		}
		return $this->scales;
	}

	public function getStdGrad($result , $max){
		if($result != ''){
			$percent = ($result / $max ) *100;
			$res = do_query_obj("SELECT * FROM gradding_points WHERE min<=$percent AND max>=$percent AND gradding_id=".$this->id, DB_student);
			 if(isset($res->title)){
				 return $res;
			 } else {
				 $res = new stdClass();
				 $res->title = '';
				 $res->color = '000000';
				 return $res;
			 }
		} else {
			 $res = new stdClass();
			 $res->title = '';
			 $res->color = '000000';
			 return $res;
		}
	}
	
	static function getGpaGrad(){
		$gradding = do_query_obj("SELECT * FROM gradding WHERE name='gpa'", DB_student);
		if(isset($gradding->id)){
			$gpa = new gradding();
			$gpa->name = $gradding->name;
			$gpa->id = $gradding->id;
			return $gpa;
		} else {
			return false;
		}
	}
	
}
?>
