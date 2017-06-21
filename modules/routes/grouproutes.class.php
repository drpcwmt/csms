<?php

  /* Routes Groups
  *
  *
  */

class GroupRoutes {
    function __construct($id, $busms='') {
		if($busms == ''){
			global $busms;
		}
		$this->busms = $busms;
		$group = do_query_obj("SELECT * FROM route_group WHERE id=$id", $busms->database, $busms->ip);
		foreach ($group as $key => $value) {
			$this->$key = $value;
		}
    }
	
	public function getName(){
		return $this->title;
	}
	
	public function getRoutesList(){
		$routes = array();
		$rows  = do_query_array("SELECT id FROM bus_routes WHERE group_id=$this->id", $this->busms->database, $this->busms->ip);
		if($rows != false && $rows>0){
			foreach($rows as $row){
				$routes[] = new Routes($row->id);
			}
		}
		return $routes;
	}
	
	static function getList(){
		global $busms;
		$out = array();
		$groups = do_query_array("SELECT * FROM route_group", $busms->database, $busms->ip);
		foreach ($groups as $row) {
			$out[] = new GroupRoutes($row->id);
		}
		return sortArrayOfObjects($out, $busms->getItemOrder('routes_groups'), 'id');
	}
	
	public function getFees($sms, $year){
		$result = do_query_array("SELECT * FROM school_fees WHERE con='bus' AND con_id=$this->id AND year=$year", $sms->database, $sms->ip);
		$out = array();
		foreach($result as $f){
			$out[] = new Fees($f->id, $sms);
		}
		return  $out;
	}

	public function loadLayout(){
		global $sms, $prvlg;
		if(isset($sms->ccid)){ 
			$busms= $sms->getBusms();
		} else {
			global $busms;
		}
		$layout = new Layout($this);
		$layout->template = "modules/routes/templates/group_layout.tpl";
		$layout->group_id = $this->id;
		$year = $_SESSION['year'];		
		
		$layout->bus_fees_rows = '';
		if($prvlg->_chk('group_fees_read')){
			$fees_form = new Layout($this);
			$fees_form->template = "modules/routes/templates/group_fees_layout.tpl";
			$fees_form->busms_id = isset($this->busms) ? $this->busms->id : '0';
			$fees_form->sms_id = $sms->id;
			$fees_form->group_id = $this->id;
			$fees_form->group_name = $this->getName();
			$fees_form->bus_fees_rows = '';
			if($prvlg->_chk('group_fees_edit') == false){
				$fees_form->prvlg_group_fees_edit= 'hidden';
			}
			
			$fees = $this->getFees($sms, $year);
			foreach($fees as $f){
				$row = new Layout($f);
				$row->template = "modules/fees/templates/level_fees_rows.tpl";
				$row->value = $f->debit !=0 ? $f->debit : ($f->credit * -1);
				$row->discount_on = $f->discount==1 ? 'checked' : '';
				$row->discount_off = $f->discount!='1' ? 'checked' : '';
				$row->main_code = Accounts::fillZero('main', $f->main_code);
				$row->sub_code = Accounts::fillZero('sub', $f->sub_code);
				$row->currency_opts = Currency::getOptions($f->currency);
				$row->busms_id = $fees_form->busms_id;
				$row->sms_id =$sms->id;			
				$fees_form->bus_fees_rows .= $row->_print();
			}
			$layout->fees_form = $fees_form->_print();
		}
		if($prvlg->_chk('group_edit') == false){
			$layout->prvlg_group_edit = 'hidden';
		}
		

		if($prvlg->_chk('group_fees_read') == false){
			$layout->prvlg_group_fees_read= 'hidden';
		}
		
		// Routes
		$routes = $this->getRoutesList();
		$layout->routes_trs = '';
		foreach($routes as $route){
			$driver = ($route->driver_id!='' && $route->driver_id!='0' ? new Drivers($route->driver_id) :'');
			$matron = ($route->matron_id!='' && $route->matron_id!='0'? new Matrons($route->matron_id) : '');
			$layout->routes_trs .= write_html('tr', '',
				write_html('td', '', 
					($prvlg->_chk('route_read') ? 
						write_html('button', 'class="circle_button hoverable ui-state-default" action="openRoute" route_id="'.$route->id.'" busms_id="'.$this->busms->id.'"',
							write_icon('extlink')
						)
					: '')
				).
				write_html('td', 'align="center"', $route->no).
				write_html('td', '', $route->region).
				write_html('td', '', ($driver!='' ? $driver->getName() :'')).
				write_html('td', '', ($matron!='' ?$matron->getName():'')).
				write_html('td', 'align="center"', count(do_query_array("SELECT * FROM route_members WHERE route_id=$route->id", $busms->database, $busms->ip)))
			); 
		}
		return $layout->_print();
	}
	
	static function loadMainLayout(){
		global $busms, $sms;
		$layout = new Layout();
		$layout->template = "modules/routes/templates/group_main_layout.tpl";
		$layout->busms_id = $busms->id;
		$groups = $busms->getGroups();
		$sms = SMS::getList();
		$layout->schools_lis = '';
		foreach($sms as $school){
			$layout->schools_lis .= write_html('li', '',
				write_html('a', 'action="listBySchool" sms_id="'.$school->id.'" class="ui-state-default hoverable"', $school->code)
			);	
		}
		$first_group = $groups[0];
		$layout->items_list ='';
		$first = true;
		foreach($groups as $group){
			 $layout->items_list .= write_html( 'li', 'itemid="'.$group->id.'" busms_id="'. $busms->id.'" '.(isset($sms->id) ? 'sms_id="'. $sms->id.'"' : '').' class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openRouteGroup"', 
				write_html('text', 'class="holder-routegroup-'.$group->id.'"',
					$group->title
				)
			);
			if($first){
				$layout->group_layout = $group->loadLayout();
			}
			$first = false;
		}
		
		return $layout->_print();
	}
}