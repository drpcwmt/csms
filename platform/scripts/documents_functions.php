<?php
// sql
function getUserRootPath(){
	global $MS_settings;
	if($_SESSION['group'] == 'student'){
		$pre_path = 'attachs/'.$MS_settings['docs_root_stds'] ;
	} elseif($_SESSION['group'] == 'prof'){
		$pre_path = 'attachs/'.$MS_settings['docs_root_profs'] ;
	} else {
		$pre_path = 'attachs/'.$MS_settings['docs_root_users'] ;
	}	
	$na = do_query( "SELECT name FROM users WHERE user_id=".$_SESSION['user_id'] ." AND `group`='".$_SESSION['group']."'", MySql_Database);
	if($na['name'] != ''){
		return $pre_path. '/'. $na['name'];
	} else {
		return false;
	}
}

function getLinkFromPath($path){
	$file = do_query("SELECT link FROM files WHERE path='$path'", LMS_Database);
	if($file['link'] != ''){
		return $file['link'];
	} else {
		return false;
	}
}

function getPathFromLink($link){
	$file = do_query("SELECT path FROM files WHERE link='$link'", LMS_Database);
	if($file['path'] != ''){
		return $file['path'];
	} else {
		return false;
	}
}

function brows_dir($dir, $sql=true, $share=false){ // create table from directory scan
	global $lang;
	$list = scandir($dir); 
	natsort($list);
	$num = count($list);
	$folders = array();
	$files = array();
	$trs = array();
	$filter = array(".", "..", ".htaccess", ".htpasswd","Thumbs.db","folder.jpg","folder.png","folder.gif","folder.bmp","Detsktop.ini","detsktop.ini","thumb.db","thumb", "_notes", "", " ");
	for($i = 0; $i < $num; $i++ ){
		if(!in_array($list[$i], $filter)) {
			$file_name = $list[$i];
			$path = $dir .'/'. $list[$i];
			if(is_dir($path)){
				$folders[] =$file_name;
			} else {
				$files[] = $file_name;
			}
		}
	}
	
	$head = '<thead>
		<tr>
			<th width="16" style="background-image:none">&nbsp;</th>
			<th width="16" style="background-image:none">&nbsp;</th>
			<th width="16" style="background-image:none">&nbsp;</th>'.
			($share ? '<th width="16" style="background-image:none">&nbsp;</th>' :'').
			'<th>'.$lang['file_name'].'</th>
			<th width="160">'.$lang['date'].'</th>
			<th width="50">'.$lang['file_size'].'</th>
		</tr>
	</thead>';
		
	for($i=0; $i<count($folders); $i++){
		$folder_name = $folders[$i];
		$path = $dir .'/'. $folder_name;
		$trs[] =  write_html('tr', '',
			write_html('td', '', '<input type="checkbox" name="folder[]" value="'.urlencode(getUtf8Path($path)).'" />').
			write_html('td', '', 
				write_html('a', 'class="hand" onclick="browseDir(\''.urlencode(getUtf8Path($path)).'\')" title="'.$lang['open'].'"', 
					write_icon('extlink')
				)
			).
			write_html('td', '', 
				write_html('a', 'class="hand" onclick="downloadFiles()" title="'.$lang['download'].'"', 
					write_icon('circle-arrow-s')
				)
			).
			($share ? 	write_html('td', '', '&nbsp;') : ''	).
			write_html('td', '', 
				'<img src="'.get_icon($path).'" border="0" height="24" width="24" /> '.
				write_html('a', 'href="#" class="file_name" onclick="browseDir(\''.urlencode(getUtf8Path($path)).'\')"', getUtf8Path($folder_name))
			).
			write_html('td', '',
				strftime ("%d.%b %I:%M %p", filemtime($path.'/.'))
			).			
			write_html('td', 'style="font-size:8px"',
				get_size(dirSize($path))
			)
		);				
	}

	for($i = 0; $i < count($files); $i++ ){
		$file_name = $files[$i];
		$path = $dir .'/'. $files[$i];
		if($sql != false){
			$link = getLinkFromPath(getUtf8Path($path));
			if(!$link){
				$link = uniqid();
				if(!do_query_edit("INSERT INTO files (path, link, owner_group, owner_id) VALUES ('".getUtf8Path($path)."', '$link', '".$_SESSION['group']."', '".$_SESSION['user_id']."')", LMS_Database)){
					$link = false;
				}
			}
		}
		$is_shared  =  $share ? testFoundRecords("SELECT link FROM files_share WHERE link='$link'", LMS_Database): false;
				
		$trs[] =  write_html('tr', 'class="file_item"',
			write_html('td', '', '<input type="checkbox"  name="file[]" value="'.$link.'" />').
			write_html('td', '', 
				write_html('a', 'class="hand" onclick="openFile(\''.$link.'\')" title="'.$lang['open'].'"', 
					'<span class="ui-icon ui-icon-extlink"></span>'
				)
			).
			write_html('td', '', 
				write_html('a', 'class="hand" onclick="downloadFiles()" title="'.$lang['download'].'"', 
					'<span class="ui-icon ui-icon-circle-arrow-s"></span>'
				)
			).
			($share ? 
				write_html('td', '', 
					write_html('a', 'class="hand" onclick="shareFile(\''.$link.'\')" title="'.($is_shared ? $lang['shared'] : $lang['share']).'"', 
						'<span class="ui-icon ui-icon-transferthick-e-w"></span>'
					)
				)
				: ''
			).
			write_html('td', '', 
				'<img src="'.get_icon($path).'" class="file_item_thumb" border="0" height="24" width="24" /> '.
				write_html('a', 'target="blank" class="file_name file_item_name" href="'.getUtf8Path($path).'"', getUtf8Path($file_name))
			).
			write_html('td', '',
				strftime("%d.%b %I:%M %p", filemtime($path))
			).
			write_html('td', 'style="font-size:9px"',
				get_size(filesize($path))
			)
		);
	}

	return write_html('table', 'class="tablesorter"', 
		$head.
		write_html('tbody', '', implode('', $trs))
	);
}

