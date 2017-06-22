<?php
setJsonHeader();
$num = 0; 
$value = $_GET['term'];
$arr = array();
$chapter = isset($_GET['chapter_id']) && $_GET['chapter_id'] != '' ? ' AND chapter_id='.$_GET['chapter_id'] : ''; 
$book = isset($_GET['book_id']) && $_GET['book_id'] != '' ? ' AND book_id='.$_GET['book_id'] : ''; 
$service = isset($_GET['service_id']) && $_GET['service_id'] != '' ? ' AND service_id='.$_GET['service_id'] : ''; 

$sql = "SELECT * FROM summarys WHERE (
	title LIKE '$value%'
	OR title LIKE '$value' 
	OR title LIKE'".strtolower($value)."%' 
	OR title LIKE '".ucfirst($value)."%'
)". $chapter . $book . $service;

//echo $sql;
$query = do_query_resource( $sql, LMS_Database);
while($summary = mysql_fetch_assoc($query)){
	$sum_id = $summary['id'];
	
	$attach_arr = array();
	$sum_attach = do_query_resource("SELECT link FROM summarys_attachs WHERE summary_id=$sum_id", LMS_Database);
	while($at = mysql_fetch_assoc($sum_attach)){
		$attach_arr[] = $at['link'];
	}
	
	$arr[] = array(
		"id"=>$sum_id,
		"title"=>$summary['title'],
		"summary"=>$summary['summary'],
		"attachs" =>implode(',', $attach_arr)
	);
}

if(count($arr) > 0){	
	echo json_encode($arr);
} 

?>