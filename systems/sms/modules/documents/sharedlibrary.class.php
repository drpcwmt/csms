<?php
/** Document shared librarys 
*
*
*/

class sharedLibrary{
	private $database = LMS_Database;
	
	public function __construct($id){
		$lib = do_query_obj("SELECT * FROM files_librarys WHERE id=$id", $this->database);	
		if(isset( $lib->id )){
			foreach($lib as $key =>$value){
				$this->$key = $value;
			}
		} else {
			return false;
		}	
		$this->path = 'attachs/'.trim($lib->path);
		$this->max_size = $lib->size * 1048576;
		$this->folder = new Folders($this->path);
		$this->cur_size = $this->folder->getSize();
		$this->can_upload = ($this->max_size != 0 && $this->cur_size > $this->max_size*1048576) ? false : true;

		$read_arr = array();
		$reads = do_query_array("SELECT con, con_id FROM files_librarys_shares WHERE lib_id=$id AND `read`= 1", $this->database);
		foreach($reads as $read){
			$read_arr[] = $read->con.($read->con_id	!= '0' ? '-'.$read->con_id: '');
		}
		$this->canread = $read_arr;

		$write_arr = array();
		$writes = do_query_array("SELECT con, con_id FROM files_librarys_shares WHERE lib_id=$id AND `write`= 1", $this->database);
		foreach($writes as $write){
			$write_arr[] = $write->con.($write->con_id!= '0' ? '-'.$write->con_id: '');
		}
		$this->canwrite = $write_arr;
	}
	
	public function is_editable(){
	// check editable
		$acess_sql = "SELECT files_librarys_shares.`write` FROM files_librarys_shares 
			WHERE files_librarys_shares.`lib_id`=$this->id
			AND files_librarys_shares.`write`=1";
		if($_SESSION['group'] == 'student'){
			$parents = getParentsArr('student', $_SESSION['user_id']);	
			foreach($parents as $array){
				$con = $array[0];
				$con_id = $array[1];
				$where[] = "(files_librarys_shares.con='$con' AND files_librarys_shares.con_id=$con_id)";
			}
			$acess_sql .= " AND (".implode(' OR ', $where).")";
		} elseif($_SESSION['group'] == 'prof'){
			$acess_sql .= " AND files_librarys_shares.con='prof'";
		}  else {
			$acess_sql .= " AND files_librarys_shares.con='admin'";
		}
		$accsess = do_query_array($acess_sql, $this->database);
		if(count($accsess) > 0){
			return true;
			$ac = $accsess[0];
			if($ac->write == 1){
				$editable = true;
			} else {
				$editable = false;
			}
		} else {
			$editable = false;
		}
	}
	
