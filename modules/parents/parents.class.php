<?php
/** Parents
*
* status:
* 0 = leaved
* 1 = maried
* 2 = divored
* 3 = father deceased
* 4 = mother deceased
* 5 = both deceased
*/

class Parents{
	private $thisTemplatePath = 'modules/parents/templates';
	
	public function __construct($id, $sms=''){
		if($sms == ''){
			global $sms;
		}
		$sql = "SELECT *  FROM parents WHERE id=$id";
		$parent = do_query_obj($sql, SMS_Database, $sms->ip);
		if(isset($parent->id)){ 
			$this->sms =$sms;
			foreach($parent as $key =>$value){
				$this->$key = $value;
			}
		} else {
		//	throw new Exception('id not found');;
		}
	}
	
	public function getName($type='mother', $other_lang = false){
		if($type == 'father'){
			$field = 'father_name';
		} else {
			$field = 'mother_name';
		}
		if($other_lang == false){
			return $_SESSION['lang'] == 'ar' ? $this->{$field.'_ar'} : $this->$field ;
		} else {
			return $_SESSION['lang'] == 'ar' ? $this->$field : $this->{$field.'_ar'} ;
		}
	}

	
	public function getTel($parent='mother', $all=false){
		$rows = do_query_array("SELECT * FROM phonebook WHERE con='$parent' AND con_id=$this->id", $this->sms->database, $this->sms->ip);
		$rows = sortArrayOfObjects($rows, $this->sms->getItemOrder('phonebook-$parent-'.$this->id), 'id');
		if( $all==false ){
			$first = reset($rows);
			return isset($first->tel) ? $first->tel : '';
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $r){
					$out[] = $r->tel;
				} 
			}
			return $out;
		}
	}

	public function getMail($parent='mother', $all=false){
		$rows = do_query_array("SELECT * FROM mailbook WHERE con='$parent' AND con_id=$this->id", $this->sms->database, $this->sms->ip);
		$rows = sortArrayOfObjects($rows, $this->sms->getItemOrder('mailbook-$parent-'.$this->id), 'id');
		if( $all==false ){
			$first = reset($rows);
			return isset($first->mail) ? $first->mail : '';
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $r){
					$out[] = $r->mail;
				} 
			}
			return $out;
		}
	}
	
	public function getAddress($parent='mother', $lang='', $all=false){
		$rows = do_query_array("SELECT * FROM addressbook WHERE con='$parent' AND con_id=$this->id", $this->sms->database, $this->sms->ip);
		$rows = sortArrayOfObjects($rows, $this->sms->getItemOrder('addressbook-$parent-'.$this->id), 'id');
		if($lang == ''){ $lang = $_SESSION['lang'];}
		if( $all==false ){
			$first = reset($rows);
			if(isset($first->address_ar)){
				if($lang == 'ar'){
					return $first->address_ar.' - '. 
						$first->region_ar.' '. 
						$first->city_ar.' '.
						$first->country_ar.'<br/>'.
						$first->landmark.' '.
						$first->zip;
				} else {
					return $first->address.' - '. 
						$first->region.' '. 
						$first->city.' '.
						$first->country;
				}
			} else {
				return '';
			}
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $r){
					if($lang == 'ar'){
						$out[] = $row->address_ar.' - '. 
							$r->region_ar.' '. 
							$r->city_ar.' '.
							$r->country_ar.'<br/>'.
							$r->landmark.' '.
							$r->zip;
					} else {
						$out[] = $r->address.' - '. 
							$r->region.' '. 
							$r->city.' '.
							$r->country;
					}
				} 
			}
			return $out;
		}
	}
	
	public function getChildrens(){
		if(!isset($this->childrens)){
			$out = array();
			$childrens = do_query_array("SELECT id FROM student_data WHERE parent_id=$this->id", SMS_Database, $this->sms->ip);
			foreach($childrens as $child){
				$out[] = new Students($child->id);
			}
			$this->childrens = $out;
		}
		return $this->childrens;
	}

	
	public function loadMainLayout(){
		global $MS_settings, $lang;

		$parents_toolbox = array();
		if(getPrvlg('edit_parent')){ 
			$parents_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="resetParentForm"',
				"text"=> $lang['reset'],
				"icon"=> "trash"
			);
		}
		$parents_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="print_pre" rel=".parents_data-'.$this->id.'"',
			"text"=> $lang['print'],
			"icon"=> "print"
		);

		$out = write_html('div', 'class="tabs" parentid="'.$this->id.'"',
			write_html('ul', '',
				write_html('li', '', write_html('a', 'href="#parent-data"', $lang['personel_infos'])).
				write_html('li', '', write_html('a', 'href="index.php?module=parents&sons&'.(isset($_GET['std_id']) ? 'std_id='.safeGet($_GET['std_id']) : 'id='.$this->id).'&dialog"', (isset($_GET['std_id'])) ? $lang['brothers'] : $lang['sons']))
			).
			write_html('div', 'id="parent-data" class="parents_data-'.$this->id.'"',
				createToolbox($parents_toolbox). 
				$this->loadDataLayout()
			)
		);
		return $out;
	}
	
	public function loadDataLayout(){
		global $lang, $prvlg;
		$layout = new Layout($this);
		$layout->template = "$this->thisTemplatePath/parents_data.tpl";
		$layout->count_sons = count($this->getChildrens());
		
		$layout->sms_id = $this->sms->id;
		// Religion 
		$layout->father_religion_1_checked = $this->father_religion == 1 ? 'checked="checked"' : '';
		$layout->father_religion_2_checked = $this->father_religion == 2 ? 'checked="checked"' : '';
		$layout->mother_religion_1_checked = $this->mother_religion == 1 ? 'checked="checked"' : '';
		$layout->mother_religion_2_checked = $this->mother_religion == 2 ? 'checked="checked"' : '';
		
		// Employers
		$layout->father_emp_check = $this->father_emp == 1 ? 'checked="checked"' : '';
		$layout->mother_emp_check = $this->mother_emp == 1 ? 'checked="checked"' : '';

		// Resp
		$layout->father_resp_check = $this->father_resp == 1 ? 'checked="checked"' : '';
		$layout->mother_resp_check = $this->mother_resp == 1 ? 'checked="checked"' : '';
		
		// Phonebook
		$fatherphonebook = new PhoneBook('father', $this->id, $this->sms);
		$layout->father_phone_book = $fatherphonebook->getList();
		$motherphonebook = new PhoneBook('mother', $this->id, $this->sms);
		$layout->mother_phone_book = $motherphonebook->getList();
		// Addressbook
		$fatheraddressbook = new AddressBook('father', $this->id, $this->sms);
		$layout->father_address_div = $fatheraddressbook->getList();
		$motheraddressbook = new AddressBook('mother', $this->id, $this->sms);
		$layout->mother_address_div = $motheraddressbook->getList();
		// mailbook
		$fathermailbook = new MailBook('father', $this->id, $this->sms);
		$layout->father_mail_book = $fathermailbook->getList();
		$mothermailbook = new MailBook('mother', $this->id, $this->sms);
		$layout->mother_mail_book = $mothermailbook->getList();
		
		// Alert comments
		$notebook = new NoteBook('parent', $this->id, $this->sms);
		$layout->notes_div = $notebook->getList();
		// status
		$layout->{"status-".$this->status."-selected"} = 'checked="checked"';
		// Editable
		$layout->editable_btn = $prvlg->_chk('parents_edit') ? 'inline-block' : 'none';
		// Other schools
		$ccs = CostCenters::getList();
		// Editable
		$layout->editable = $prvlg->_chk('parents_edit') ? 1 : 0;
		return $layout->_print();
		
	}

	static function newParents(){
		global $sms, $lang;
		$layout = new Layout();
		$layout->sms_id = $sms->id;
		$layout->template = "modules/parents/templates/parents_data.tpl";
		// Phonebook
		$layout->father_phone_book = '<ul></ul>';
		$layout->mother_phone_book = '<ul></ul>';
		// Addressbook
		$layout->father_address_div = '<ul></ul>';
		$layout->mother_address_div ='<ul></ul>';
		// mailbook
		$layout->father_mail_book = '<ul></ul>';
		$layout->mother_mail_book = '<ul></ul>';
		$layout->notes_div = '<ul></ul>';
		// Editable
		$layout->editable = 1;
		$layout->editable_btn = 'inline-block';
		return $layout->_print();
		
	}
	
	static function getAutocompleteParent( $value){
		global $sms;
		$table = 'parents';
		$value = trim($value);
		$num =0;
		$arr = array();
		$Arabic = new I18N_Arabic('KeySwap');
		$sql = "SELECT * FROM parents WHERE 
		(
			father_name_ar LIKE '$value%'
			OR father_name_ar LIKE '".addslashes($Arabic->swapEa($value))."%'
			OR LOWER(father_name) LIKE LOWER('$value')
			OR LOWER(father_name) LIKE LOWER('$value%')
			OR LOWER(father_name) LIKE LOWER('".addslashes($Arabic->swapAe($value))."%')
			OR mother_name_ar LIKE '$value%'
			OR mother_name_ar LIKE '".addslashes($Arabic->swapEa($value))."%'
			OR LOWER(mother_name) LIKE LOWER('$value')
			OR LOWER(mother_name) LIKE LOWER('$value%')
			OR LOWER(mother_name) LIKE LOWER('".addslashes($Arabic->swapAe($value))."%')
		)";
		
		
		$parents = do_query_array( $sql, $sms->database, $sms->ip);
		foreach($parents as $parent){
			// Phonebook
			$fatherphonebook = new PhoneBook('father', $parent->id, $sms);
			$parent->father_phone_book = $fatherphonebook->getList();
			$motherphonebook = new PhoneBook('mother', $parent->id, $sms);
			$parent->mother_phone_book = $motherphonebook->getList();
			// Addressbook
			$fatheraddressbook = new AddressBook('father', $parent->id, $sms);
			$parent->father_address_div = $fatheraddressbook->getList();
			$motheraddressbook = new AddressBook('mother', $parent->id, $sms);
			$parent->mother_address_div = $motheraddressbook->getList();
			// mailbook
			$fathermailbook = new MailBook('father', $parent->id, $sms);
			$parent->father_mail_book = $fathermailbook->getList();
			$mothermailbook = new MailBook('mother', $parent->id, $sms);
			$parent->mother_mail_book = $mothermailbook->getList();
			// Notebook
			$notebook = new NoteBook('parents', $parent->id, $sms);
			$parent->notebook = $notebook->getList();
			
			$out[] = $parent;
		}
		
		return json_encode($out);
	}

}
