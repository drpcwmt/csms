<?php

## BusMS Resources

## handel Driver, Matrons and bus files


if($this_system->type=='busms' && isset($_GET['templ'])){

	$resource_type = $_GET['templ'];

	include("modules/$resource_type/$resource_type.php");
} elseif($this_system->type== 'sms'){

	if( isset($_GET['templ'])){
		$resource_type = safeGet($_GET['templ']);

		if(isset($_GET['save'])){
			if(getPrvlg("resource_edit_$resource_type")){
				switch($resource_type){
					case 'levels':
						echo Levels::_save($_POST);
					break;	
					case 'classes':
						echo Classes::_save($_POST);
					break;	
					case 'groups':
						echo Groups::_save($_POST);
					break;	
					case 'materials':
						echo Materials::_save($_POST);
					break;	
					case 'principals':
						echo Principals::_save($_POST);
					break;	
					case 'coordinators':
						echo Coordinators::_save($_POST);
					break;	
					case 'supervisors':
						echo Supervisors::_save($_POST);
					break;	
					case 'profs':
						echo Profs::_save($_POST);
					break;	
					case 'halls':
						echo Halls::_save($_POST);
					break;	
					case 'tools':
						echo Tools::_save($_POST);
					break;	
				}
			} else {
				echo json_encode(array('error'=>$lang['no_privilege']));
			}
		} 
		elseif(isset($_GET['delete'])){
			$id = safeGet($_GET['id']);
			if(getPrvlg("resource_edit_$resource_type")){
				switch($resource_type){
					case 'levels':
						echo Levels::_delete($id);
					break;	
					case 'classes':
						echo Classes::_delete($id);
					break;	
					case 'groups':
						echo Groups::_delete($id);
					break;	
					case 'materials':
						echo Materials::_delete($id);
					break;	
					case 'principals':
						echo Principals::_delete($id);
					break;	
					case 'coordinators':
						echo Coordinators::_delete($id);
					break;	
					case 'supervisors':
						echo Supervisors::_delete($id);
					break;	
					case 'profs':
						echo Profs::_delete($id);
					break;	
					case 'halls':
						echo Halls::_delete($id);
					break;	
					case 'tools':
						echo Tools::_delete($id);
					break;	
				}
			} else {
				echo json_encode(array('error'=>$lang['no_privilege']));
			}
		} 
		elseif(isset($_GET['new'])){
			if(getPrvlg("resource_edit_$resource_type")){
				switch($resource_type){
					case 'levels':
						echo Levels::_new();
					break;	
					case 'classes':
						echo Classes::_new();
					break;	
					case 'groups':
						echo Groups::_new();
					break;	
					case 'materials':
						echo Materials::_new();
					break;	
					case 'principals':
						echo Principals::_new();
					break;	
					case 'coordinators':
						echo Coordinators::_new();
					break;	
					case 'supervisors':
						echo Supervisors::_new();
					break;	
					case 'profs':
						echo Profs::_new();
					break;	
					case 'halls':
						echo Halls::_new();
					break;	
					case 'tools':
						echo Tools::_new();
					break;	
				}
			} else {
				echo json_encode(array('error'=>$lang['no_privilege']));
			}
		} 

		// Specials cases handel
			// import profs
		/*elseif( $_GET['templ'] == 'profs' && isset($_GET['import'])){
			if(getPrvlg("resource_edit_profs")){
				echo json_encode(Profs::_import());
			} else {
				echo write_error($lang['no_privilege']);	
			}
		} 
			// add principal Levels
		elseif($_GET['templ'] == 'principals' && isset($_GET['updateform'])){
			if(getPrvlg("resource_edit_principals")){
				$principal = new Principals(safeGet($_GET['itemid']));
				echo $principal->updateForm();
			} else {
				echo write_error($lang['no_privilege']);	
			}
		}
			// delete principal Levels
		elseif($_GET['templ'] == 'principals' && isset($_GET['dellevel'])){
			$answer['error'] = '';
			if(getPrvlg("resource_edit_principals")){
				$id = $_POST['principal_id'];
				$level_id =  $_POST['level_id'];
				if(!do_query_edit("DELETE FROM principals WHERE id=$id AND levels=$level_id", $sms->database, $sms->ip)){
					$answer['error'] = 'Error';
				}
			} else {
				$answer['error'] = $lang['no_privilege'];
			}
			echo json_encode($answer);
		}
			// add coordinator Levels
		elseif($_GET['templ'] == 'coordinators' && isset($_GET['updateform'])){
			if(getPrvlg("resource_edit_coordinators")){
				$coordinator = new Coordinators(safeGet($_GET['itemid']));
				echo $coordinator->updateForm();
			} else {
				echo write_error($lang['no_privilege']);	
			}
		}
			// delete coordinator Levels
		elseif($_GET['templ'] == 'coordinators' && isset($_GET['dellevel'])){
			$answer['error'] = '';
			if(getPrvlg("resource_edit_coordinators")){
				$id = $_POST['coordinator_id'];
				$level_id =  $_POST['level_id'];
				if(!do_query_edit("DELETE FROM coordinators WHERE id=$id AND levels=$level_id", $sms->database, $sms->ip)){
					$answer['error'] = 'Error';
				}
			} else {
				$answer['error'] = $lang['no_privilege'];
			}
			echo json_encode($answer);
		}*/
		
		/************************************************************/
		// Save new sub
		/*elseif($_GET['templ'] == 'materials' && isset($_GET['mat_list_opt'])){
			$langs = do_query_array("SELECT * FROM materials WHERE `group_id`=1", $sms->database, $sms->ip);
			$langs_arr = array();
			$langs_arr[0] = '';
			foreach($langs as $lng){
				$langs_arr[$lng->id] = $lng->{'name_'.$_SESSION['dirc']};
			}
			echo write_select_options( $langs_arr, '', false);
		}

		// Save new sub
		elseif($_GET['templ'] == 'materials' && isset($_GET['savesub'])){
			echo Materials::saveSub($_POST);
		}
		// Rename sub Materials
		elseif($_GET['templ'] == 'materials' && isset($_GET['rename_sub'])){
			$answer['error'] = '';
			if(getPrvlg("resource_edit_materials")){
				$sub_id = $_POST['id'];
				$title =  $_POST['title'];
				if(!do_update_obj(array('title'=>$title), "id=$sub_id", 'materials_subs')){
					$answer['error'] = 'Error';
				}
			} else {
				$answer['error'] = $lang['no_privilege'];
			}
			echo json_encode($answer);
		}
			// Delete sub Materials
		elseif($_GET['templ'] == 'materials' && isset($_GET['del_sub'])){
			$answer['error'] = '';
			if(getPrvlg("resource_edit_materials")){
				$sub_id = $_POST['id'];
				if(!do_query_obj("SELECT * FROM materials_skills WHERE sub_id=$sub_id")){
					if(!do_delete_obj("id=$sub_id", 'materials_subs')){
						$answer['error'] = 'Error';
					}
				} else {
					$answer['error'] = 'Skills Exists';
				}
			} else {
				$answer['error'] = $lang['no_privilege'];
			}
			echo json_encode($answer);
		}
		
			
		elseif($_GET['templ'] == 'materials' && isset($_GET['skills'])){
			// Add Skill
			if(isset($_GET['add_skill'])){
				echo Materials::newSkill(safeGet('sub_id'));
			}
				// edit Skill
			elseif(isset($_GET['edit_skill'])){
				echo Materials::editSkill(safeGet('skill_id'));
			}
				// Save new Skill
			elseif(isset($_GET['saveskill'])){
				echo Materials::saveSkill($_POST);
			}
				// Delete Skill
			elseif(isset($_GET['del_skill'])){
				$answer['error'] = '';
				if(getPrvlg("resource_edit_materials")){
					$skill_id = $_POST['id'];
					if(!do_query_obj("SELECT * FROM services_skills_results WHERE skill_id=$skill_id", DB_year)){
						if(!do_delete_obj("id=$skill_id", 'materials_skills')){
							$answer['error'] = 'Error';
						}
					} else {
						$answer['error'] = 'Result Exists';
					}
				} else {
					$answer['error'] = $lang['no_privilege'];
				}
				echo json_encode($answer);
			}
		}*/

		// Load Item layout 
		elseif( isset($_GET['itemid'])){
			if(getPrvlg("resource_read_$resource_type")){
				$resource = new Resources( $resource_type, safeGet($_GET['itemid']));
				echo $resource->loadLayout();
			} else {
				echo write_error($lang['no_privilege']);	
			}
		}
		elseif(!isset($_GET['itemid'])){
			include("modules/$resource_type/$resource_type.php");
			
			
		}

	} else {
		require_once($this_system->type.'_resources_menu.php');
		echo $resource_menu . write_html('div', 'id="resource_main_div"  class="module_main_div"', '');
		
	}

}

?>