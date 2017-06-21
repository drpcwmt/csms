<?php
/** Documents 
*
*
*/

class Documents{

	private $database = LMS_Database;
	
	public function __construct(){
		if(!is_writable(docRoot.'attachs')){
			die('folder attachs must be writable');
		}
		$this->user = new Users($_SESSION['group'], $_SESSION['user_id']);	
		$this->view = 'icon';
	}
	
	public function loadListView($items, $options){
		global $lang;
		
		// options
		if(!isset($options->selectable)) $options->selectable = false;
		if(!isset($options->sharable)) $options->sharable = false;
		if(!isset($options->openable)) $options->openable = true;
		if(!isset($options->downloadable)) $options->downloadable = true;
		if(!isset($options->editable)) $options->editable = false;
		if(!isset($options->deattach)) $options->deattach = false;
		if(!isset($options->mini)) $options->mini = true;
				
		$head = write_html('thead', '', 
			write_html('tr', '', 
				($options->selectable ? write_html('th', 'width="20" style="background-image:none"', '') :'').
				($options->sharable ? write_html('th', 'width="22" style="background-image:none"', '') :'').
				($options->downloadable ? write_html('th', 'width="22" style="background-image:none"', '') :'').
				($options->openable ? write_html('th', 'width="22" style="background-image:none"', '') :'').
				($options->editable ? write_html('th', 'width="22" style="background-image:none"', '') :'').
				write_html('th', '', $lang['file_name']).
				($options->mini == false ?
					write_html('th', 'width="120"', $lang['date']).
					write_html('th', 'width="50"', $lang['file_size'])
				: '')
			)
		);
		
		$trs = array();
		if(isset($items['folders'])){
			$folders = $items['folders'];
			foreach($folders as $folder){
				$path = $folder->path;
				$trs[] =  $folder->loadListView($options);			
			}
		}
		
		$files = $items['files'];
		foreach($files as $file ){
			$path = $file->path;
			$trs[] = $file->loadListView($options);			
	
		}
		return write_html('table', 'class="tablesorter"', 
			$head.
			write_html('tbody', ' class="items_contener"', implode('', $trs))
		);
	}
	
	public function loadIconView($items, $options){
		global $lang;
		
		
		// options
		if(!isset($options->selectable)) $options->selectable = false;
		if(!isset($options->sharable)) $options->sharable = false;
		if(!isset($options->openable)) $options->openable = true;
		if(!isset($options->downloadable)) $options->downloadable = true;
		if(!isset($options->editable)) $options->editable = false;
		if(!isset($options->deattach)) $options->deattach = false;
		if(!isset($options->mini)) $options->mini = true;

		$a = array();
		if(isset($items['folders'])){
			$folders = $items['folders'];
			foreach($folders as $folder){
				$path = $folder->path;
				$a[] =  $folder->loadIconView($options);			
			}
		}
		if(isset($items['files'])){
			$files = $items['files'];
			foreach($files as $file ){
				$path = $file->path;
				$a[] = $file->loadIconView($options);			
		
			}
		}
		if(count($a) > 0){
			return write_html('div', 'class="ui-corner-all ui-widget-default" style="padding:"10px"',
				write_html('ul', 'style="margin:5px; padding:0px; list-style:none" class="items_contener"',
					implode('', $a)
				)
			);
		} else {
			return '';
		}
	}
	
	public function buildNavBar($full_path, $root_path){
		$link = $root_path;
		$navs = explode('/', str_replace($root_path, '', $full_path));	
		$nav_items = array();
		for($i=0; $i< count($navs); $i++){
			if($navs[$i] != '.' && $navs[$i] != '..' && $navs[$i] != ''){
				$link .= '/'.$navs[$i];
				$nav_items[] =  '<span  class="ui-state-default hoverable hand " action="browseDir" path="'.$link.'" >'.
				write_html('em', 'style="padding:3px"', $navs[$i]).
				'<a class="ui-icon ui-icon-circle-triangle-e" style="height:16px"></a></span>';
			}
		}
		return implode('', $nav_items);
	}
	
