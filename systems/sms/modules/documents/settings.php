<?php
##settings_documents

if(MS_codeName !='sms_basic'){
	// Default body
	$doc_size_array = array (
		 0 => 'disable',
		 -1=> 'unlimited',
		 50 => $lang['50_mb'],
		 100 => $lang['100_mb'],
		 200 => $lang['200_mb'],
		 300 => $lang['300_mb'],
		 500 => $lang['500_mb'],
		 1024 => $lang['1_gb'],
		 1536 => $lang['1.5_gb'],
		 2048 => $lang['2_gb'],
	);
	 
	$img_sizes = array (
		360 => '360 x 240',
		600 => '800 x 600',
		768 => '1024 x 768',
		1080 => '1440 x 1080'
	);
	
	$video_sizes = array (
		240 => '360 x 240',
		480 => '600 x 480',
		600 => '800 X 600',
		768 => '1024 x 768',
		1080 => '1440 x 1080'
	);
	
	$img_type  = array (
		'jpeg' => 'Jpeg',
		'png' => 'Png',
		'gif' => 'Gif'
	);
	
	$img_qualitys  = array (
		'' => $lang['same'],
		'0.2' => $lang['small'],
		'0.5' => $lang['medium'],
		'1' => $lang['large']
	);
	
	
	$lib_tbody = '';
	$libs = do_query_resource("SELECT * FROM files_librarys", LMS_Database);
	while( $lib = mysql_fetch_assoc($libs)){
		$lib_tbody .= write_html('tr', '',
			write_html('td', '', '<a class="hand ui-icon ui-icon-close" onclick="deletelib(this, '.$lib['id'].')"></span>').
			write_html('td', '', '<a class="hand ui-icon ui-icon-extlink" onclick="openlib('.$lib['id'].')"></span>').
			write_html('td', '', $lib['title']).
			write_html('td', '', $lib['path']).
			write_html('td', '', $lib['size'])
		);
	}
	
	$enable = $MS_settings['docs'] =='1' ? true : false;
	$module_settings = write_html('div', 'id="document_setting_div"', 
		write_html('fieldset', '',
			write_html('legend', '', $lang['users_docs']).
			write_html('label', '', 
				'<input type="radio" name="docs" value="1" onclick="enableDocsSetting()"'.($MS_settings['docs'] =='1' ?'checked="checked"' : '').'/>'.
				$lang['enable_docs']
			).
			write_html('label', '', 
				'<input type="radio" name="docs" value="0" onclick="disableDocsSetting()"'.($MS_settings['docs'] =='0' ?'checked="checked"' : '').'/>'.
				$lang['disable_docs']
			)
		).
		write_html('table', 'id="docs_table" class="result"',
			write_html('tr', '',
				write_html('td', '', $lang['enable_std_docs']).
				write_html('td', '',
					write_html_select('name="docs_std" class="combobox" id="docs_std"', $doc_size_array, $MS_settings['docs_std'])
				).
				write_html('td', '',
					'<input type="text" name="docs_root_stds" value="'.$MS_settings['docs_root_stds'].'" '.(!$enable ? 'class="disabled"':'').' title="'.$lang['leave_blank_for_default'].'" />'
				)
			).
			write_html('tr', '',
				write_html('td', '', $lang['enable_prof_docs']).
				write_html('td', '',
					write_html_select('name="docs_prof" class="combobox" id="docs_prof"', $doc_size_array, $MS_settings['docs_prof'])
				).
				write_html('td', '',
					'<input type="text" name="docs_root_profs" value="'.$MS_settings['docs_root_profs'].'" '.(!$enable ? 'class="disabled"':'').' title="'.$lang['leave_blank_for_default'].'" />'
				)
			).
			write_html('tr', '',
				write_html('td', '', $lang['enable_users_docs']).
				write_html('td', '',
					write_html_select('name="docs_user" class="combobox" id="docs_user"', $doc_size_array, $MS_settings['docs_user'])
				).
				write_html('td', '',
					'<input type="text" name="docs_root_users" value="'.$MS_settings['docs_root_users'].'" '.(!$enable ? 'class="disabled"':'').' title="'.$lang['leave_blank_for_default'].'" />'
				)
			)
		).
		write_html('fieldset', '',
			write_html('legend', '', $lang['media_compressions']).
			write_html('table', '', 
				write_html('tr', '',
					write_html('td', 'width="24"', '<input type="checkbox" value="1" name="autoconvert_video" '.($MS_settings['autoconvert_video']==1 ? 'checked="checked"' : '').' />').
					write_html('td', 'colspan="2"', $lang['autoconvert_video'])
				).
				write_html('tr', '',
					write_html('td', 'width="24"', '&nbsp;').
					write_html('td', '', $lang['video_size']).
					write_html('td', '',
						write_html_select('name="conv_video_size" class="combobox" id="conv_video_size"',$video_sizes, $MS_settings['conv_video_size']).' px'
					)
				).
				write_html('tr', '',
					write_html('td', 'width="24"', '<input type="checkbox" name="autoconvert_image" value="1" '.($MS_settings['autoconvert_image']==1 ? 'checked="checked"' : '').' />').
					write_html('td', 'colspan="2"', $lang['autoconvert_image'])
				).
				write_html('tr', '',
					write_html('td', 'width="24"', '&nbsp;').
					write_html('td', '', $lang['img_height']).
					write_html('td', '',
						write_html_select('name="conv_img_hight_size" class="combobox" id="conv_img_hight_size"',$img_sizes, $MS_settings['conv_img_hight_size']).' px'
					)
				).
				write_html('tr', '',
					write_html('td', 'width="24"', '&nbsp;').
					write_html('td', '', $lang['type']).
					write_html('td', '',
						write_html_select('name="conv_img_type" class="combobox" id="conv_img_type"',$img_type, $MS_settings['conv_img_type'])
					)
				).
				write_html('tr', '',
					write_html('td', 'width="24"', '&nbsp;').
					write_html('td', '', $lang['img_quality']).
					write_html('td', '',
						write_html_select('name="conv_img_quality" class="combobox" id="conv_img_quality"',$img_qualitys, $MS_settings['conv_img_quality'])
					)
				)
			)
		).
		
		write_html('fieldset', '',
			write_html('legend', '', $lang['thumbnails']).
			write_html('label', '',
				$lang['activate_real_thumb'].
				write_html('label', '', '<input type="radio" name="real_thumb" value="1" '.($MS_settings['real_thumb']==1 ? 'checked="checked"' : '').' />'. $lang['on']).
				write_html('label', '', '<input type="radio" name="real_thumb" value="0" '.($MS_settings['real_thumb']==0 ? 'checked="checked"' : '').' />'. $lang['off'])
			)
		).
		
		write_html('fieldset', '',
			write_html('legend', '', $lang['materials']).
			write_html('label', '',
				$lang['service_doc_size'].
				'<input type="text" name="docs_services_max" value="'.$MS_settings['docs_services_max'].'" />'
			)
		).
	
		write_html('fieldset', '',
			write_html('legend', '', $lang['max_upload_filesize']).
			write_html('label', '',
				$lang['max_upload_filesize'].
				'<input type="text" name="upload_max_filesize" value="'.$MS_settings['upload_max_filesize'].'" />'
			)
		).
		write_html('fieldset', '',
			write_html('legend', '', $lang['allowed_file_type']).
			write_html('label', '',
				$lang['all'].
				'<input type="radio" name="docs_allowed" value="1" />'
			).'<br />'.
			write_html('label', '',
				$lang['filter'].
				'<input type="radio" name="docs_allowed" value="0" />'.
				write_html('textarea', 'name="docs_filter"', $MS_settings['docs_filter'])
			)
		)
	
	);
} else {
	$module_settings = write_error('You must Upgrade to SMS School life to enable this module.');	
}