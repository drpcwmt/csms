<?php
/** Resources 
*
*
*/

class Resources{
	public $item = '';
	public $item_id = '';
	public $item_type = '';

	
	public function __construct($item_type, $item_id, $sms=''){
		if($sms == '' ){
			global $this_system;
			$sms = $this_system;
		}
		$this->item_type = $item_type;
		$this->item_id = $item_id;
		if($item_type == 'etabs'){
			$this->item = new Etabs($item_id, $sms);
		} elseif($item_type == 'levels'){
			$this->item = new Levels($item_id, $sms);
		} elseif($item_type == 'classes'){
			$this->item = new Classes($item_id,'', $sms);
		} elseif($item_type == 'groups'){
			$this->item = new Groups($item_id, $sms);
		} elseif($item_type == 'halls'){
			$this->item = new Halls($item_id, $sms);
		} elseif($item_type == 'tools'){
			$this->item = new Tools($item_id, $sms);
		} elseif($item_type == 'principals'){
			$this->item = new Principals($item_id, $sms);
		} elseif($item_type == 'coordinators'){
			$this->item = new Coordinators($item_id, $sms);
		} elseif($item_type == 'supervisors'){
			$this->item = new Supervisors($item_id, $sms);
		} elseif($item_type == 'profs'){
			$this->item = new Profs($item_id, $sms);
		} elseif($item_type == 'materials'){
			$this->item = new Materials($item_id, $sms);
		}
	}

	public function getName($other_lang = false){
		if($other_lang == false){
			return $_SESSION['lang'] == 'ar' ? $this->item->name_rtl : $this->item->name_ltr ;
		} else {
			return $_SESSION['lang'] == 'ar' ? $this->item->name_ltr : $this->item->name_rtl ;
		}
	}
	
	public function loadLayout(){
		
		return write_html('div', 'class="ui-corner-all ui-widget-content" style="padding:5px"',
			$this->item->loadLayout()
		);
	}
	