	static function libForm($lib_id=false){
		global $lang;
		if($lib_id != false){		
			$seek_lib = true;
			$library = new sharedLibrary($lib_id);
			$read_arr = array();
			$reads = do_query_array("SELECT con, con_id FROM files_librarys_shares WHERE lib_id=$lib_id AND `read`= 1", LMS_Database);
			foreach($reads as $read){
				$read_arr[] = $read->con.($read->con_id	!= '0' ? '-'.$read->con_id: '');
			}
	
			$write_arr = array();
			$writes = do_query_array("SELECT con, con_id FROM files_librarys_shares WHERE lib_id=$lib_id AND `write`= 1", LMS_Database);
			foreach($writes as $write){
				$write_arr[] = $write->con.($write->con_id!= '0' ? '-'.$write->con_id: '');
			}
		} else {
			$seek_lib = false;	
		}
		
		$permission_thead = write_html('thead', '',  
			write_html( 'tr', '',
				write_html('th', '', '&nbsp;').
				write_html('th', 'width="60"', $lang['read']).
				write_html('th', 'width="60"', $lang['write'])
			)
		);	

		$levels = Levels::getList();
		$levels_html ='<fieldset>'.
			write_html('legend', '', $lang['levels'].' '.
				write_html('a', 'onclick="toogleNext(this)" class="hoverable ui-state-default ui-corner-all rev_float" style="display:block; width:16px; height:16px"',
					write_html('span', 'class="ui-icon ui-icon-carat-1-s"', '')
				)
			).
			'<table class="result hidden">'.$permission_thead.'</tbody>';
				foreach ($levels as $level){
					$levels_html .= write_html( 'tr', '',
						write_html('td', '', $level->getName()).
						write_html('td', 'width="200"', '<input type="checkbox" name="read[]" value="level-'.$level->id.'" '.($seek_lib && in_array('level-'.$level->id , $read_arr )? 'checked="checked"': '').' />').
						write_html('td', 'width="196"', '<input type="checkbox" name="write[]" value="level-'.$level->id.'"  '.($seek_lib && in_array('level-'.$level->id , $write_arr )? 'checked="checked"': '').'/>')
					);
				}
			$levels_html .='</tbody></table></fieldset>';
	
		$classes = Classes::getList();
		$classes_html ='<fieldset>'.
			write_html('legend', '', $lang['classes'].' '.
				write_html('a', 'onclick="toogleNext(this)" class="hoverable ui-state-default ui-corner-all rev_float" style="display:block; width:16px; height:16px"',
					write_html('span', 'class="ui-icon ui-icon-carat-1-s"', '')
				)
			).
			'<table class="result hidden">'.$permission_thead.'</tbody>';
				foreach ($classes as $class){
					$classes_html .= write_html( 'tr', '',
						write_html('td', '', $class->getName()).
						write_html('td', 'width="200"', '<input type="checkbox" name="read[]" value="class-'.$class->id.'" '.($seek_lib && in_array('class-'.$class->id , $read_arr )? 'checked="checked"': '').' />').
						write_html('td', 'width="196"', '<input type="checkbox" name="write[]" value="class-'.$class->id.'" '.($seek_lib && in_array('class-'.$class->id , $write_arr )? 'checked="checked"': '').' />')
					);
				}
			$classes_html .='</tbody></table></fieldset>';
		
		$groups = Groups::getList();
		$groups_html ='<fieldset>'.
			write_html('legend', '', $lang['groups'].' '.
				write_html('a', 'onclick="toogleNext(this)" class="hoverable ui-state-default ui-corner-all rev_float" style="display:block; width:16px; height:16px"',
					write_html('span', 'class="ui-icon ui-icon-carat-1-s"', '')
				)
			).
			'<table class="result hidden">'.$permission_thead.'</tbody>';
				foreach ($groups as $group){
					$groups_html .= write_html( 'tr', '',
						write_html('td', '', $group->getName()).
						write_html('td', 'width="200"', '<input type="checkbox" name="read[]" value="group-'.$class->id.'" '.($seek_lib && in_array('group-'.$class->id , $read_arr )? 'checked="checked"': '').' />').
						write_html('td', 'width="196"', '<input type="checkbox" name="write[]" value="group-'.$class->id.'" '.($seek_lib && in_array('group-'.$class->id , $write_arr )? 'checked="checked"': '').' />')
					);
				}
			$groups_html .='</tbody></table></fieldset>';
	
		$out = write_html('form', 'id="lib_form"',
			'<input type="hidden" name="lib_id" id="lib_id" value="'.($seek_lib ? $lib_id : '').'" />'.
			write_html('div', 'class="ui-corner-all ui-state-highlight" style="padding:5px"',
				write_html('table',  'border="0" cellspacing="0"',
					write_html('tr', '',
						write_html('td', 'width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['name'])
						).
						write_html('td', 'id="book_id_td"',
							'<input type="text" name="new_lib_name" id="new_lib_name" class="required" value="'.($seek_lib ? $library->title : '').'" />'
						)
					).
					write_html('tr', '',
						write_html('td', 'width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['docs_root'])
						).
						write_html('td', 'id="book_id_td"',
							($seek_lib ?  $library->path :'<input type="text" name="new_lib_path" id="new_lib_path" class="required" style="width:300px" />')
						)
					).
					write_html('tr', '',
						write_html('td', 'width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['size'])
						).
						write_html('td', 'id="book_id_td"',
							'<input type="text" name="size" id="new_lib_size" style="width:50px"  value="'.($seek_lib ?  $library->size : '').'"/> Mb'
						)
					)
				)
			).
			write_html('fieldset', '',
				write_html('legend', '', $lang['permissions']).
				write_html('table', 'class="result"',
					$permission_thead.
					write_html('tbody','',
						write_html( 'tr', '',
							write_html('td', '', $lang['admin']).
							write_html('td', '', '<input type="checkbox" name="read[]" value="admin"  '.($seek_lib && in_array('admin' , $read_arr )? 'checked="checked"': '').'/>').
							write_html('td', '', '<input type="checkbox" name="write[]" value="admin"  '.($seek_lib && in_array('admin' , $write_arr )? 'checked="checked"': '').'/>')
						).
						write_html( 'tr', '',
							write_html('td', '', $lang['prof']).
							write_html('td', '', '<input type="checkbox" name="read[]" value="prof"  '.($seek_lib && in_array('prof' , $read_arr )? 'checked="checked"': '').'/>').
							write_html('td', '', '<input type="checkbox" name="write[]" value="prof"  '.($seek_lib && in_array('prof' , $write_arr )? 'checked="checked"': '').'/>')
						)
					)
				).
				$levels_html.
				$classes_html.
				$groups_html
			)
		);
		return $out;
	}
	
