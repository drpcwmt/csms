<?php
/** ToDo class
*
*/

class ToDo{
	private $thisTemplatePath = 'modules/todo/templates';

	public function __construct($id= ''){
		$this->DB_year = Db_prefix.$_SESSION['year'];
		if($id != ''){	
			$todo = do_query_obj("SELECT * FROM todo WHERE id=$id", $this->DB_year);	
			if(isset($todo->id)){
				foreach($todo as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}
	
	static function getList($from=0, $max=10){
		$user_id = $_SESSION['user_id'];
		$group = $_SESSION['group'];
		return do_query_array("SELECT id FROM todo WHERE con='group' AND con_id=$user_id LIMIT $from, $max", DB_year);
	}
	
	static function getListTable($from=0, $max=10){
		global $lang;
		$jobs = ToDo::getList($from, $max);
		$trs = array();
		if(count($jobs) > 0){
			foreach($jobs as $job){
				$job->icon_done = $job->done == 1 ? 'ui-icon-checked' : '';
				$job->icon_checked = $job->checked == 1 ? 'ui-icon-checked' : '';
				$job->font_stat = $job->done == 1 ? '500' : 'bold';
				$trs[] = fillTemplate("modules/todo/templates/todo_list.tpl", $job);
			}
			
			return write_html('table', 'class="result"',
				implode('', $trs)
			);
		} else {
			return $lang['no_jobs'];
		}
	}

	
}
