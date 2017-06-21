<?php
## xml parser
	error_reporting(0);
	ini_set("display_errors", 0);

//echo $_POST['data'];
if($_POST['data'] == ''){
	die('Error: No data to export');
} else {
	$table = $_POST['data'];
}

function arrayToXls($input) {
	// BoF
	$ret = pack('ssssss', 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
 
 // array_values is used to ensure that the array is numerically indexed
	$lineNumber = 0;
	foreach ($input->row as $cell) {
		$cell = $input->row[$lineNumber];
		$cellNo=0;
		foreach($cell as $data){
			$encoding = mb_detect_encoding($data);
			$data = iconv( $encoding, "CP1256//TRANSLIT",$data);
		//	echo $data;
			if (is_numeric($data)) {
				// number, store as such
				$ret .= pack('sssssd', 0x203, 14, $lineNumber, $cellNo, 0x0, $data);
			} elseif(count($data) >0) {
				// everything else store as string
				$len = strlen($data);
				$ret .= pack('ssssss', 0x204, 8 + $len, $lineNumber, $cellNo, 0x0, $len) . $data;
			}
			$cellNo++;
		}
		$lineNumber++;
	}
 
	//EoF
	$ret .= pack('ss', 0x0A, 0x00);
 
	return $ret;
}

function arrayToCSV($table) {
	$lineNumber = 0;
	foreach ($input->row as $cell) {
		$cell = $input->row[$lineNumber];
		$cellNo=0;
		foreach($cell as $data){
			$encoding = mb_detect_encoding($data);
			$data = iconv( $encoding, "CP1256//TRANSLIT",$data);
		//	echo $data;
			if (is_numeric($data)) {
				// number, store as such
				$ret .= pack('sssssd', 0x203, 14, $lineNumber, $cellNo, 0x0, $data);
			} else {
				// everything else store as string
				$len = strlen($data);
				$ret .= pack('ssssss', 0x204, 8 + $len, $lineNumber, $cellNo, 0x0, $len) . $data;
			}
			$cellNo++;
		}
		$lineNumber++;
	}
}

header('Content-disposition: attachment; filename="new_file.'.$_POST['type'].'"');
header("Pragma: no-cache");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); 
header('Content-Encoding: charset=utf-8');


if( $_POST['type'] == 'xml'){
	header('Content-type: "text/xml"');	
	echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'.$_POST['data'];
	
} elseif( $_POST['type'] == 'csv'){
	header( "Content-Type: text/csv" );
	
	$fp= fopen('php://output', 'w');
	$xml = simplexml_load_string($_POST['data'], "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	
	foreach($array as $line){
		 foreach ($line as $fields){
			fputcsv($fp, $fields['cell']);
		 }
	}
	fclose($fp);

//	header("Content-Type: application/vnd.ms-excel; charset=Windows-1252");
//	header("Content-type: application/x-msexcel; charset=Windows-1252");
//	$xml = simplexml_load_string( $table);
//	echo arrayToXls($xml);
//	echo $table;
}
?>