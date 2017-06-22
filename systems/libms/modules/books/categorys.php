<?php
## LIBms categorys

//dialog mmode
$dialog_mode = isset($_GET['dialog']) ? true : false;

if(isset($_GET['newinsertedcat'])){
	$categorys = array( $_GET['newinsertedcat'] => getCategoryNameById($_GET['newinsertedcat']) ); 
	
} else {
	$categorys = createCatsList();
}
$accordion = '';
$thead = write_html('thead', '',
	write_html('tr', '',
		write_html('th', 'width="16" style="background-image:none"', '&nbsp;').
		write_html('th', 'width="16" style="background-image:none"', '&nbsp;').
		write_html('th', '', $lang['code']).
		write_html('th', '', $lang['sub_cat']).
		write_html('th', 'width="60"', $lang['total']).
		write_html('th', 'width="60"', $lang['lost']).
		write_html('th', 'width="60"', $lang['bad']).
		write_html('th', 'width="60"', $lang['out']).
		write_html('th', 'width="60"', $lang['avaible'])
	)
);

foreach($categorys  as $cat_id => $cat_name){
	$cat_toobar = write_html('div', 'class="toolbox"',
		write_html('button', 'onclick="renameCat('.$cat_id.')" title="'.$lang['rename_cat'].'"', write_icon('pencil')).
		write_html('button', 'onclick="deleteCat('.$cat_id.')" title="'.$lang['delete_cat'].'"', write_icon('close')).
		write_html('button', 'onclick="newCategorysSub('.$cat_id.')" title="'.$lang['new_cat_sub'].'"', write_icon('plus')).
		write_html('button', 'onclick="" title="'.$lang['print'].'"', write_icon('print'))  
	);
	$subs = createCatsSubsList($cat_id);
	$tbody = '';
	if($subs != false){
		foreach($subs  as $sub_id => $sub_name){
			$sub_total = getTotalBooks('cat_sub', $sub_id);
			$sub_lost = getLostBooks('cat_sub', $sub_id);
			$sub_bad = getBadBooks('cat_sub', $sub_id);
			$sub_out = getOutBooks('cat_sub', $sub_id);
			$sub_code = getSubCode( $sub_id);
			$sub_avaible = $sub_total - $sub_out - $sub_lost;
			$tbody .= write_html('tr', 'id="sub_tr_'.$sub_id.'"',
				write_html('td', '',
					write_html('a', 'title="'.$lang['rename'].'" onclick="renameSub('.$sub_id.',\''.$sub_code.'\',\''.$sub_name.'\')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;"', 
						write_icon('pencil')
					)
				).
				write_html('td', '',
					write_html('a', 'title="'.$lang['delete'].'" onclick="deleteSub('.$sub_id.')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;"', 
						write_icon('close')
					)
				).
				write_html('td', 'class="code_td" width="80"', $sub_code).
				write_html('td', 'class="name_td"', $sub_name).
				write_html('td', '', $sub_total).
				write_html('td', '', $sub_lost).
				write_html('td', '', $sub_bad).
				write_html('td', '', $sub_out).
				write_html('td', '', $sub_avaible)
			);
		}
	}
	$accordion .= write_html('h3', 'id="cat_name_'.$cat_id.'"', $cat_name).
	write_html('div', 'id="cat_div_'.$cat_id.'"',
		$cat_toobar.
		write_html('table', 'id="cat_table_'.$cat_id.'" class="tablesorter"',
			$thead.
			write_html('tbody' ,'', $tbody)
		)
	);
}

/********** body */////////////////
if(isset($_GET['newinsertedcat'])){
	$categorys_html = $accordion;
} else {
	$categorys_html = ( !$dialog_mode ? 
		write_html('div', 'class="ui-corner-top ui-widget-header reverse_align"',
			write_html('h3', 'class="title_wihte"', $lang['borrow'])
		) : ''
	).
	write_html('div', 'class="ui-corner-bottom ui-widget-content module_content"',
		write_html('div', 'class="toolbox"',
			write_html('a', 'title="'.$lang['new_cat'].'" onclick="newCategorysCat()"', 
				$lang['new_cat'].
				write_icon('plus')
			)
		).
		write_html('div', 'id="category_div" class="accordion"', 
			$accordion
		)
	);
}

?>