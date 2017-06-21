<?php
## Generate Groups
$error= false;
	// Geneare groups by service oprional
if( isset($_POST['generate_optional_groups'])){
	if(generatOptionalGroup( DB_year) == false){
		$error = "Error generating optional groups";
	}
}
	// Create Religion groups
if( isset($_POST['generate_religion_groups'])){
	if(newYear::generatReligionGroup( DB_year,  array($_POST['ser_muslim'],$_POST['ser_christian']) , $_POST['level_id'])){
		$error = "Error generating Religions groups";
	};
}

if($error != false){
	$answer['error'] = $error;
} else {
	$answer['error'] = '';
}
setJsonHeader();
print json_encode($answer);
exit;

?>