<?php 
## Upload frorm 
$destination = isset($_GET['dest']) ? $_GET['dest'] : Ms_myDocs;
$filename = isset($_GET['filename']) ? $_GET['filename'] : false;
$overwrite = isset($_GET['overwrite']) ? true : false;
$multiple = !isset($_GET['multi']) || $_GET['multi'] == 'true' ? true : false;


$upload_form = write_html('form', 'action="" method="post" enctype="multipart/form-data" name="upload_form" id="upload_form"',
	write_html('div', 'class="ui-corner-all ui-state-highlight" style="padding:5px"', $lang['max_size_upload'].': '.formatSize(max_size_upload)).
	write_html('div', 'class="toolbox"',
		write_html('a', 'onclick="$(\'#upload_field\').click()"', 
			write_icon('plus').
			$lang['select']
		)
	).
	write_html('labe', '', 
		'<input type="checkbox" value="1" id="autoconvert" checked="checked" />'.
		$lang['autoconvert']
	).
	'<input type="file" name="files[]" '.($multiple ? 'multiple="multiple"' : '').' class="hidden" id="upload_field" />'.
	'<input type="hidden" value="'.$destination.'" id="destination" />'.
	($filename ? '<input type="hidden" value="'.$filename.'" id="filename" />' : '').
	($overwrite ? '<input type="hidden" value="1" id="overwrite" />' : '').
	write_html('table', 'id="upload_table" class="result" width="100%" border="0" cellspacing="1" cellpadding="2"', '')
);
?>