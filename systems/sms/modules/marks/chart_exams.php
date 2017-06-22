<?php
## marks charts

include("scripts/chart/class/pData.class.php");
include("scripts/chart/class/pDraw.class.php");
include("scripts/chart/class/pImage.class.php");

$MyData = new pData();  
$terms = terms::getTermsByCon($con, $con_id);
$absents = new absents($con, $con_id); 

$con_name = $this_system->getAnyNameById($con, $con_id);
$this_effect_val = 0;
$terms_values = array();
$last_result = 0;
$count_all_exams = 0;

if(isset($_GET['service_id'])  && $_GET['service_id'] != ''){
	$cur_service = new services(safeGet($_GET['service_id']));
	$chart_title = $cur_service->name;
	$title = $_GET['service_id'] != 0 ? $cur_service->name : $lang['total'];
	$calc_type =  Marks::getLevelCalcType($cur_service->level_id);
	$first_date = getYearSetting('begin_date');
	foreach($terms as $term){
		$term->con =$con;
		$term->con_id = $con_id;
		$exams = $term->getExamsByTerm($cur_service->id);
		$i = 0;
		
		if(count($exams) > 0){			
			foreach($exams as $exam){
				$results = $exam->getExamResults();
				if($results != false && count($results) > 0){
					if($i == 0 ){
						$MyData->addPoints($term->name,"Exams");
						$MyData->addPoints(NULL,"Absents");
					} elseif($i == count($exams)-1) {
						if($exam->date != ''){
							$absent_rows = $absents->getAbsents($first_date, $exam->date);
							$first_date = $exam->date;
							$MyData->addPoints((count($absent_rows)>0 ? count($absent_rows) : NULL ), 'Absents');
						} else {
							$MyData->addPoints(NULL,"Absents");
						}
						$MyData->addPoints($exam->exam_no,"Exams");
					} else {
						$MyData->addPoints($exam->exam_no,"Exams");
						if($exam->date != ''){
							$absent_rows = $absents->getAbsents($first_date, $exam->date);
							$first_date = $exam->date;
							$MyData->addPoints((count($absent_rows)>0 ? count($absent_rows) : NULL ), 'Absents');
						} else {
							$MyData->addPoints(NULL,"Absents");
						}
					}
					// class lines
					$statics = $exam->getStatics();
					$class_min =  ($statics->min / $exam->max) * 100;
					$class_max =  ($statics->max / $exam->max) * 100;
					$class_avg =  ($statics->avg / $exam->max) * 100;
					$MyData->addPoints(round($class_min, 2), 'Class min');
					$MyData->addPoints(round($class_max, 2), 'Class max');
					$MyData->addPoints(round($class_avg, 2), 'Class avg');
						
					//Student
					if($con == 'student'){
						$std_results = $results[$con_id];
						// Take care of absent value
						if($std_results == NULL){
							$std_results = $last_result;
						}
						$last_result = $std_results;
						
						if($calc_type == 'per' || $calc_type == 'marks'){
							$this_exam_val = $std_results / ($exam->max> 1 ? $exam->max : 1);
							//$MyData->addPoints(round($this_effect_val, 2), 'Effective');
						} elseif($calc_type == 'moyen'){
							if($exam->results != NULL ){
								$coef = $exam->coef!=0 ? $exam->coef : 1;
								$this_exam_val = $std_results / ($exam->max > 1 ? $exam->max : 1);
								$maxs = $maxs + ($exam->max * $coef);
								$moyens = $moyens + ($std_results * $coef);
								$this_effect_val = $moyens / ($maxs != 0 ?$maxs : 1);
							} else {
								$this_exam_val = NULL;
								$this_effect_val = NULL;
							}
							$MyData->addPoints(round($this_effect_val*100,2), 'Effective');
						} elseif($calc_type == 'skills'){
							$max = count(getGradinArray($level_id));
							if($exam->results != NULL){
								$this_exam_val = ($max-$std_results)/ $max;
							} else {
								$this_exam_val = NULL;
							}
						}
						$MyData->addPoints(round($this_exam_val*100,2), 'Student');
					}
					$i++;
				}
			}
		}
		if($calc_type == 'moyen'){
			$total_term = $term->get_mat_term_avg($cur_service, $con, $con_id);
		} elseif($calc_type == 'points'){
			$total_term = $term->get_mat_term_points($cur_service, $con, $con_id);
		} elseif($calc_type == 'per'){
			$total_term = $term->get_mat_term_per($cur_service, $con, $con_id);
		} elseif($calc_type == 'skills'){
			$total_term = $term->get_mat_term_grad($cur_service, $con, $con_id);
		} 
		$count_all_exams = ($count_all_exams + $i-1);
		if($total_term > 0){
			$terms_values[$count_all_exams]= array($term->name, $total_term);
		}

	}
} else {
	$chart_title = "Total";
	$obj = $sms->getAnyObjById($con, $con_id);
	$level_id = $obj->getLevel()->id;
	$calc_type = marks::getLevelCalcType($level_id);
	foreach($terms as $term){
		$term_id = $term->id;
		$this_term_val ='';// $term->get_std_term_total($con_id, $term_id, $calc_type);
		$MyData->addPoints($term->name,"Exams");	
		$terms_values[]= array($term->name, $this_term_val);

		$class_eval = $term->get_class_term_statics( $service, "class", $class_id) ;
		$MyData->addPoints(($class_eval != false ? $class_eval->min : ''), 'Class min');
		$MyData->addPoints(($class_eval != false ? $class_eval->max : ''), 'Class max');
		$MyData->addPoints(round(($class_eval != false ? $class_eval->avrg : ''), 2), 'Class avg');
		
		$MyData->addPoints(round($this_term_val,2), 'Student');
		
		$absent_rows = $absents->getAbsents($term->begin_date, $term->end_date);
		$MyData->addPoints(count($absent_rows), 'Absents');
	}
}
//print_r($MyData);
//exit;