	static function _save($post){
		global $lang;
		$error = false;
		if(isset($post['lib_id']) && $post['lib_id'] != ''){
			$seek_lib = true;
			$lib_id = $post['lib_id'];
			$lib = do_query("SELECT path FROM files_librarys WHERE id=$lib_id", LMS_Database);
			$path = $lib['path'];
		} else {
			$seek_lib = false;	
			if(!isset($_POST['new_lib_path']) || $_POST['new_lib_path'] == ''){
				$error = 'Error: root cannot be empty';
			} else {
				$path = $_POST['new_lib_path'];
			}
		}
		// name
		if(!isset($_POST['new_lib_name']) || $_POST['new_lib_name'] == ''){
			$error = 'Error: Title cannot be emplty';
		} else {
			$title = $_POST['new_lib_name'];
		}
		// size
		$size = $_POST['size'];
		
		if(!$error){
			//dir creation
			$sys_path = 'attachs/'.sqlToSystemPath($path);
			if(!file_exists($sys_path)){
				$chk_mkdir = mkdir($sys_path, 0777, true);
				if(!$chk_mkdir){
					$error = 'Error : Cant create path:'.$sys_path;
				}
			}
		
			// insert to SQL
			if(!$error){
				$sql = $seek_lib ? 
					"UPDATE  files_librarys SET title='$title', size='$size' WHERE id=$lib_id"  :
					"INSERT INTO files_librarys (title, path, size) VALUES ('$title', '$path', $size)";
				if(!do_query_edit($sql , LMS_Database)){
					$error = $lang['error_updating'];
				}
			}
		}
		if(!$error){
			$lib_id = $seek_lib ? $lib_id : mysql_insert_id();
			$answer['id'] = $lib_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $error;
		}
		
		// library permission
		if(isset($_POST['read'])){
			$reads = $_POST['read'];
			foreach($reads as $c){
				if(strpos($c , '-') !== false){
					$x = explode('-', $c);
					$con = $x[0];
					$con_id  = $x[1];
				} else {
					$con = $c;
					$con_id  = '';
				}
				if(count(do_query_array("SELECT * FROM files_librarys_shares WHERE lib_id=$lib_id AND con='$con' AND con_id='$con_id'", LMS_Database))){
					do_query_edit("UPDATE files_librarys_shares SET `read`=1 WHERE lib_id=$lib_id AND con='$con' AND con_id='$con_id'", LMS_Database);
				} else {
					do_query_edit("INSERT INTO files_librarys_shares (lib_id, con, con_id, `read`) VALUES ($lib_id, '$con', '$con_id', 1)", LMS_Database);
				}
			}
		}
	
		if(isset($_POST['write'])){
			$writes = $_POST['write'];
			foreach($writes as $c){
				if(strpos($c , '-') !== false){
					$x = explode('-', $c);
					$con = $x[0];
					$con_id  = $x[1];
				} else {
					$con = $c;
					$con_id  = '';
				}
				if(count(do_query_array("SELECT * FROM files_librarys_shares WHERE lib_id=$lib_id AND con='$con' AND con_id='$con_id'", LMS_Database))){
					do_query_edit("UPDATE files_librarys_shares SET `write`=1 WHERE lib_id=$lib_id AND con='$con' AND con_id='$con_id'", LMS_Database);
				} else {
					do_query_edit("INSERT INTO files_librarys_shares (lib_id, con, con_id, `write`) VALUES ($lib_id, '$con', '$con_id', 1)", LMS_Database);
				}
			}
		}
		//echo $lib_id;
		return json_encode($answer);
	}
	
	public function _delete(){
		global $lang;
		$answer = array();
		$path = $this->folder->_delete();
		if(do_query_edit("DELETE FROM files_librarys WHERE id=$this->id", $this->database)){
			do_query_edit("DELETE FROM files_librarys_shares WHERE lib_id=$this->id", $this->database);
			do_query_edit("DELETE FROM files WHERE path LIKE '$this->path%'", $this->database);
			$answer['path'] = $path;
			$answer['error'] ='';
		} else {
			$answer['id'] = "";
			$answer['error'] ='Error while deleting';
		}
		return json_encode($answer);

	}
	
}