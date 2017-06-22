<?php
## marks charts
require_once('modules/absents/absents.class.php');
require_once('modules/services/services.class.php');
require_once('modules/marks/terms.class.php');
require_once('modules/marks/marks.class.php');
require_once('modules/marks/exams.class.php');
require_once('modules/marks/gradding.class.php');
require_once('modules/students/students.class.php');
require_once('modules/students/history.class.php');

include("scripts/chart/class/pData.class.php");
include("scripts/chart/class/pDraw.class.php");
include("scripts/chart/class/pImage.class.php");

$MyData = new pData();  
$student = new history($std_id);

$limits = 5;
$count_years = 0;
$years = $student->getYearList();
$absents = new absents('student', $std_id); 

$con_name = $student->getName();

if(isset($_GET['mat_id'])  && $_GET['mat_id'] != ''  && $_GET['mat_id'] != 0){
	$mat_id = $_GET['mat_id'];
	require_once('modules/resources/materials.class.php');
	$material = new Materials($mat_id);
	$chart_title = $material->getName();
} else {
	$mat_id = false;
	$chart_title = $lang['total'];
}

$cur_year = $_SESSION['year'];
$lastyear = $years[0]->year;

foreach($years as $year){
	$_SESSION['year'] = $year->year;
	$database = Db_prefix.$year->year;
	// chekc if student exists id this year
	$student_class = do_query_obj("SELECT class_id FROM classes_std WHERE std_id=$std_id", $database);

	// year
	$MyData->addPoints($_SESSION['year'], 'Year');	

	if(isset($student_class->class_id)){
		// level 
		$student = new Students($con_id);
		$level_id = $student->getLevel()->id;
		
		// total 
		if($mat_id != false){
			$cur_service = do_query_obj("SELECT * FROM services WHERE mat_id=$mat_id AND level_id = $level_id", $database); 
			if(!isset($cur_service->id)){
				$MyData->addPoints(NULL, 'Results');
			} else {
				//Results 
				$marks = new marks('student', $student->id);
				$results = $marks->getYearTotal($cur_service);
				$MyData->addPoints($results[0], 'Results');
				if($year->year != $lastyear){
					$grad = $results[1];
					$gradding_array[] = $grad != false && $results[0] > 0 ? array( $year->year, $results[0].'-'.$grad->title) : NULL;
				} else {
					$gradding_array[] = $results[0] > 0 ? array( $year->year, $results[0]) : NULL;
				}
			}
		} else {
			$cur_service = false;		
			//Results 
			$marks = new marks('student', $student->id);
			$results = $marks->getYearTotal(false);
			$MyData->addPoints($results[0], 'Results');
			$grad = $results[1];
			$gradding_array[] = $grad != false ?array( $year->year, $results[0].'-'.$grad->title) : NULL;
		}
		// Absents
		$absent_rows = $absents->getAbsents($year->begin_date, $year->end_date);
		$MyData->addPoints((count($absent_rows)>0 ? count($absent_rows) : NULL ), 'Absents');
	} else {
		$MyData->addPoints(NULL, 'Results');
		$MyData->addPoints(NULL, 'Absents');
	}
		
}
$_SESSION['year'] = $cur_year;
//print_r($MyData);
//exit;

//$MyData->loadPalette("scripts/chart/palettes/light.color", TRUE);
$MyData->setAxisName(0, $chart_title);
$MyData->setXAxisName($con_name);
//$MyData->setSerieDescription("Results",$con_name);
$MyData->setAbscissa("Year");
//$MyData->setSerieWeight("Student",2);


//$serieSettings = array("R"=>229,"G"=>11,"B"=>11,"Alpha"=>100);
//$MyData->setPalette("Server A",$serieSettings);

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = TRUE;

/* Absents */
$Config = array("BreakVoid"=>FALSE, "BreakR"=>234, "BreakG"=>55, "BreakB"=>26);
//$myPicture->drawSplineChart($Config);

 /* Add a border to the picture */
$myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

 /* Set the default font */
$myPicture->setFontProperties(array("FontName"=>"scripts/chart/fonts/verdana.ttf","FontSize"=>8));

/* Define the chart area */
 $myPicture->setGraphArea(60,40,650,200);

 /* Draw the scale */
 $scaleSettings = array("GridR"=>150,"GridG"=>150,"GridB"=>150,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);

/* Overlay with a gradient */
  $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));
 
 /* Write the chart legend */
 /* Student Name */
//$myPicture->drawText(350,45, $con_name,array("FontSize"=>12,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

$myPicture->setFontProperties(array("FontName"=>"scripts/chart/fonts/verdana.ttf","FontSize"=>9));
$myPicture->drawLegend(50,10,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>255,"FontG"=>255,"FontB"=>255));


 
 /* Draw the chart */
 
$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>30));
$myPicture->drawBarChart(array("DisplayValues"=>FALSE,"DisplayColor"=>DISPLAY_AUTO,"Rounded"=>TRUE)); 

$MyData->drawAll(); 
  
if(count($years) > 0){
	$LabelSettings = array("TitleMode"=>LABEL_TITLE_BACKGROUND,"DrawSerieColor"=>FALSE,"TitleR"=>255,"TitleG"=>255,"TitleB"=>255);
	foreach($gradding_array as $key=> $array){
		$LabelSettings["OverrideTitle"]=$array[0];
		$LabelSettings["ForceLabels"]= array($array[1]);
		$myPicture->writeLabel(array("Results"),$key, $LabelSettings);
	}
}

 /* Render the picture (choose the best way) */
 while (@ob_end_clean());
 $myPicture->autoOutput("mark.png");
?>