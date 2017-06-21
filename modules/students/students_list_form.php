<?php
## student list Form 
$main_db = DB_student;
$year_db = DB_year;
$student_fields = getTableFields( 'student_data', MySql_Database);
$parent_fields = getTableFields( 'parents', MySql_Database);
$hidden_array= array( 'parent_id', 'old_sch_grade', 'comment', 'guardians', 'country', 'country_ar', 'father_zip', 'mother_zip', 'father_city', 'mother_city', 'father_country', 'mother_country', 'father_city_ar', 'mother_city_ar', 'father_country_ar', 'mother_country_ar', 'father_resp', 'mother_resp', 'father_emp', 'mother_emp');
$hidden_filter_array= array('id', 'parent_id', 'guardians', 'father_resp', 'mother_resp', 'comment');


$order_by = array(
	'0'=>'',
	DB_student.'.student_data.id'=> $lang['id'],
	DB_student.'.student_data.name' => $lang['name'],
	DB_student.'.student_data.name_ar' => $lang['name_ar'],
	DB_student.'.levels.id' => $lang['levels'],
	DB_year.'.classes.id' => $lang['classes'],
	DB_year.'.groups.id' =>	$lang['group'],
	DB_student.'.levels.id' => $lang['levels'],
	DB_year.'.classes.id' => $lang['classes'],
	DB_year.'.groups.id' => $lang['group'],
	DB_student.'.student_data.sex' => $lang['sex'],
	DB_student.'.student_data.religion' => $lang['religion'],
	DB_student.'.student_data.nationality' => $lang['nationality'],
	DB_student.'.student_data.birth_date' => $lang['birth_date']
	
);


// Filters
$filter_array = array();
$filter_array[] = write_html('option', 'value=""', '');
foreach($student_fields as $f){
	if(!in_array($f, $hidden_filter_array)){
		$filter_array[] = write_html('option', 'value="'.DB_student.'.student_data.'.$f.'" t="student_data" db="'.DB_student.'"', $lang[$f]);
	}
}
foreach($parent_fields as $f){
	if(!in_array($f, $hidden_filter_array)){
		$filter_array[] = write_html('option', 'value="'.DB_student.'.parents.'.$f.'" t="parents" db="'.DB_student.'"', $lang[$f]);
	}
}

$filter_array[] = write_html('option', 'value="'.DB_year.'.classes_std.new_stat"', $lang['new_stat']);


// Student fieldset
foreach($student_fields as $field){
	if(!in_array($field, $hidden_array)){
		$student_li[] = write_html('li', '',
			write_html('label', '', 
				'<input type="checkbox" name="fields[]" value="'.$main_db.'.student_data.'.$field.'" />'.
				$lang[$field]
			)
		);
	}
}

foreach($parent_fields as $field){
	if(!in_array($field, $hidden_array)){
		$parent_li[] = write_html('li', '',
			write_html('label', '', 
				'<input type="checkbox" name="fields[]" value="'.$main_db.'.parents.'.$field.'" />'.
				$lang[$field]
			)
		);
	}
}

