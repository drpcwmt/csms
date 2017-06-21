<?php
/** Login
*
*
*/

class Users{
	private $thisTemplatePath = 'modules/settings/templates';
	
	public function __construct($group='', $user_id='', $system=''){
		global $this_system, $lang;
		$this->sys = $system!='' ? $system : $this_system;
			// System
		if($group == '' && $user_id == '0'){
			$this->reel_name = $lang['system'];
			$this->group = $lang['system'];
			$user_id = 0;
			$group = $lang['system'];
			// Group
		} elseif($group != '' && $user_id == ''){
			$this->group = $group;
			$this->reel_name = $lang[$group];
		} else {		
				// current user
			if($group == '' && $user_id == ''){
				$user_id = $_SESSION['user_id'];
				$group = $_SESSION['group'];
				$user =do_query_obj("SELECT * FROM users WHERE `group`='$group' AND user_id=$user_id", $this->sys->database, $this->sys->ip);
				//  user without group dont work on SMS
			} elseif($group == '' && $user_id != ''){
				$user =do_query_obj("SELECT * FROM users WHERE  user_id=$user_id", $this->sys->database, $this->sys->ip);
				// specifique users
			} elseif($group != '' && $user_id != ''){
				$user =do_query_obj("SELECT * FROM users WHERE `group`='$group' AND user_id=$user_id", $this->sys->database, $this->sys->ip);
			}
			if(isset($user->user_id)){ 
				foreach($user as $key =>$value){
					$this->$key = $value;
				}
			//	echo $user->name;
			} else {
				$this->group = $group;
				$this->user_id = $user_id;
			}
		}
	}
	
	public function getRealName(){
		if(!isset($this->reel_name)){
			if(MS_codeName!= 'hrms' && $this->sys->getSettings('hrms_server') == '1'){
				$this->reel_name = $this->sys->getAnyNameById($this->group, $this->user_id);
			} else {
				global $this_system;
				$this->reel_name = $this_system->getAnyNameById($this->group, $this->user_id);
			}
		}
		return  $this->reel_name;
	}
	
	public function loadLayout(){
		global $lang, $this_system;
		$prvlg = new Privileges($this->group, $this->user_id);
		if(!in_array($this->group, array('student', 'parent'))){
			$out = write_html('div', 'class="tabs"',
				write_html('ul', '',
					write_html('li', '', write_html('a', 'href="#user_infos"', $lang['user_infos'])).
					write_html('li', '', write_html('a', 'href="#user_privileges"', $lang['privileges'])).
					($this_system->type == 'hrms' ?
						write_html('li', '', write_html('a', 'href="#job_privileges"', $lang['jobs']))
					: '')
				).
				write_html('div', 'id="user_infos"', $this->loadUserData() ).
				write_html('div', 'id="user_privileges"', $prvlg->loadLayout() ).
				($this_system->type == 'hrms' ?
					write_html('div', 'id="job_privileges"', $this->loadJobPrvlg() )
				: '')
			);
		} else {
			$out = write_html('div', 'id="user_infos"', $this->loadUserData() );
		}
		return $out;
	}

	public function loadUserData(){
		global  $lang;
		$layout = new Layout($this);
		$layout->template = 'modules/settings/templates/users.tpl';
		// Reel name
		$layout->user_reel_name = $this->getRealName();

		// Groups
		$groups = do_query_array("SELECT * FROM groups");
		foreach($groups as $group){
			$groups_arr[$group->name] = $lang[$group->name];
		}
		$layout->group_select_val = write_select_options( $groups_arr, $this->group, false);
		// languages
		$layout->ui_lang_select = write_select_options( getLangArray() , $this->def_lang != '' ? $this->def_lang :$this->sys->getSettings('default_lang'));
		// hide unused
		
		$layout->disable_for_update = 'disabled="disabled"';
		$layout->hide_for_update = 'hidden';
		$layout->password_type = 'text';
		
		
		$layout->last_login = write_html('fieldset', 'class="ui-state-highlight unprintable"',
			write_html('legend', '', $lang['last_login']).
			write_html('h4', '',
				($this->last_login > 0 ? date('d/m/Y h:m a', $this->last_login) : $lang['never'])
			).
			write_html('h4', '', $lang['visites'].': '.$this->counter)
		);
		return $layout->_print();
	}
	
