<?php
/** Login
*
*
*/

class Login{
	private $thisTemplatePath = 'modules/login/templates';
	
	static function isLogged(){
		global $this_system;
		$interval = $this_system->getSettings('sessiontimeout')!= false ? $this_system->getSettings('sessiontimeout') : MS_timeout;
		if((isset($_SESSION["user_id"]) && $_SESSION["user_id"] != '')){  //=> is set session ...OK
			if($_SESSION['last_op'] + ( $interval *60) > time()){ //=> still under timeout ...OK
				return true;
			} else {
				//echo 'last_op ='.$_SESSION['last_op'].' &timeout='.( $this_system->getSettings('sessiontimeout') *60);
				unset($_SESSION["user_id"]);
				return false;
			}
		} else { return false;}
	}

	static function doLogin($post){
		global $MS_settings, $lang;
		if(empty($post['user'])){
			return $lang['empty_user_name'];
		} elseif(empty($post['pass'])){
			return $lang['empty_password'];
		} else {
			$user_name = GetSQLValueString($post['user'], "text");
			$password = GetSQLValueString($post['pass'], "text");
			$sql = "SELECT * FROM users WHERE name=$user_name AND password=$password";
			$user = do_query_obj($sql);
			if($user != false && $user->user_id != ''){
				// Login OK
				if(isset($post['session'])){
					$s = explode('-', $post['session']);
					$user->$s[0] = $s[1];
				}
				Login::setSession($user);
				
				// UPdate DNS
				//updateDyndns();
				
				// Update last login
				do_update_obj(array('last_login'=>time(), 'counter'=>$user->counter+1) , "user_id=".$user->user_id." AND `group`='".$user->group."'", "users");
				
				return true;
			} else {
				// Bad Login
				return $lang['error_login_1'];
			}
		}
	}
	
	static function logingPage($error=false){
		include_once(docRoot.'/ui/header.php');
		global $this_system, $MS_settings, $lang;

		$assets_files = array(
			'lang/lang.js.php',
			'assets/js/init.js',
			'assets/js/jquery.maskedinput-1.3.js',
			'assets/js/jquery.easing.1.3.js',
			'assets/js/ui.combobox.js',
			'assets/js/jquery.hoverIntent.minified.js',
			'modules/login/login.js',
		);
		// Login Form
		$loginForm = new Layout();
		$loginForm->systemName = $MS_settings['school_name'] .' ' . $this_system->getSettings('sch_code');
		$loginForm->systemVersion = MS_codeName.' '.$this_system->getSettings('sys_version');
		$loginForm->logo_path = $this_system->getLogo();
		if($error != false){
			$loginForm->error = write_html('div', ' class="ui-state-error ui-corner-all" style="padding:10px"', $error);
		}
		$loginFormHtml = fillTemplate('modules/login/templates/login_form.tpl', $loginForm);
	
		/*$browser = getBrowser();
		
		if($browser['name'] == 'Internet Explorer'){
			if($browser['version'] < 10){
				return write_page($header, 
					write_html('div', 'class="ui-state-error ui-corner-all" align="center" style="padding:50px; margin:50px;"', $lang['incompatible_browser']), 
					$assets_files
				);
			}
		}*/
				
		chdir('../../');
		return write_page($header, $loginFormHtml, $assets_files);
	
		
	}
	
	static function setSession($user){
		$_SESSION["user_id"] = $user->user_id;
		$_SESSION["user"] = $user->name;
		$_SESSION["group"] = $user->group;

		//  styles
		$_SESSION["css"] = $user->css != '' ? $user->css : $MS_settings['def_theme'];

		//languange 
		$_SESSION["lang"] = $user->def_lang != '' ? $user->def_lang : $MS_settings['default_lang']; 
		if($_SESSION["lang"] == 'ar') {$_SESSION["dirc"] = "rtl";} else {$_SESSION["dirc"] = "ltr";}
		
		// current year database
		if(isset($user->year) && $user->year != ''){
			$_SESSION['year'] = $user->year;
			if(!defined('DB_year')){
				define( 'DB_year', Db_prefix.$user->year);
			}
		} else {
			$today = time();
			
			$year = do_query_obj("SELECT * FROM years WHERE begin_date<=$today ORDER BY begin_date DESC LIMIT 1");
			if($year == false){
				$year =  do_query_obj("SELECT * FROM years ORDER BY year DESC LIMIT 1");
			}
			$_SESSION['year'] = $year->year;
			if(!defined('DB_year')){
				define('DB_year', Db_prefix.$year->year);
			}
		}
			// extra session data
		if(isset($user->cur_id)){
			$_SESSION['cur_id'] = $user->cur_id;
		}
		if(isset($user->std)){
			$_SESSION['std'] = $user->std;
		}
			// set last operation
		$_SESSION['last_op'] = time();
	}
	
}