<?php
## services deatils books
require_once('scripts/services_functions.php');

if(isset($_GET['service_id'])){
	$service_id = safeGet($_GET['service_id']);	
}

$editable =check_user_lesson_privilege(false, $service_id);

$books = getServiceBooks($service_id);
$books_list = '';
foreach($books as $book_id => $book_name){
	$books_list .= write_html('h3', 'bookid="'.$book_id.'"', 
		write_html('text', 'class="holder-book-'.$book_id.'"', $book_name).
		($editable ?
			write_html('a', 'action="editBook" bookid="'.$book_id.'" bookname="'.$book_name.'" class="rev_float ui-state-default ui-corner-all hoverable"', write_icon('pencil'))
		: '')
	);
	
	$chapters = getBookChapers($book_id);
	$chapter_list = '<ul style="list-style:none; padding:0; margin:0">';
	foreach($chapters as $chapter_id => $chapter_name){
		$chapter_list .=write_html('li', 'style="display:block;padding:5px;" class="hand ui-state-default hoverable clickable ui-corner-all" chapterid="'.$chapter_id.'" serviceid="'.$service_id.'" action="displaySummaryList" title="'.$chapter_name.'"',
			write_html('span', 'style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"',
				write_html('text', 'class="holder-chapter-'.$chapter_id.'"', $chapter_name)
			).
			($editable ?
				write_html('a', 'action="editChapter" chapterid="'.$chapter_id.'" chaptername="'.$chapter_name.'" bookid="'.$book_id.'" serviceid="'.$service_id.'" class="rev_float ui-state-default ui-corner-all hoverable"', write_icon('pencil'))
			: '')
		);
	}
	$chapter_list .= '</ul>';
	
	$books_list .= write_html('div', 'style="padding:0px"',
		write_html('div', 'class="chapter_list" id="book-'.$book_id.'-chapters" style="padding:7px"', $chapter_list).
		($editable ?
			write_html('a', 'class="hoverable hand ui-accordion-header ui-helper-reset ui-state-default ui-accordion-icons ui-corner-all" style="padding:5px; margin:-5px 7px 5px; display:block;" action="editChapter" serviceid="'.$service_id.'" bookid="'.$book_id.'"',
				write_html('span', 'class="ui-icon ui-icon-plus" style="float:left"', '').
				$lang['new_chapter'].'...'
			)
		: '')
	);
}

$new_book_but = ($editable ?
	write_html('a', 'class="hoverable hand ui-accordion-header ui-helper-reset ui-state-default ui-accordion-icons ui-corner-all" style="padding: 5px; margin:3px 0px; display: block;" action="editBook" serviceid="'.$service_id.'"',
		write_html('span', 'class="ui-icon ui-icon-plus" style="float:left"', '').
		$lang['new_book'].'...'
	)
: '');

$books['service_id'] = $service_id;
$books['books_list'] = $books_list;
$books['new_book_but'] = $new_book_but;

$books_toolbox = array(array(
	"tag" => "a",
	"attr"=> 'action="openSummary" serviceid="'.$service_id.'"',
	"text"=> $lang['new'],
	"icon"=> "document"
));

$books['toolbox'] = createToolbox($books_toolbox);

$books_html = fillTemplate("$thisTemplatePath/books.tpl", $books);

?>