	public function _delete($docs=true){
		return do_delete_obj("`group`='$this->group' AND user_id=$this->user_id", 'users', $this->sys->database, $this->sys->ip);			
	}
	
	public function getPrvlg($prv){
		if(!isset($this->privileges)){
			$ps =false;
			if(isset($this->user_id)){
				$ps = do_query_array("SELECT name FROM privileges WHERE value=1 AND `group`='$this->group' AND user_id=".$this->user_id, $this->sys->database, $this->sys->ip);
			} 
			if(!$ps){
				$ps = do_query_array("SELECT name FROM privileges WHERE value=1 AND `group`='$this->group' AND user_id IS NULL", $this->sys->database, $this->sys->ip);
			}
			$this->privileges = array();
			foreach($ps as $p){
				$this->privileges[] = $p->name;
			}
		}
		//print_r($this->privileges);
		return in_array($prv, $this->privileges);
	
	}
	
	public function getPrvlgTable(){
		global $lang;
		$privileges = new Privileges($this->group, $this->user_id);
		return $privileges->loadLayout();
	}
	
	public function loadJobPrvlg(){
		$jobs = Jobs::getList(false);
		$cur_jobs = do_query_array("SELECT * FROM privilege_cc WHERE user_id=$this->user_id");
		$cur_jobs_array = array();
		foreach($cur_jobs as $job){
			$cur_jobs_array[] = $job->job;
		}
		$tr = array();
		foreach($jobs as $job){
			$tr[] = write_html('tr', '',
				write_html('td', 'width="20"', 
					'<input type="checkbox" update="assignJobPrvlg" job_id="'.$job->id.'" '.(in_array($job->id, $cur_jobs_array) ? 'checked="checked"' : '').' />'
				).
				write_html('td', '', $job->getName())
			);
		}
		return write_html('form', '',
			'<input type="hidden" name="user_id" value="'.$this->user_id.'" /> '.
			write_html('table', 'class="result"',
				implode('', $tr)
			)
		);
	}
	
		
	static function newUser(){
		global $lang, $this_system;
		$layout = new Layout();
		$layout->template = 'modules/settings/templates/users.tpl';
		$layout->password_type = 'password';
		$layout->disable_for_update = '';
		$layout->hide_for_update = '';
		// Groups
		$groups = do_query_array("SELECT * FROM groups");
		foreach($groups as $group){
			$groups_arr[$group->name] = $lang[$group->name];
		}
		$layout->group_select_val = write_select_options( $groups_arr, '', false);
		$layout->ui_lang_select = write_select_options( getLangArray() , $this_system->getSettings('default_lang'), false);
		return $layout->_print();
	}
	
