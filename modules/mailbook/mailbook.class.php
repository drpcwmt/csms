<?php
/** MailBook
*
*/
class MailBook{

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
		$rows = do_query_array("SELECT * FROM mailbook WHERE con='$this->con' AND con_id=$this->con_id", $this->sys->database, $this->sys->ip);
		$rows = sortArrayOfObjects($rows, $this->sys->getItemOrder('mailbook-'.$this->con.'-'.$this->con_id), 'id');

		$lis = array();
		if($rows == false || count($rows)> 0){
			foreach($rows as $row){
				$lis[] = write_html('li','itemid="'.$row->id.'" class="hoverable ui-state-default ui-corner-all"',
					write_html('b', 'style="float:none"', $row->mail).
					($this->editable ? 
						write_html('a', 'class="rev_float ui-state-default hoverable mini_circle_button unprintable" module="mailbook" action="deleteMailBook" rel="'.$row->id.'" sys_id="'.$this->sys->id.'" ', write_icon('trash'))
					: '')
				);
			}
		} 
		return write_html('ul', 'class="list_menu listMenuUl sortable" rel="mailbook-'.$this->con.'-'.$this->con_id.'" style="margin:0"', implode('', $lis));
	}
	
	static function _new($con, $con_id, $sys){
		$layout = new Layout();
		$layout->con=$con;
		$layout->con_id=$con_id;
		$layout->template = 'modules/mailbook/templates/new.tpl';
		return $layout->_print();
	}
	
	static function _save($post, $sys){
		if($id = do_insert_obj($post, 'mailbook', $sys->database, $sys->ip) ){
			return array( "error"=>'', 'id'=>$id);
		} else {
			return false;
		}
	}
	
	static function _delete($id, $sys){
		if(do_delete_obj("id=$id", 'mailbook', $sys->database, $sys->ip)!=false ){
			return true;
		} else {
			return false;
		}
	}
		
	
}
?>