$div_1 = write_html('div', 'class="item"',
	write_html('h3', '', $lang['show_std_infos']).
	write_html('fieldset', '',
		write_html('legend', 'onclick="$(\'#others_ul,#parent_ul\').slideUp();$(\'#student_ul\').slideDown()"  style="cursor:pointer;" ', 
			$lang['student_data'].
			write_html('span', 'style="float:right" class="ui-icon ui-icon-triangle-1-s"','')
		).
		write_html('label', '', '<input type="checkbox" value="1" name="serial" checked="checked" />'.$lang['show_serial']).
		write_html('ul', 'id="student_ul" ', implode('', $student_li))
	).
	write_html('fieldset', '',
		write_html('legend', 'onclick="$(\'#student_ul,#others_ul\').slideUp();$(\'#parent_ul\').slideDown()" style="cursor:pointer;"', 
			$lang['parents_infos'].
			write_html('span', 'style="float:right" class="ui-icon ui-icon-triangle-1-s"','')
		).
		write_html('ul', 'id="parent_ul" class="hidden"', implode('', $parent_li))
	).
	write_html('fieldset', '',
		write_html('legend', 'onclick="$(\'#student_ul,#parent_ul\').slideUp();$(\'#others_ul\').slideDown()" style="cursor:pointer;"', 
			$lang['other_infos'].
			write_html('span', 'style="float:right" class="ui-icon ui-icon-triangle-1-s"','')
		).
		write_html('ul', 'id="others_ul" class="hidden"', 
			write_html('li', '',
				write_html('label', '', 
					'<input type="checkbox" name="fields[]" value="'.DB_year.'.classes.name_'.$_SESSION['dirc'].' AS class_name" />'.
					$lang['class']
				)
			).
			write_html('li', '',
				write_html('label', '', 
					'<input type="checkbox" name="fields[]" value="'.DB_student.'.levels.name_'.$_SESSION['dirc'].' AS level_name" />'.
					$lang['level']
				)
			).
			write_html('li', '',
				write_html('label', '', 
					'<input type="checkbox" name="fields[]" value="'.DB_year.'.classes_std.new_stat AS new_stat" />'.
					$lang['redouble_stat']
				)
			).
			write_html('li', '',
				write_html('label', '', 
					'<input type="checkbox" name="extras[]" value="absents" />'.
					$lang['total_absent']
				)
			).
			write_html('li', '',
				write_html('label', '', 
					'<input type="checkbox" name="extras[]" value="brothers" />'.
					$lang['brothers']
				)
			).
			write_html('li', '',
				write_html('label', '', 
					'<input type="checkbox" name="extras[]" value="age" />'.
					$lang['age_in_first_oct']
				)
			).
			write_html('li', '',
				write_html('label', '', 
					'<input type="checkbox" name="extras[]" value="login" />'.
					$lang['login']
				)
			)
		)
	)
);

$div_2 = write_html('div', 'class="item"',
	write_html('h3', '', $lang['filter']).
	write_html('fieldset', '',
		'<input type="hidden" name="main_param" id="list_main_param" />'.
		'<input type="hidden" name="params" id="list_params" />'.
		write_html('label', '',
			'<input type="radio" name="setfilter" onclick="unsetFilters()" checked="checked" />'.
			$lang['all_student']
		).
		'<br />'.
		write_html('label', '',
			'<input type="radio" name="setfilter" onclick="setFilter()" />'.
			$lang['student_list_opts']
		)
	).
	write_html('div', 'id="params_elemnt" class="hidden"',
		write_html('select', 'id="fields_select"', implode('', $filter_array))
	).
	write_html('div', 'id="param_content" class="hidden"',
		write_html('div', 'class="toolbox"',
			write_html('a', 'onclick="insertParam()"', write_icon('plus').$lang['and']).
			write_html('a', 'onclick="clearParam()"', write_icon('trash').$lang['clear'])
		).        
		write_html('fieldset', 'id="param_div"','')
	)
);

$div_3 =  write_html('div', 'class="item"',
	write_html('h3', '', $lang['order']).
	write_html('fieldset', '',
		write_html('legend', '', $lang['principal_order']).
		write_html('table', ' border="0" cellspacing="0"',
			write_html('tr', '',
				write_html('td', 'width="120" valign="middel"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['order_by'])
				).
				write_html('td', '',
					write_html_select('id="order_by" name="order_1" class="combobox"', $order_by, '')
				)
			).
			write_html('tr', '',
				write_html('td', 'colspan="2"',
					write_html('label', '',
						'<input type="checkbox" name="grouped" value="1" />'.
						$lang['each_par_page']
					)
				)
			)
		)
	).
	write_html('fieldset', '',
		write_html('legend', '', $lang['secondary_order']).
		write_html('table', ' border="0" cellspacing="0"',
			write_html('tr', '',
				write_html('td', 'width="120" valign="middel"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['order_by'])
				).
				write_html('td', '',
					write_html_select('id="grouped_by" name="order_2" class="combobox"', $order_by, '')			
				)
			).
			write_html('tr', '',
				write_html('td', 'width="120" valign="middel"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['order_by'])
				).
				write_html('td', '',
					write_html_select('id="grouped_by" name="order_3" class="combobox"', $order_by, '')			
				)
			).
			write_html('tr', '',
				write_html('td', 'width="120" valign="middel"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['order_by'])
				).
				write_html('td', '',
					write_html_select('id="grouped_by" name="order_4" class="combobox"', $order_by, '')			
				)
			)	
		)
	)
);

echo write_html('form', 'id="list_content"', $div_1 .$div_2.$div_3 );
?>