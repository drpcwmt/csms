<?php
/** NoteBook
*
*/
class NoteBook{

	public function __construct($con, $con_id, $sys){
		global $prvlg;
		$this->con = $con;
		$this->con_id = $con_id;
		$this->sys = $sys;
		if($con == 'student' && $prvlg->_chk('std_edit')){
			$this->editable = true;
		} elseif(($con == 'father' || $con == 'mother' || $con == 'parent') && $prvlg->_chk('parents_edit')){
			$this->editable = true;
		} elseif($con == 'emp' && $prvlg->_chk('emp_edit')){
			$this->editable = true;
		} else {
			$this->editable = false;
		}
	}
	
	public function getList(){
		global $sms;
		$rows = do_query_array("SELECT * FROM notebook WHERE con='$this->con' AND con_id=$this->con_id", $this->sys->database, $this->sys->ip);
		$rows = sortArrayOfObjects($rows, $this->sys->getItemOrder('notebook-'.$this->con.'-'.$this->con_id), 'id');
		$lis = array();
		if($rows == false || count($rows)> 0){
			foreach($rows as $row){
				$lis[] = write_html('li','itemid="'.$row->id.'" class="hoverable ui-state-highlight ui-corner-all" style="border: 1px solid #CCC"',
					write_html('span', '', $row->note)." ".
					($this->editable ? 
						write_html('a', 'class="rev_float ui-state-default hoverable mini_circle_button unprintable" module="notebook" action="deleteNoteBook" rel="'.$row->id.'" sys_id="'.$this->sys->id.'" ', write_icon('trash'))
					: '')
				);
			}
		} 
		return write_html('ul', 'class="list_menu listMenuUl sortable" rel="notebook-'.$this->con.'-'.$this->con_id.'"', implode('', $lis));
	}
	
	static function _new($con, $con_id, $sys){
		$layout = new Layout();
		$layout->con=$con;
		$layout->con_id=$con_id;
		$layout->sys_id = $sys->id;
		$layout->user_id=$_SESSION['user_id'];
		$layout->template = 'modules/notebook/templates/new.tpl';
		return $layout->_print();
	}
	
	static function _save($post, $sys){
		if($id = do_insert_obj($post, 'notebook', $sys->database, $sys->ip) ){
			return array( "error"=>'', 'id'=>$id);
		} else {
			return false;
		}
	}
	
	static function _delete($id, $sys){
		if(do_delete_obj("id=$id", 'notebook', $sys->database, $sys->ip)!=false ){
			return true;
		} else {
			return false;
		}
	}
		
	
}
?>
