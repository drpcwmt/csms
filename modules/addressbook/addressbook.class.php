<?php
/** AddressBook
*
*/
class AddressBook{

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
		global $lang;
		$rows = do_query_array("SELECT * FROM addressbook WHERE con='$this->con' AND con_id=$this->con_id", $this->sys->database, $this->sys->ip);
		$rows = sortArrayOfObjects($rows, $this->sys->getItemOrder('addressbook-'.$this->con.'-'.$this->con_id), 'id');
		$lis = array();
		if($rows == false || count($rows)> 0){
			foreach($rows as $row){
				$lis[] = write_html('li','itemid="'.$row->id.'" class="hoverable '.($this->editable ? 'ui-state-default' :'').'" style="border: 1px solid #CCC"',
					($this->editable ? 
						write_html('a', 'class="rev_float ui-state-default hoverable mini_circle_button unprintable" module="addressbook" action="deleteAddressBook" rel="'.$row->id.'" sys_id="'.$this->sys->id.'" ', write_icon('trash')).
						write_html('a', 'class="rev_float ui-state-default hoverable mini_circle_button unprintable" module="addressbook" action="editAddressBook" rel="'.$row->id.'" sys_id="'.$this->sys->id.'" ', write_icon('pencil'))
					: '').
					write_html('div', 'align="right"',
						$row->address_ar.' - '. 
						$row->region_ar.' '. 
						$row->city_ar.' '.
						$row->country_ar.'<br/>'.
						$row->landmark.' '.
						$row->zip
					).
					write_html('div', '',
						$row->address.' - '. 
						$row->region.' '. 
						$row->city.' '.
						$row->country
					)
				);
			}
		} 
		return write_html('ul', 'class="list_menu listMenuUl '.($this->editable ? 'sortable' :'').'" rel="addressbook-'.$this->con.'-'.$this->con_id.'" style="margin:0"', implode('', $lis));
	}
	
	static function _new($con, $con_id, $sys){
		$layout = new Layout();
		$layout->con=$con;
		$layout->con_id=$con_id;
		$layout->sys_id = $sys->id;
		$layout->user_id=$_SESSION['user_id'];
		$layout->template = 'modules/addressbook/templates/new.tpl';
		return $layout->_print();
	}
	
	static function _save($post, $sys){
		if($post['id'] != ''){
			if(do_update_obj($post,  "id=".$post['id'], 'addressbook', $sys->database, $sys->ip) ){
				return array( "error"=>'', 'id'=>$post['id']);
			} else {
				return false;
			}
		} else {
			if($id = do_insert_obj($post, 'addressbook', $sys->database, $sys->ip) ){
				return array( "error"=>'', 'id'=>$id);
			} else {
				return false;
			}
		}
	}
	
	static function _delete($id, $sys){
		if(do_delete_obj("id=$id", 'addressbook', $sys->database, $sys->ip)!=false ){
			return true;
		} else {
			return false;
		}
	}
	
	static function _edit($address_id, $sys){
		$address = do_query_obj("SELECT * FROM addressbook WHERE id=$address_id", $sys->database, $sys->ip);
		$layout = new Layout($address);
		$layout->template = 'modules/addressbook/templates/new.tpl';
		$layout->sys_id = $sys->id;
		return $layout->_print();
	}
		
	static function _copy($con, $con_id, $from, $sys){
		if($con == 'student'){
			$student = new Students($con_id, $sys);
			$parent_id = $student->getParent()->id;
		} else {
			$parent_id = $con_id;
		}
		$rows = do_query_array("SELECT * FROM addressbook WHERE con='$from' AND con_id=$parent_id", $sys->database, $sys->ip);
		if($rows != false){
			foreach($rows as $row){
				unset($row->id);
				$row->con = $con;
				$row->con_id = $con_id;
				do_insert_obj($row, 'addressbook', $sys->database, $sys->ip);
			}
		}
		$address_book = new AddressBook($con, $con_id, $sys);
		return array('error'=> '', 'html'=>$address_book->getList());
	}
	
	static function toStr($adrs, $lang=''){
		if($lang == ''){ $lang = $_SESSION['lang'];}
		if($lang == 'ar'){
			return $adrs->address_ar.' - '. 
				$adrs->region_ar.' '. 
				$adrs->city_ar.' '.
				$adrs->country_ar.'<br/>'.
				$adrs->landmark.' '.
				$adrs->zip;
		} else {
			return $adrs->address.' - '. 
				$adrs->region.' '. 
				$adrs->city.' '.
				$adrs->country;
		}
	}
}
?>