	static function saveUser($post){
		global $this_system;
		$user_id = $post['user_id'];
		$group = $post['group'];
		$chk = do_query_obj("SELECT name FROM users WHERE user_id=".$user_id." AND `group`='$group'", $this_system->database, $this_system->ip);
		if(isset($chk->name)){
			if(UpdateRowInTable("users", $post, "user_id=$user_id AND `group`='$group'") != false){
				$answer['id'] = $user_id;
				$answer['error'] = '' ;
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		} else { // new Student
			if(insertToTable("users", $post) != false){
				$answer['id'] =  $user_id;
				$answer['error'] = '';
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		}		
		return json_encode($answer);
	}
	
	static function listGroup($group){
		global $lang;
		$users = do_query_array("SELECT user_id FROM users WHERE `group`='$group' AND user_id IS NOT NULL");
		$users_trs = '';
		foreach($users as $u){
			$user = new Users($group, $u->user_id);
			$users_trs .= write_html('tr', 'id="tr_'.$user->user_id.'"', 
				write_html('td', '', 
					write_html('button', 'type="button" action="deleteUser" group="'.$user->group.'" userid="'.$user->user_id.'" class="hoverable ui-state-default circle_button" title="'.$lang['delete'].'"', write_icon('close'))
				).
				write_html('td', '', 
					write_html('button', 'type="button" action="openPrvlg" group="'.$user->group.'-'.$user->user_id.'" class="hoverable ui-state-default circle_button" title="'.$lang['privileges'].'"', write_icon('key'))
				).
				write_html('td', '', 
					write_html('button', 'type="button" action="openUser" group="'.$user->group.'" userid="'.$user->user_id.'" class="hoverable ui-state-default circle_button" title="'.$lang['reset_password'].'"', write_icon('refresh'))
				).
				write_html('td', '', $user->user_id).
				write_html('td', '', $user->getRealName()).
				write_html('td', '', ($user->last_login > 0 ? date('d/m/Y h:m a', $user->last_login) :'-'))
			);
		}
		
		
		return write_html('table', 'class="tablesorter"',
			write_html('thead', '', 
				write_html('tr', '', 
					write_html('th', 'width="22"', '&nbsp;').
					write_html('th', 'width="22"', '&nbsp;').
					write_html('th', 'width="22"', '&nbsp;').
					write_html('th', 'width="60"', $lang['id']).
					write_html('th', '', $lang['name']).
					write_html('th', 'width="180"', $lang['last_login'])
				)
			).
			write_html('tbody', 'id="users_tbody"', $users_trs)
		);
	}
	
	static function getTotalUsersInGroup($group_id){
		return count(do_query_array("SELECT users.user_id FROM users, groups WHERE users.user_id!=0 AND users.group=groups.name AND groups.id=$group_id"));
	}

	static function loadMainLayout(){
		global $lang, $this_system, $prvlg;
			// Groups
		$groups_q = do_query_array("SELECT * FROM groups");
		foreach($groups_q as $gr){
			$groups[$gr->id] = $gr->name;
		}

		// Default body
		$group_tbody= '';
		foreach($groups as $group_id => $group_name){
			$gr_total = Users::getTotalUsersInGroup($group_id);
			$group_tbody .= write_html('tr', 'id="groupId_tr_'.$group_id.'"', 
				write_html('td', '', 
					write_html('button', 'type="button"  class="hoverable ui-state-default circle_button" action="listGroup" group="'.$group_name.'"  title="'.$lang['open'].'"', write_icon('newwin'))
				).
				write_html('td', '', 
					write_html('button', 'type="button"  class="hoverable ui-state-default circle_button" action="openPrvlg" group="'.$group_name.'" title="'.$lang['privilegs'].'"', write_icon('key'))
				).
				($this_system->type == 'sms'  && $prvlg->_chk('add_user') ?
					write_html('td', '', 
						(!in_array($group_name, array('superadmin', 'admin')) ?
							write_html('button', 'type="button"  class="hoverable ui-state-default circle_button" title="'.$lang['user_generator'].'" action="regenerateUsers" group="'.$group_name.'"', write_icon('gear'))
						:'')
					)
				: '').
				write_html('td', '',  $lang[$group_name]).
				write_html('td', '', $gr_total.' '. $lang['users'] )
			);
		}
		
		return ($prvlg->_chk('add_user') ?
				write_html('div', 'class="toolbox"',
					write_html('a', 'onclick="addUser()"', 
						write_icon('document').
						$lang['new_user']
					)
				)
			: '').
			'<table class="result">
			<thead>
				<th width="20">&nbsp;</th>
				<th width="20">&nbsp;</th>'.
				($this_system->type == 'sms'  && $prvlg->_chk('add_user')?
					'<th width="20">&nbsp;</th>'
				:'').
				'<th width="200">'.$lang['name'].'</th>
				<th>'.$lang['users'].'</th>
			</thead>
			<tbody>'.$group_tbody.'</tbody>
		</table>';
	}
	
}
