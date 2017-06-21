<?php
## service Details
require_once('modules/services/services.class.php');
require_once('modules/lms/books.class.php');
require_once('modules/lms/chapters.class.php');
require_once('modules/lms/units.class.php');

require_once('scripts/lms_functions.php');

$service_id = $_GET['service_id'];

// tab to show
$tabs = array();
if(in_array($_SESSION['group'], array('parent', 'student'))){
	if(MSEXT_lms){
		$tabs[] = 'books';
		$tabs[] = 'homeworks';
		$tabs[] = 'timeline';
	}
	$tabs[] = 'notes';
	$tabs[] = 'documents';
} elseif(in_array($_SESSION['group'], array('prof', 'supervisor'))){
	if(MSEXT_lms){
		$tabs[] = 'books';
		$tabs[] = 'timeline';
		$tabs[] = 'planedTimeline';
		$tabs[] = 'homeworks';
	}
	$tabs[] = 'notes';
	$tabs[] = 'documents';
	$tabs[] = 'settings';
} else {
	if(MSEXT_lms){
		$tabs[] = 'books';
		$tabs[] = 'planedTimeline';
	}
	$tabs[] = 'documents';
	$tabs[] = 'settings';
}

$i = 0;
$titles = array();
$details_div = array();
foreach($tabs as $tab){
	switch ($tab) {
		case 'notes':
			if($i == 0) {require_once("services_details_notes.php");}
			$href = $i==0 ? '#first_details_tab' : 'index.php?module=lessons&notes_list&service_id='.$service_id;
			$titles[] = write_html('li', '', 
				write_html('a', 'href="'.$href.'"', $lang['notes'])
			);
			if($i==0){
				$details_div[] = write_html('div', 'id="first_details_tab"' , $out_div);
				$i++;
			}
		break;
		case 'books':
			$books_html = Books::bookListLayout($service_id);
			$href = $i==0 ? '#first_details_tab' : 'index.php?module=lms&books_list&service_id='.$service_id;
			$titles[] = write_html('li', '', 
				write_html('a', 'href="'.$href.'"', $lang['books'])
			);
			if($i==0){
				$details_div[] = write_html('div', 'id="first_details_tab"' , $books_html);
				$i++;
			}
		break;
		case 'documents': // default case if EXT_lms not used
			if($i == 0) {require_once("services_details_$tab.php");}
			$href = $i==0 ? '#first_details_tab' : 'index.php?module=documents&type=services&service_id='.$service_id;
			$titles[] = write_html('li', '', 
				write_html('a', 'href="'.$href.'"', $lang['documents'])
			);
			if($i==0){
				$details_div[] = write_html('div', 'id="first_details_tab"' , $out_div);
				$i++;
			}
		break;
		case 'homeworks':
			if($i == 0) {require_once("services_details_$tab.php");}
			$href = $i==0 ? '#first_details_tab' : 'index.php?module=lms&homeworks_list&service_id='.$service_id;
			$titles[] = write_html('li', '', 
				write_html('a', 'href="'.$href.'"', $lang['homeworks'])
			);
			if($i==0){
				$details_div[] = write_html('div', 'id="first_details_tab"' , $homeworks_div);
				$i++;
			}
		break;
		case 'timeline':
			if($i == 0) {require_once("services_details_$tab.php");}
			$href = $i==0 ? '#first_details_tab' : 'index.php?module=lms&timeline&service_id='.$service_id;
			$titles[] = write_html('li', '', 
				write_html('a', 'href="'.$href.'"', $lang['timeline'])
			);
			if($i==0){
				$details_div[] = write_html('div', 'id="first_details_tab"' , $out_div);
				$i++;
			}
		break;
		case 'planedTimeline':
			if($i == 0) {require_once("services_details_$tab.php");}
			$href = $i==0 ? '#first_details_tab' : 'index.php?module=lms&planedTimeline&service_id='.$service_id;
			$titles[] = write_html('li', '', 
				write_html('a', 'href="'.$href.'"', $lang['planedTimeline'])
			);
			if($i==0){
				$details_div[] = write_html('div', 'id="first_details_tab"' , $out_div);
				$i++;
			}
		break;
		case 'settings':
			if($i == 0) {require_once("services_details_$tab.php");}
			$href = $i==0 ? '#first_details_tab' : 'index.php?module=services&details=settings&service_id='.$service_id;
			$titles[] = write_html('li', '', 
				write_html('a', 'href="'.$href.'"', $lang['settings'])
			);
			if($i==0){
				$details_div[] = write_html('div', 'id="first_details_tab"' , $out_div);
				$i++;
			}
		break;
	}
}

$service_tab = write_html('div', 'class="tabs"', 
	write_html('ul', '', implode('', $titles)).
	implode('', $details_div)
);

//echo $service_tab;
?>
