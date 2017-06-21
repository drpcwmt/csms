<?php
/** Summarys
*
*/

class Summarys{

	public function __construct($id){
		if($id != ''){		
			$summary = do_query_obj("SELECT * FROM summarys WHERE id=$id", LMS_Database);	
			if(isset($summary->id)){ 
				foreach($summary as $key =>$value){
					$this->$key = $value;
				}
			} else {
				//throw new Exception('id Not Found');
			}	
		} else {
			//throw new Exception('id Not Found');
		}
			
	}

	public function getBook(){
		if(isset($this->book_id) && $this->book_id != ''){
			return new Books($this->book_id);
		} else {
			return false;
		}
	}

	public function getChapter(){
		if(isset($this->chapter_id) && $this->chapter_id != ''){
			return new Chapters($this->chapter_id);
		} else {
			return false;
		}
	}
	
	public function is_editable(){
		if(!isset($this->editable)){
			if($this->service_id != ''){
				$this->editable = Services::check_user_service_privilege($this->service_id);
			} else {
				$this->editable = false;
			}
		} 
		return $this->editable;
	}
	
	public function read(){
		$layout = $this;
		$layout->class_id = 'summary_form-'.$this->id;
		$layout->book_name = $this->getBook()->getName();
		$layout->chapter_name = $this->getChapter()->getName();
		$layout->attachements_list = $this->getAttachsTable();
		return fillTemplate("modules/lms/templates/summary_read.tpl", $layout); 
	}
	
	
	public function edit(){
		$layout = $this;
		$layout->class_id = 'summary_form-'.$this->id;
		$layout->attachements_list = $this->getAttachsTable();
		return fillTemplate("modules/lms/templates/summary_edit.tpl", $layout); 
	}
	
	static function newForm(){
		$layout = $this;
		$layout->class_id = 'summary_form-new';
		return fillTemplate("modules/lms/templates/summary_edit.tpl", $layout); 
	}
	
	
	public function getAttachsTable(){
		$attachements = do_query_array("SELECT link FROM summarys_attachs WHERE summary_id=$this->id", LMS_Database);
		$file_arr = array();
		if($attachements != false && count($attachements) > 0){
			foreach($attachements as $attach ){
				$file_arr[] = new Files($attach->link);
			}
			$options = new stdClass();
			$options->selectable = false;
			$options->sharable = false;
			$options->editable = false;
			$options->openable = true;
			$options->mini = true;
			$documents = new Documents();
			$attach_div = $documents->loadListView(array('files' => $file_arr), $options);
		} else {
			$attach_div = '';
		}
		return $attach_div;

	}
	
	static function _delete($sum_id){
		if(do_query_edit("DELETE FROM summarys WHERE id=$sum_id", LMS_Database)){
			do_query_edit("DELETE FROM lessons_summary WHERE summary_id=$sum_id", LMS_Database);
			do_query_edit("DELETE FROM summarys_attachs WHERE summary_id=$sum_id", LMS_Database);
			$answer['id'] = $sum_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return $answer;
	}

	static function _save($post){
		$answer = array();
		if($post['id'] !='' && $post['id'] !='new'){ // edit homework
			if(UpdateRowInTable("summarys", $post, "id=".$post['id'], LMS_Database)){
				$summary_id = $post['id'];
				$result = true;
			}
		} else { // new homework
			if(insertToTable("summarys", $post, LMS_Database)){
				$summary_id = mysql_insert_id();
				$result = true;
				$summary = do_query_row("SELECT summarys.*, books.title AS book_name, chapters.title AS chapter_name FROM summarys, books, chapters WHERE summarys.id=$summary_id AND summarys.chapter_id=chapters.id AND summarys.book_id=books.id", LMS_Database);
				$answer['html'] = fillTemplate("modules/lms/templates/summary_list.tpl", $summary);
			}
		}
		
		if(isset($summary_id)){
			$sql_link = array();
			do_query_edit("DELETE FROM summarys_attachs WHERE summary_id=$summary_id", LMS_Database);
			if($post['attachements'] != ''){
				$links = explode(',', $post['attachements']);
				foreach($links as $link){
					$link != '' ? $sql_link[] = "($summary_id, '$link')" : '';
				}
				do_query_edit("INSERT INTO summarys_attachs (summary_id, link) VALUES ". implode(',', $sql_link), LMS_Database);
			}
		}
		
			// Attach to lesson
		if(isset($post['lesson_id']) && $post['lesson_id']!=''){
			$lesson_id = $post['lesson_id'];
			if(count(do_query_array("SELECT * FROM lessons_summary WHERE lesson_id=$lesson_id AND summary_id=$summary_id", LMS_Database))<1){
				do_query_edit("INSERT INTO lessons_summary (lesson_id, summary_id) VALUES ($lesson_id, $summary_id)", LMS_Database);	
			}
		}
		
		if($result){
			$answer['id'] = $summary_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return $answer;
	}
}