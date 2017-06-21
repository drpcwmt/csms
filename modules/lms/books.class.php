<?php
/** Books
*
*/

class Books {
	
	public function __construct($id){
		if($id != ''){	
			$book = do_query_obj("SELECT * FROM books WHERE id=$id", LMS_Database);	
			if(isset($book->id)){
				foreach($book as $key =>$value){
					$this->$key = $value;
				}
				$this->editable = Services::check_user_service_privilege($this->service_id);
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}
	
	public function getName(){
		return $this->title;
	}
	
	public function getChapters(){
		if(!isset($this->chapters)){
			$out = array();
			$chapters = do_query_array("SELECT id FROM chapters WHERE book_id=$this->id", LMS_Database);
			foreach($chapters as $chapter){
				$out[] = new Chapters($chapter->id);	
			}
			$this->chapters = $out;
		}
		return $this->chapters;
	}
	
	public function loadLayout(){
		global $lang;
		$service_id = $this->service_id;
		$bl = new stdClass();
		$bl->id = $this->id;
			// Chapters colone
		$chapters = $this->getChapters();
		if(count($chapters) > 0){
				// Units Colone
			$first_chapter = $chapters[0];
			$bl->units_table = $first_chapter->getUnitsTable();

			$chapter_list = '<ul style="list-style:none; padding:0; margin:0">';
			foreach($chapters as $chapter){
				$chapter_list .=write_html('li', 'style="display:block;padding:5px;" class="hand ui-state-default hoverable clickable ui-corner-all '.($chapter == $first_chapter ? 'ui-state-active' : '').'" chapterid="'.$chapter->id.'" bookid="'.$chapter->book_id.'" serviceid="'.$service_id.'" action="displaySummaryList" title="'.$chapter->getName().'"',
					write_html('span', 'style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"',
						write_html('text', 'class="holder-chapter-'.$chapter->id.'"', $chapter->getName())
					).
					($this->editable ?
						write_html('a', 'action="editChapter" chapterid="'.$chapter->id.'" chaptername="'.$chapter->getName().'" bookid="'.$this->id.'" serviceid="'.$service_id.'" class="rev_float ui-state-default ui-corner-all hoverable mini_circle_button"', write_icon('pencil'))
					: '')
				);
			}
			$chapter_list .= '</ul>';
			$bl->chapters_list = $chapter_list;
		}

			// toolbox
		if($this->editable){
			$toolbox = array();
			$toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="editChapter"  serviceid="'.$this->id.'" bookid="'.$this->id.'" title="'. $lang['new_chapter'].'"',
				"text"=> $lang['new_chapter'],
				"icon"=> "plus"
			);
			$toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="openSummary"  serviceid="'.$this->id.'" bookid="'.$this->id.'" '.(isset($first_chapter) ?  'chapterid="'.$first_chapter->id.'"' : '').' title="'. $lang['new_summary'].'"',
				"text"=> $lang['new_summary'],
				"icon"=> "plus"
			);
			
		}
		$bl->books_toolbox = createToolbox($toolbox);
		return  fillTemplate("modules/lms/templates/books.tpl", $bl);
	}

	public function getUnits(){
		if(!isset($this->units)){
			$units = do_query_array("SELECT id FROM summarys WHERE book_id=$this->id", LMS_Database);
			$out = array();
			foreach($units as $unit){
				$out[] = new Units($unit->id);	
			}
			$this->units = $out;
		}
		return $this->units;
	}
	
	static function getList($service_id){
		$out = array();
		$books = do_query_array("SELECT id FROM books WHERE service_id=$service_id", LMS_Database);
		foreach($books as $book){
			$out[] = new Books($book->id);	
		}
		
		return $out;
	}
	
	static function bookListLayout($service_id){
		global $lang;
		$books = Books::getList($service_id);
		$books_list = '';
		foreach($books as $book){
			$books_list .= write_html('h3', 'bookid="'.$book->id.'"', 
				write_html('a', '', 
					write_html('text', 'class="holder-book-'.$book->id.'"', $book->getName())
				)
			).
			write_html('div', '', $book->loadLayout());
		}
		
			// Create all layouts
		$bookListLayout = new stdClass();
		$bookListLayout->toolbox = createToolbox(array( array(
				"tag" => "a",
				"attr"=> 'action="editBook" serviceid="'.$service_id.'" title="'. $lang['new_book'].'"',
				"text"=> $lang['new_book'],
				"icon"=> "plus"
			)
		));
		$bookListLayout->books_list = $books_list;
		
		return fillTemplate("modules/lms/templates/books_layout.tpl", $bookListLayout);
	}

	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			$result = do_update_obj($post, 'id='.$post['id'], 'books', LMS_Database);
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'books', LMS_Database);
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
		if(do_query_edit("DELETE FROM books WHERE id=$id", LMS_Database)){
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