//$MyData->loadPalette("scripts/chart/palettes/light.color", TRUE);
$MyData->setAxisName(0, $chart_title);
$MyData->setXAxisName($con_name);
$MyData->setSerieDescription("Exams",$con_name);
$MyData->setAbscissa("Exams");
$MyData->setSerieWeight("Student",2);


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
$myPicture->drawText(350,45, $con_name,array("FontSize"=>12,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

$myPicture->setFontProperties(array("FontName"=>"scripts/chart/fonts/verdana.ttf","FontSize"=>9));
$myPicture->drawLegend(50,10,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>255,"FontG"=>255,"FontB"=>255));


 
 /* Draw the chart */
$myPicture->setShadow(FALSE);
$MyData->setSerieDrawable("Absents",FALSE);
$myPicture->drawLineChart();
$myPicture->drawPlotChart(array("DisplayValues"=>FALSE,"PlotBorder"=>TRUE,"BorderSize"=>1,"Surrounding"=>-60,"BorderAlpha"=>50)); 
 
$MyData->setSerieDrawable("Absents",TRUE);
$MyData->setSerieDrawable("Student",FALSE);
$MyData->setSerieDrawable("Effective",FALSE);
$MyData->setSerieDrawable("Class max",FALSE);
$MyData->setSerieDrawable("Class min",FALSE);
$MyData->setSerieDrawable("Class avg",FALSE);
$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>30));
$myPicture->drawBarChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"Rounded"=>TRUE)); 

$MyData->drawAll(); 
  
/* Terms label */
if(count($terms_values) > 0){
	$LabelSettings = array(
		"DrawVerticalLine"=>TRUE,
		"TitleMode"=>LABEL_TITLE_BACKGROUND,
		"TitleR"=>255,"TitleG"=>255,"TitleB"=>255, 
		"DrawSerieColor"=>FALSE,
		"GradientEndR"=>220,"GradientEndG"=>255,"GradientEndB"=>220
	); 
	foreach($terms_values as $key=> $array){
		$LabelSettings["OverrideTitle"]=$array[0];
		$LabelSettings["ForceLabels"]= array($array[1]);
		$myPicture->writeLabel(array("Student"),$key, $LabelSettings);
	}
}

/* Render the picture (choose the best way) */
while (@ob_end_clean());
$myPicture->autoOutput("mark.png");
?>