	static function getItemsToolbox($item_type, $item_id){
		global $lang;
		$item_toolbox = array();
		$container_div = '#'.$item_type.'-infos-'.$item_id;
		$editable = getPrvlg("resource_edit_$item_type");


		if(in_array($item_type, array('profs', 'supervisors', 'principals', 'coordinators'))){
			if($editable && $item_type == 'principals'){
				$item_toolbox[] = array(
					"tag" => "a",
					"attr"=> ' action="updatePrincipalLevels" principalid="'.$item_id.'"',
					"text"=> $lang['change'],
					"icon"=> "newwin"
				);
			}
			if($editable && $item_type == 'coordinators'){
				$item_toolbox[] = array(
					"tag" => "a",
					"attr"=> ' action="updateCoordinatorLevels" coordinator_id="'.$item_id.'"',
					"text"=> $lang['change'],
					"icon"=> "newwin"
				);
			}
			if(getPrvlg("login_read-$item_type")){
				$usergroup = substr($item_type, 0, -1);
				$item_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="settings" action="openUser" group="'.$usergroup .'" userid="'.$item_id.'"',
					"text"=> $lang['login'],
					"icon"=> "comment"
				);
			}
			if($editable){
				$item_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="saveResource" resourcetype="'.$item_type.'"',
					"text"=> $lang['save'],
					"icon"=> "disk"
				);
				$item_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="deleteResourceItem" resourcetype="'.$item_type.'" itemid="'.$item_id.'"',
					"text"=> $lang['delete'],
					"icon"=> "close"
				);
			}
		} else {
			if($editable){
				$item_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="saveResource" resourcetype="'.$item_type.'"',
					"text"=> $lang['save'],
					"icon"=> "disk"
				);
				$item_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="deleteResourceItem" resourcetype="'.$item_type.'" itemid="'.$item_id.'"',
					"text"=> $lang['delete'],
					"icon"=> "close"
				);
			}
		}
		if(!in_array($item_type, array('supervisors', 'profs'))){
			$item_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="print_pre" rel="'.$container_div .'"',
				"text"=> $lang['print'],
				"icon"=> "print"
			);
			$item_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="saveAsPdf" rel="'.$container_div .'"',
				"text"=> $lang['save_as_pdf'],
				"icon"=> "print"
			);
			$item_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="exportTable" rel="'.$container_div .'"',
				"text"=> $lang['export'],
				"icon"=> "disk"
			);
		}
		return createToolbox($item_toolbox);
		
	}
	
	static function loadItemsLayout($item_type){
		global $lang;
		$toolbox = array();
		$toolbox[] = array(
			"tag" => "span",
			"attr"=> 'style="margin:0px 7px 0px 2px"',
			"text"=>'<input type="text" class="ui-state-default ui-corner-left" onkeyup="filterResourceList(this.value)" onfocus="$(this).val(\'\')" onblur="resetSearchMenu(this)" value="'.$lang['search'].'" />'.
				write_html('text', 'class="hoverable ui-state-default ui-corner-right" style="padding:3px" onClick="resetSearchMenu($(this).prev(\'input\')" title="'. $lang['reset'].'"',
					write_icon('refresh')
				),
			"icon"=> ""
		);
		if(getPrvlg('resource_edit_'.$item_type)){
			$toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="newResourceItem" title="'. $lang['new'].'" templ="'.$item_type.'"',
				"text"=> '',
				"icon"=> "document"
			);
			
		}
		
		$toolbox[] = array(
			"tag" => "a",
			"attr"=> 'class="print_but" rel="#resource_list" title="'. $lang['print_list'].'"',
			"text"=> '',
			"icon"=> "print"
		);
		if($item_type == 'profs'){
			$toolbox[] =array(
				"tag" => "a",
				"attr"=> 'action="importProfs" title="'. $lang['import'].'"',
				"text"=> "",
				"icon"=> "arrowreturnthick-1-s"
			);
		}
		
		$layout = new Layout();
		$layout->resource_type_name = $lang[$item_type];
		$layout->item_type = $item_type;
		$layout->toolbox = createToolbox($toolbox);
		$layout->items_list = Resources::loadtItemsList($item_type);
		if($item_type == 'classes' ){
			$layout->year = '-'.$_SESSION['year'];
		}
		return fillTemplate('modules/resources/templates/resources.tpl', $layout);
	}
	
	static function loadtItemsList($item_type){
		if($item_type == 'etabs'){
			$list = Etabs::getList();
		} elseif($item_type == 'levels'){
			$list = Levels::getList();
		} elseif($item_type == 'classes'){
			$list = Classes::getList();
		} elseif($item_type == 'groups'){
			$list = Groups::getList();
		} elseif($item_type == 'halls'){
			$list = Halls::getList();
		} elseif($item_type == 'tools'){
			$list = Tools::getList();
		} elseif($item_type == 'principals'){
			$list = Principals::getList();
		} elseif($item_type == 'coordinators'){
			$list = Coordinators::getList();
		} elseif($item_type == 'supervisors'){
			$list = Supervisors::getList();
		} elseif($item_type == 'profs'){
			$list = Profs::getList();
		} elseif($item_type == 'materials'){
			$list = Materials::getList();
		}
		
		$out = '';
		foreach($list as $item){
			if(isset($item->id) && $item->id != ''){
				 $out .=write_html( 'li', 'itemid="'.$item->id.'" rel="'.$item_type.'" class="hoverable clickable ui-stat-default ui-corner-all" action="openResourceInfos"', 
					($item_type == 'materials' ?
						write_html('span', 'style="background-color:#'.$item->color.'" class="color"', '')
					: '').
					write_html('text', 'class="holder-'.$item_type.'-'.$item->id.'"',
						$item->getName()
					)
				);	
			}
		}
		return $out;
	}
}
