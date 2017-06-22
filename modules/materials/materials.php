<?php
	if( isset($_GET['mat_list_opt'])){
		$langs = do_query_array("SELECT * FROM materials WHERE `group_id`=1", $sms->database, $sms->ip);
		$langs_arr = array();
		$langs_arr[0] = '';
		foreach($langs as $lng){
			$langs_arr[$lng->id] = $lng->{'name_'.$_SESSION['dirc']};
		}
		echo write_select_options( $langs_arr, '', false);
	}

	// Save new sub
	elseif( isset($_GET['savesub'])){
		echo Materials::saveSub($_POST);
	}
	// Rename sub Materials
	elseif( isset($_GET['rename_sub'])){
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
	elseif( isset($_GET['del_sub'])){
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
	
		
	elseif( isset($_GET['skills'])){
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
	} else {
		echo Resources::loadItemsLayout($resource_type);
	}
?>