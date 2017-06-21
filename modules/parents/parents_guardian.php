<?php
## SMS
## Guardian
function build_guardian_div($id){
	include('lang/'.$_SESSION['lang'].'.php');
	$form_name = $id != '' ? "guardian-$id" : "new_grad_form";
	$seek_guard = false;
	if($id != ''){
		$guardian = do_query("SELECT * FROM guardians WHERE id=$id", MySql_Database);
		if($guardian['id'] != ''){
			$seek_guard = true;
		}
	}

	$out = write_html('form', 'id="'.$form_name.'" class="ui-widget-content ui-corner-all" style="margin:5px; padding:5px"',
		($id != '' ?
			write_html('div', 'class="toolbox"',
				write_html('a', 'onclick="updateGuardian()"', write_icon('disk').$lang['save'])
			)
		: ''). 
		write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
			write_html('tr', '',
				write_html('td', 'width="120" valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left ui-corner-left"', $lang['name'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_name" type="text" id="resp_name" class="input_double" value="'.($seek_guard ? $guardian['resp_name'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['resp_degree'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_degree" type="text" id="resp_degree" value="'.($seek_guard ? $guardian['resp_degree'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['tel'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_tel" type="text" id="resp_tel" value="'.($seek_guard ? $guardian['resp_tel'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['mobil'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_mobil" type="text" id="resp_mobil" value="'.($seek_guard ? $guardian['resp_mobil'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['mail'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_mail" type="text" id="resp_mail" value="'.($seek_guard ? $guardian['resp_mail'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['language'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_lang" type="text" id="resp_lang" value="'.($seek_guard ? $guardian['resp_lang'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['address'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_address" type="text" id="resp_address" class="input_double" value="'.($seek_guard ? $guardian['resp_address'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['city'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_city" type="text" id="resp_city" value="'.($seek_guard ? $guardian['resp_city'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['zip'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_zip" type="text" id="resp_zip" value="'.($seek_guard ? $guardian['resp_zip'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['country'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_country" type="text" id="resp_country" value="'.($seek_guard ? $guardian['resp_country'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['job'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_job" type="text" id="resp_job" value="'.($seek_guard ? $guardian['resp_job'] : '').'" /> '
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['job_address'])
				).
				write_html('td', 'valign="middel"', 
					'<input name="resp_job_address" type="text" id="resp_job_address" class="input_double" value="'.($seek_guard ? $guardian['resp_job_address'] : '').'" /> '
				)
			)
		)
	);

	return $out;
}

if(isset($_GET['guard_id'])){
	$guardian_html = build_guardian_div($_GET['guard_id']);
}elseif(isset($_GET['new_guard'])){
	$guardian_html = build_guardian_div('');
} else {
	$std = do_query("SELECT guardians FROM student_data WHERE id=".$_GET['std_id'], MySql_Database);
	if($std['guardians'] != ''){
		if(strpos($std['guardians'], ',') !== false){
			$guardians = explode(',', $std['guardians']);
		} else {
			$guardians = array($std['guardians']);
		}
	}
	
	$guardian_divs = '';
	if(isset($guardians) && count($guardians) > 0) { 
		foreach($guardians as $guard){
			$guardian_divs .= build_guardian_div($guard);
		}
	}
	$guardian_html = write_html('div', '',
		write_html('div', 'class="toolbox"',
				write_html('a', 'onclick="newGuardian()"', write_icon('plus').$lang['new'])
		). 
		write_html('div', 'id="guardians_div"',
			$guardian_divs
		)
	);
}
?>