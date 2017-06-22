<?php
/** Notes
*
*/

class Notes{

	public function __construct($id){
		global $lang;
		if($id != ''){	
			$note = do_query_obj("SELECT * FROM schedules_notes WHERE id=$id", DB_year);	
			if($note->id != ''){
				foreach($note as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}
	
	public function toList(){
		$note  = $this;
		$note->shared_attr  = ($note->shared == '1') ? 'shared="1"' : '';
		return fillTemplate("modules/lessons/templates/notes_list.tpl", $note);
	}
	
}