function buildFileTableFromArray($links){
	global $lang;
	include_once('scripts/hrms_functions.php');
	$head = '<thead>
		<tr>
			<th width="16" style="background-image:none">&nbsp;</th>
			<th width="16" style="background-image:none">&nbsp;</th>
			<th width="16" style="background-image:none">&nbsp;</th>
			<th>'.$lang['file_name'].'</th>
			<th>'.$lang['owner'].'</th>
			<th width="160">'.$lang['date'].'</th>
			<th width="50">'.$lang['file_size'].'</th>
		</tr>
	</thead>';
	
	$folders = array();
	$files = array();
	$trs =array();
	foreach($links as $link){
		$path = getSystemPath(getPathFromLink($link));
		if(is_dir($path)){
			$folders[$link] =$path;
		} else {
			$files[$link] = $path;
		}
	}
		
	foreach($folders as $link => $path){
		$folder_name = getFilenameFromStr($path);
		$trs[] =  write_html('tr', '',
			write_html('td', '', '<input type="checkbox" name="folder[]" value="'.urlencode(getUtf8Path($path)).'" />').
			write_html('td', '', 
				write_html('a', 'class="hand" onclick="browseDir(\''.urlencode(getUtf8Path($path)).'\')" title="'.$lang['open'].'"', 
					write_icon('extlink')
				)
			).
			write_html('td', '', 
				write_html('a', 'class="hand" onclick="downloadFiles()" title="'.$lang['download'].'"', 
					write_icon('circle-arrow-s')
				)
			).
			write_html('td', '', 
				'<img src="'.get_icon($path).'" class="file_item_thumb" border="0" height="24" width="24" /> '.
				write_html('a', 'target="blank" class="file_name file_item_name hand" onclick="browseDir(\''.urlencode(getUtf8Path($path)).'\')"', getUtf8Path($folder_name))
			).
			write_html('td', '', 
				($owner_group == 'student' ? getStudentNameById($owner['owner_id']) : getEmployerNameById($owner['owner_id']))
			).
			write_html('td', '', 
				date ("d F Y H:i:s", filemtime($path.'/.'))
			).
			write_html('td', 'style="font-size:8px"',
				get_size(dirSize($path))
			)
		);				
	}

	foreach($files as $link => $path){
		$owner = do_query("SELECT owner_group, owner_id FROM files WHERE link='$link'", LMS_Database);
		$file_name = getFilenameFromStr($path);				
		$trs[] =  write_html('tr', 'class="file_item"',
			write_html('td', '', '<input type="checkbox"  name="file[]" value="'.$link.'" />').
			write_html('td', '', 
				write_html('a', 'class="hand" onclick="openFile(\''.$link.'\')" title="'.$lang['open'].'"', 
					'<span class="ui-icon ui-icon-extlink"></span>'
				)
			).
			write_html('td', '', 
				write_html('a', 'class="hand" onclick="downloadFiles()" title="'.$lang['download'].'"', 
					'<span class="ui-icon ui-icon-circle-arrow-s"></span>'
				)
			).
			write_html('td', '', 
				'<img src="'.get_icon($path).'" class="file_item_thumb" border="0" height="24" width="24" /> '.
				write_html('a', 'target="blank" class="file_name file_item_name" href="'.getUtf8Path($path).'"', getUtf8Path($file_name))
			).
			write_html('td', '', 
				($owner['owner_group'] == 'student' ? getStudentNameById($owner['owner_id']) : getEmployerNameById($owner['owner_id']))
			).
			write_html('td', '',
				date ("d F Y H:i:s", filemtime($path))
			).
			write_html('td', 'style="font-size:9px"',
				get_size(filesize($path))
			)
		);
	}

	return write_html('table', 'class="tablesorter"', 
		$head.
		write_html('tbody', '', implode('', $trs))
	);
}

//build explorer tabl efrom links array
function buildAttachTable($file_arr, $editable=false){
	global $lang;
	$out =  '';
	foreach($file_arr as $link){
		$file_s = do_query("SELECT path FROM files WHERE link='$link'", LMS_Database);
		$path = $file_s['path'];
		$size = '';//@filesize(getSystemPath($path));
		$f = explode('/', $path);
		$file_name = $f[count($f)-1];
		$extention = substr($file_name, strrpos($file_name, ".")+1);
		$out .=  write_html('tr', '',
			($editable ? 
				write_html('td', 'width="20"',
					 write_html('a', 'href="#" class="ui-corner-all ui-state-default hand hoverable" title="'.$lang['remove'].'" action="dettachFile" module="documents" link="'.$link.'" style="float:left"', write_icon('close'))
					 )
			: '').
			write_html('td', 'width="20"',
				'<img src="'.get_icon($path).'" width="24" />'
			).
			write_html('td', '',
				write_html('a', 'class="hand" href="'.$path.'" target="_blank" onclick="openFile(\''.$link.'\')"', $file_name)
			)
		);
	}
	return $out;
}

function loadAttachList($con, $con_id, $editable=false){
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
		$attach_div = buildAttachTable($file_arr, $editable);
	} else {
		$attach_div = '';
	}
	return $attach_div;
}