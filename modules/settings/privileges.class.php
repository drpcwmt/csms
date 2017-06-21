<?php
/** Privilege
*
*/

class Privileges{
	
	public function __construct($group='', $user_id=''){
		if($group ==''){
			$group = $_SESSION['group'];
			$user_id = $_SESSION['user_id'];
		}
		$this->group = $group;
		$this->user_id = $user_id;
		$this->privileges = Privileges::getprvlgArr($this->group, $this->user_id);
	}
	
	public function _chk($key){
		return in_array($key, $this->privileges);
	}
	
	public function loadLayout(){
		global $this_system, $lang, $prvlg;
		$module_lis= '';
		$module_divs= '';
		
		// Modules privilegs
		$modules = scandir('modules');
		$avoided_modules = array();
		$first = true;
		foreach($modules as $module){
			if(!in_array($module, $avoided_modules) && is_dir('modules/'.$module) && file_exists('modules/'.$module.'/privilges.php')){
				include('modules/'.$module.'/privilges.php');
				$table = '';
				if(count($privilges) > 0){
					foreach($privilges as $p){
						$table .= $this->write_privilge_tr($p);
					}
					$module_lis .= write_html('li', 'rel="'.$module.'" action="openPrvlgByKey" class="hoverable clickable ui-corner-all ui-state-default'.($first ? ' ui-state-active':'').'"', $lang[$module]);
					$module_divs .= write_html('div', 'id="prvlg_'.$module.'" class="'.($first ? '':'hidden').'"',
						write_html('table', 'width="100%" borer="0" class="tablesorter"',
							$table
						)
					);
					$first = false;
				}
			}
		}
		
		$layout = new Layout();
		$layout->modules = $module_lis;
		$layout->group_name = $lang[$this->group];
		$layout->editable = $prvlg->_chk('edit_prvlg') ? '1' : '0';
		$layout->group_id = $this->group;
		if($this->user_id!= ''){
			$user = new Users($this->group, $this->user_id);
			$layout->group_name .=  ' - '. $user->getRealName();
			$layout->group_id .= $this->user_id;
		}
		$layout->privilge_divs = $module_divs;
		$layout->template = 'modules/settings/templates/privilges.tpl';
		return $layout->_print();
	}
	
	public function write_privilge_tr($key){
		global $lang;
		$static_arr = $this->getStaticPrvlg();
		$prvlg_arr = $this->privileges;
		$out = write_html('tr', '',
			write_html('td', '', $lang[$key]).
			write_html('td', 'width="16"', 
				'<input type="checkbox" name="prvlg[]" value="'.$key.'" '.(in_array($key, $prvlg_arr) ? 'checked="checked"' : '').' '.(in_array($key, $static_arr) ? 'disabled="disabled"' : '').'/>'
			)
		); 
		return $out;
	}

	
	public function getStaticPrvlg(){
		$this->statics = array();
		$statics = do_query_array("SELECT name FROM privileges WHERE `group`='$this->group' AND static=1");
		foreach($statics as $static){
			$this->statics[] = $static->name;
		}
		return $this->statics;
	}

	static function getprvlgArr($group, $user_id=''){
		$prvlg_arr = array();
		if($user_id != ''){
			$ps = do_query_array("SELECT name FROM privileges WHERE `group`='$group' AND user_id=$user_id AND value=1");
			foreach($ps as $p){
				$prvlg_arr[] = $p->name;
			}
		}
		
		if(count($prvlg_arr) == 0){
			$ps = do_query_array("SELECT name FROM privileges WHERE `group`='$group' AND value=1");
			foreach($ps as $p){
				$prvlg_arr[] = $p->name;
			}
		}
		return $prvlg_arr;
	}
	
	static function _save($group, $user_id='', $privileges){
		if( $user_id != '' ){
			$group = $group;
			$user_id = $user_id;
			$where = " `group`='$group' AND user_id=$user_id";
		} else {
			$group = $group;	
			$where = " `group`='$group'";
			$user_id = 'NULL';
		}
		$error = false;

		$prvl = new Privileges();
		$static = $user_id=='NULL' ? array() : $prvl->getStaticPrvlg();
		if(!do_query_edit("DELETE FROM privileges WHERE $where AND `static`!=1")){
			$error = true;
		} else {
			if(count($privileges) > 0){
				foreach($privileges as $name){
					if(!in_array($name, $static)){
						$ins = array();
						$ins['name'] = $name;
						$ins['group'] =$group;
						$ins['user_id'] = $user_id;
						$ins['value'] = '1';
						$ins['static'] = '0';
						if(!do_insert_obj($ins, 'privileges')){
							$error = true;
						}
					}
				}
			}
		}
		return $error ? false : true;
	}
	
}
?>