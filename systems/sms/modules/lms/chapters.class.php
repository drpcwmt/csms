<?php
/** Chapters
*
*/

class Chapters {
	
	public function __construct($id){
		if($id != ''){	
			$book = do_query_obj("SELECT * FROM chapters WHERE id=$id", LMS_Database);	
			if(isset($book->id)){
				foreach($book as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}
	
	public function getName(){
		return $this->title;
	}
	
	public function getUnits(){
		if(!isset($this->units)){
			$units = do_query_array("SELECT id FROM summarys WHERE chapter_id=$this->id", LMS_Database);
			$out = array();
			foreach($units as $unit){
				$out[] = new Units($unit->id);	
			}
			$this->units = $out;
		}
		return $this->units;
	}
	
	public function getUnitsTable(){
		$trs= array();
		$units = $this->getUnits();
		foreach($units as $unit){
			$trs[] = fillTemplate("modules/lms/templates/summary_list.tpl", $unit);
			/*$trs[] = write_html('tr', '',
				write_html('td', 'width="24"', 
					write_html('button', 'type="button" action="openSummary" summaryid="'.$unit->id.'" class="ui-state-default hoverable circle_button"', write_icon('extlink'))
				).
				write_html('td', '', $unit->getName())
			);*/
		}
		$out = write_html('table', 'class="result"',
			write_html('tbody', '', implode('', $trs))
		);
		
		return $out;
	}
	
	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			$result = do_update_obj($post, 'id='.$post['id'], 'chapters', LMS_Database);
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'chapters', LMS_Database);
		}
		if($result!=false){
			$answer['id'] = $result;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}

	static function _delete($id){
		if(do_query_edit("DELETE FROM chapters WHERE id=$id", LMS_Database)){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}
	
}