	public function buildToolbox($options){
		global $lang;
		if($options->selectable){
			$toolbox = array();
			if($options->downloadable){
				$toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="downloadFiles" class="click-disabled"',
					"text"=> $lang['download'],
					"icon"=> "arrowthickstop-1-s"
				);
			}
			
			if($options->can_upload){
				$upload_event = "uploadFile('$options->destination', '', '', 'reloadCurrent(); reloadSpaceBar();', 'true')";
			} else {
				$upload_event = "MS_alert(getLang('max_space_cant_upload'))";
			}
			$toolbox[] = array(
				"tag" => "a",
				"attr"=> 'onclick="'.$upload_event.'"',
				"text"=> $lang['upload'],
				"icon"=> "arrowthickstop-1-n"
			);
			
			if($options->editable){
				$toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="createNewDir" ',
					"text"=> $lang['new_folder'],
					"icon"=> "document"
				);

				$toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="cutFile" class="click-disabled"',
					"text"=> $lang['cut'],
					"icon"=> "scissors"
				);

				$toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="pasteFile" ',
					"text"=> $lang['paste'],
					"icon"=> "clipboard"
				);
				
				$toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="renameFile" class="click-disabled"',
					"text"=> $lang['rename'],
					"icon"=> "pencil"
				);

				$toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="shareFile" class="click-disabled"',
					"text"=> $lang['share'],
					"icon"=> "transferthick-e-w"
				);

				$toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="deleteFiles" class="click-disabled"',
					"text"=> $lang['delete'],
					"icon"=> "trash"
				);
			}
			$toolbox[] = array(
				"tag" => "span",
				"attr"=> 'style="margin:0px 10px" class="ui-corner-all ui-state-default"',
				"text"=> write_html('a', 'class="hoverable hand clickable '.($options->view == 'list' ? 'ui-state-active' :'').'" action="setView" viewtype="list" action="reloadCurrent" title="'.$lang['list_view'].'"', '<img src="assets/img/list_view.png" width="14" />').
				 write_html('a', 'class="hoverable hand clickable '.($options->view == 'icon' ? 'ui-state-active' :'').'" action="setView" viewtype="icon" title="'.$lang['icon_view'].'"', '<img src="assets/img/icon_view.png" width="14" />'), 
				"icon"=> ""
			);			

			$toolbox[] = array(
				"tag" => "span",
				"attr"=> 'style="margin:0px 7px 0px 2px"',
				"text"=>'<input type="text" class="ui-state-default ui-corner-left" placeholder="'.$lang['search'].'" onfocus="$(this).val(\'\')" onkeyup="filterFilesList(this.value)"  />'.
					write_html('text', 'class="ui-state-default hoverable ui-corner-right" style="padding:3px" onClick="$(this).prev(\'input\').val(\'\');filterFilesList(\'\')" title="'. $lang['reset'].'"',
						write_icon('refresh')
					),
				"icon"=> ""
			);
			

			return createToolbox($toolbox);
		} else {
			return '';
		}
			
	}

	public function browseDir($dir, $options){
		$directory = new Folders($dir);
		$scan = $directory->scanDir(false);
		$options->view = isset($options->view) ? $options->view : $this->view;
		$options->selectable = true;
		$options->mini = false;
		if($options->view == 'list'){
			return $this->loadListView($scan, $options);
		} else {
			return $this->loadIconView($scan, $options);
		}
	}
	
	public function loadMainLayout(){
		global $lang;
		$myDocs = new Documents();
		$out = write_html('div', 'class="ui-widget-header ui-corner-top"',
			write_html('h2', 'class="reverse_align big_title"',$lang['documents'])
		).
		write_html('div', 'class="ui-corner-bottom ui-widget-content transparent_div"',
			write_html('table', 'width="100%"',
				write_html('tr', '',
					write_html('td', 'valign="top" width="275px"',
						$myDocs->getLibraryList()
					).
					write_html('td', 'valign="top" style="padding:10px"',
						write_html('div', 'id="browser_td"', 
							$myDocs->loadMyDocs()
						)
					)
				)
			)
		);
		return $out;
	}

	public function getLibraryList(){
		global $lang;
		
		// Shares
		$max_size = Documents::getUserDocsMaxSize();
		$private_shares = write_html('li', 'class=" hand ui-state-default ui-corner-all clickabel hoverable '.($max_size == 0 ? 'ui-state-active' :'').'" action="openSharedFiles"', $lang['shared_files']);

			// librarys
		$edit_librarys_prvlg = getPrvlg('edit_librarys');
		$lib_sql = "SELECT files_librarys.* FROM files_librarys, files_librarys_shares
			WHERE files_librarys_shares.`read`=1
			AND files_librarys.id = files_librarys_shares.lib_id";
			
		if($_SESSION['group'] == 'student'){
			$parents = getParentsArr('student', $_SESSION['user_id']);	
			foreach($parents as $array){
				$con =$array[0];
				$con_id= $array[1];
				$where[] = "(files_librarys_shares.con='$con' AND files_librarys_shares.con_id=$con_id)";
			}
			$lib_sql .= " AND (".implode(' OR ', $where).")";
		} elseif(in_array($_SESSION['group'], array('prof', 'supervisor'))){
			$lib_sql .= " AND files_librarys_shares.con='prof'";
		}  else{ // super admin must see all librarys
			$lib_sql .= " AND files_librarys_shares.con='admin'";
		}
		
		$libs = do_query_array($lib_sql, $this->database);
		$library_list = '';
		foreach($libs as $lib){
			$library_list .= write_html('li', 'class="hand ui-state-default ui-corner-all clickabel hoverable" action ="openLibrarys" libid="'.$lib->id.'"', 
				$lib->title.
				($edit_librarys_prvlg ? 
					write_html('a', 'class="rev_float ui-state-default ui-corner-all hoverable mini_circle_button" action="editLib" libid="'.$lib->id.'"', write_icon('pencil'))
				: '')
			);
		}
		
		// Services
		$services_list = '';
		$menu_service = array();
		if(in_array($_SESSION['group'], array('student', 'parent', 'prof', 'supervisor', 'superadmin', 'principal'))){
			$menu_service = array();
			if($_SESSION['group'] == 'student' || $_SESSION['group'] == 'parent'){
				$student = new Students($_SESSION['std_id']);
				$menu_service = $student->getServices();
			} else{
				if($_SESSION['group'] == 'prof'){
					$prof = new Profs($_SESSION['user_id']);
					$menu_service = $prof->getServices();
				} elseif($_SESSION['group'] == 'supervisor') {
					$supervisor = new Supervisors($_SESSION['user_id']);
					$menu_service = $supervisor->getServices();
				}
			}

			foreach($menu_service as $service){
				$level = new Levels($service->level_id);
				$services_list .= write_html('li', 'class="hand ui-state-default ui-corner-all clickabel hoverable" action="openServiceFiles" serviceid="'.$service->id.'"',
					$service->getName().
					(in_array($_SESSION['group'], array('prof', 'supervisor', 'superadmin')) ?
						' '. $level->getName()
					: '')
				); 
			}
		}

		return write_html('div', 'class="accordion"',
			($max_size != 0 ? 
				write_html('h3', '', 
					write_html('a', 'action="openMyDocument"', $lang['my_documents'])
				).
				$this->loadSpaceBar()
			: '').			
			write_html('h3', '',
				write_html('a', '', $lang['shares'])
			).
			write_html('div', '',
				write_html('ul', ' class="list_menu listMenuUl " id="library_list"',
					$private_shares.
					$library_list.
					($edit_librarys_prvlg ? 
						write_html('div', 'class="toolbox"',
							write_html('a', 'onclick="addNewLibrary()"', 
								write_icon('document')."&nbsp;".$lang['add']
							)
						)
					: '')
				)
			).
			(count($menu_service) > 0 ?
				write_html('h3', '', 
					write_html('a', '', $lang['materials'])
				).
				write_html('div', '',
					write_html('ul', 'style="list-style:none; padding:0; margin:0px" class="list_menu listMenuUl"',
						$services_list
					)
				)
			: '')
		);
	}

	public function loadSpaceBar(){
		global $lang, $sms;
		$max_size = Documents::getUserDocsMaxSize();
		$myDocs = new Documents();
		$user_path = $myDocs->user->doc_path;
		if($user_path == ''){
			$user_path = "attachs/".$sms->getSettings('docs_root_users')."/".$this->user->name;
		}
		
		if($user_path != false){
			// Make dir if not exists
			if(!is_dir($user_path)){
				mkdir( $user_path, 0777, true);
			}
		}
		$user_folder = new Folders($user_path);
		
		return write_html('div', 'id="spaceBarHolder"', 
			write_html('div', ' align="center" style="padding:5px" id="document_space_div"',
				write_html('div', 'id="sizeDiv" sizevalue="'.round($user_folder->getSize() / ($max_size *1048576), 2)*100 .'" style="height:12px"', '').
				write_html('span', 'class="mini"', write_html('b', '',$lang['used'].': '). formatSize($user_folder->getSize())).
				write_html('span', 'class="mini '.($user_folder->getSize() > $max_size*1048576 ? 'error' : '').'"', write_html('b', '',$lang['free'].': '). formatSize(($max_size*1048576) - $user_folder->getSize())).
				write_html('span', 'class="mini"', write_html('b', '',$lang['used'].': '). formatSize($max_size*1048576))
			)
		);
	}
	
	public function loadMyDocs($incpath='', $view='icon'){
		global $MS_settings, $lang, $normalize, $sms;
		
		$max_size = Documents::getUserDocsMaxSize();		
		$root_path = $this->user->doc_path;
		if($root_path == ''){
			$root_path = "attachs/".$sms->getSettings('docs_root_users')."/".$this->user->name;
		}
		
		if($root_path != false){
			$system_path = $root_path;
			// Make dir if not exists
			if(!is_dir($system_path)){
				mkdir( $system_path, 0777, true);
			}
			$directory = new Folders($root_path);
			// check for upload avaivility
			$editable = $max_size==0 ? false :true;
			$browsrOpts = new stdClass();
			$browsrOpts->can_upload = ($max_size != 0 && $directory->getSize() > $max_size*1048576) ? false : true;
			$browsrOpts->editable = $max_size==0 ? false :true;
			$browsrOpts->sharable = true;
			$browsrOpts->view = $view;
			
			$full_path = $system_path;
			if(!empty($incpath)){
				$incpath = str_replace($root_path.'/', '', $incpath);
				$full_path .= '/'.sqlToSystemPath($incpath);
			}
			$browsrOpts->destination = $root_path.'/'.$incpath;
		
			// Nav menu root
			$nav_bar = write_html('span', 'class="ui-state-default hoverable hand" action="openMyDocument"',
				'<img src="assets/img/filemanger_icons/folder.png" border="0" width="16" height="16" class="def_float" />'.
				write_html('em', 'style="padding:3px"', $lang['my_documents']).
				'<a class="ui-icon ui-icon-circle-triangle-e" style="height:16px"></a>'
			).
			$this->buildNavBar($root_path.'/'.$incpath, $root_path); 
			
			$files_table = write_html('form', 'id="explorer_form"',
				'<input type="hidden" id="cur_folder" value="'.$incpath.'" />'.
				'<input type="hidden" id="doc_type" value="mydoc" />'.
				'<input type="hidden" id="doc_view" value="'.$view.'" />'.
				$this->browseDir($full_path, $browsrOpts)
			);
			
			return $this->buildToolbox($browsrOpts).
			write_html('div', 'class="ui-widget-content address_bar"', $nav_bar).
			$files_table;
		}
	}

	public function loadLibrary($libid, $incpath='', $view='icon'){
		global $MS_settings, $lang;
		$lib = new sharedLibrary($libid);
		
		$max_size = $lib->max_size;		
		$root_path = $lib->path;
		$browsrOpts = new stdClass();
		$browsrOpts->editable = $lib->is_editable();
		$browsrOpts->can_upload = $browsrOpts->editable ? $lib->can_upload : false;
		$browsrOpts->sharable = false;
		$browsrOpts->view = $view;
		
		$system_path = sqlToSystemPath($root_path);
		
		$full_path = $system_path;
		if(!empty($incpath)){
			$incpath = str_replace($root_path.'/', '', $incpath);
			$full_path .= '/'.($incpath);
		}
		$browsrOpts->destination = systemToSqlPath($full_path);

		// Nav menu root
		$nav_bar =write_html('span', 'class="ui-state-default hoverable hand" action="openLibrarys" libid="'.$lib->id.'"',
			'<img src="assets/img/filemanger_icons/folder.png" border="0" width="16" height="16" class="def_float" />'.
			write_html('em', 'style="padding:3px"', $lib->title).
			'<a class="ui-icon ui-icon-circle-triangle-e" style="height:16px"></a>'
		).
		$this->buildNavBar($root_path.'/'.$incpath, $root_path); 
		
		$files_table = write_html('form', 'id="explorer_form"',
			'<input type="hidden" id="cur_folder" value="'.systemToSqlPath($incpath).'" />'.
			'<input type="hidden" id="doc_type" value="lib" />'.
			'<input type="hidden" id="doc_view" value="'.$view.'" />'.
			'<input type="hidden" id="libid" value="'.$lib->id.'" />'.
			$this->browseDir($full_path, $browsrOpts)
		);
		
		return $this->buildToolbox($browsrOpts).
		write_html('div', 'class="ui-widget-content address_bar"', $nav_bar).
		$files_table;
	}

	public function loadServices($service_id, $incpath='', $view='icon'){
		global $MS_settings, $lang, $normalize;
		$max_size = $normalize($MS_settings['docs_services_max']);		
		
		$service = new services($service_id);
		
		$root_path = 'attachs/services/';
		$full_path = $root_path.$service->id.'/';
		$system_path = sqlToSystemPath($full_path);

		if(!is_dir($system_path)){
			$createFolder = Folders::_new($root_path, $service->id);
		}
	
		if(!empty($incpath)){
			$incpath = str_replace($full_path.'/', '', $incpath);
			$system_path .= '/'.sqlToSystemPath($incpath);
		}
		
		$directory = new Folders($system_path);
		$browsrOpts = new stdClass();		
		$browsrOpts->destination = $full_path.'/'.$incpath;
		$browsrOpts->editable = services::check_user_service_privilege($service_id);;
		$browsrOpts->can_upload = ($browsrOpts->editable==false || ($max_size != 0 && $directory->getSize() > $max_size)) ? false : true;
		$browsrOpts->sharable = false;
		$browsrOpts->view = $view;
		
		
		$level = new Levels($service->level_id);
		// Nav menu root
		$nav_bar =write_html('span', 'class="ui-state-default hoverable hand" action="openServiceFiles" serviceid="'.$service->id.'"',
			'<img src="assets/img/filemanger_icons/folder.png" border="0" width="16" height="16" class="def_float" />'.
			write_html('em', 'style="padding:3px"', $service->getName().'-'.$level->getName()).
			'<a class="ui-icon ui-icon-circle-triangle-e" style="height:16px"></a>'
		).
		$this->buildNavBar($full_path.'/'.$incpath, $full_path); 
		
		$files_table = write_html('form', 'id="explorer_form"',
			'<input type="hidden" id="cur_folder" value="'.$incpath.'" />'.
			'<input type="hidden" id="doc_type" value="services" />'.
			'<input type="hidden" id="doc_view" value="'.$view.'" />'.
			'<input type="hidden" id="serviceid" value="'.$service->id.'" />'.
			$this->browseDir($system_path, $browsrOpts)
		);
		
		return $this->buildToolbox($browsrOpts).
		write_html('div', 'class="ui-widget-content address_bar"', $nav_bar).
		$files_table;
	}
		
	public function getSharedFiles($view='icon'){
		global $lang;
		$links = array();
		$where = array();

		if($_SESSION['group'] == 'student'){
			$parents = getParentsArr('student', $_SESSION['user_id']);
			foreach($parents as $array){
				$con =$array[0];
				$con_id= $array[1];
				$where[] = "(con='$con' AND con_id=$con_id)";
			}
			$sql = "SELECT files.link FROM files_share, files
			WHERE ((con='student' AND con_id=".$_SESSION['user_id'].")
			OR ".implode(' OR ', $where).")";
		} elseif(in_array($_SESSION['group'], array('principal', 'superadmin'))){
			$prof_level = array();
			$class_arr = Classes::getList();
			foreach($class_arr as $class){
				$where []= "(con='class' AND con_id=$class->id)";
				if(!in_array($class->level_id, $prof_level)){
					$prof_level[] = $class->level_id;
					$where[] = "(con='level' AND con_id=$class->level_id)";
				}
			}
			$sql = "SELECT files.link, files_share.date FROM files_share, files
			WHERE ((con='".$_SESSION['group']."' AND con_id=".$_SESSION['user_id'].")
			OR (con='admin' AND con_id=".$_SESSION['user_id'].")
			OR ".implode(' OR ', $where).")";
		
		} else {			
			$sql = "SELECT files.link, files_share.date FROM files_share, files
			WHERE con='admin' AND con_id=".$_SESSION['user_id'];
		} 
	
		$sql .= " AND files.link=files_share.link AND files.owner_id!=".$_SESSION['user_id']." ORDER BY files_share.date DESC";
		
		//echo $sql;
		$shared_files = do_query_array($sql, LMS_Database);
		if($shared_files != false && count($shared_files) >0){
			foreach($shared_files as $file){
				try{
					$links['files'][] = new Files($file->link);
				} catch(Exception $e){
					//
				}
			}
		}
		
		// File Table
		$options = new stdClass();
		$options->selectable = true;
		$options->sharable = false;
		$options->editable = false;
		$options->mini = false;

		$browsrOpts = new stdClass();
		$browsrOpts->editable = false;
		$browsrOpts->can_upload = false;
		$browsrOpts->sharable = false;
		$browsrOpts->selectable = true;
		$browsrOpts->downloadable = true;
		$browsrOpts->view = $view;
		
		return $this->buildToolbox($browsrOpts).
		 write_html('form', 'id="explorer_form"',
			'<input type="hidden" id="doc_type" value="shared" />'.
			'<input type="hidden" id="doc_view" value="'.$view.'" />'.
			($view == "icon" ?
				$this->loadIconView($links, $options)
			:	
				$this->loadListView($links, $options)
			)
		);
	}
	
	
	static function getUserDocsMaxSize(){
		global $MS_settings;
		if($_SESSION['group'] == 'student'){
			$max_size = $MS_settings['docs_std'];
		} elseif($_SESSION['group'] == 'prof'){
			$max_size = $MS_settings['docs_prof'];
		} else {
			$max_size = $MS_settings['docs_user'];
		}
		return $max_size;
	}

	static function deleteLibrary($lib_id){
		die ('Error not functional');
	}

	static function loadAttachList($con, $con_id, $editable=false){
		switch ($con){
			case 'homework':
				$attachements = do_query_resource("SELECT link FROM homeworks_attachs WHERE homework_id=$con_id", LMS_Database);
			break;
			case 'summary':
				$attachements = do_query_resource("SELECT link FROM summarys_attachs WHERE summary_id=$con_id", LMS_Database);
			break;
		}	
		$file_arr = array();
		if($attachements != false && mysql_num_rows($attachements) > 0){
			while($attach = mysql_fetch_assoc($attachements)){
				$file_arr[] = $attach['link'];
			}
			$attach_div = Documents::buildAttachTable($file_arr, $editable);
		} else {
			$attach_div = '';
		}
		return $attach_div;
	}

	static function buildAttachTable($file_arr, $editable=false){
		global $lang;
		$out =  '';
		foreach($file_arr as $link){
			$file = new Files($link);
			/*$file_s = do_query("SELECT path FROM files WHERE link='$link'", LMS_Database);
			$path = $file_s['path'];
			$size = '';//@filesize(getSystemPath($path));
			$f = explode('/', $path);
			$file_name = $f[count($f)-1];
			$extention = substr($file_name, strrpos($file_name, ".")+1);*/
			$out .=  write_html('tr', '',
				($editable ? 
					write_html('td', 'width="20"',
						 write_html('a', 'href="#" class="ui-corner-all ui-state-default hand hoverable" title="'.$lang['remove'].'" action="dettachFile" module="documents" link="'.$link.'" style="float:left"', write_icon('close'))
						 )
				: '').
				write_html('td', 'width="20"',
					'<img src="'.$file->getThumb().'" width="24" />'
				).
				write_html('td', '',
					write_html('a', 'class="hand" href="'.$file->path.'" target="_blank" onclick="openFile(\''.$link.'\')"', $file->filename)
				)
			);
		}
		return $out;
	}
}

?>