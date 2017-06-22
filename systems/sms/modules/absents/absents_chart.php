<?php
## CHART absents ##
include("scripts/chart/class/pData.class.php");
include("scripts/chart/class/pDraw.class.php");
include("scripts/chart/class/pImage.class.php");

$MyData = new pData();  
$Arabic = new I18N_Arabic('Glyphs');

if(isset($_SESSION['cur_class'])){
	$con = 'class';
	$con_id = $_SESSION['cur_class'];
}

if(isset($_GET['con_id']) && $_GET['con_id']!= '0'){
	$con = $_GET['con'];
	$con_id = $_GET['con_id'];
} else {
	$con = 0;
	$con_id=0;
}

$obj = $sms->getAnyObjById($con, $con_id);
if($con == 'student'){
	$stds = array($con_id);	
} else {
	$std = $obj->getStudents();
	foreach($std as $s){
		$stds[] = $s->id;
	}
}

$con_name = $_SESSION['dirc'] != 'rtl' ? $obj->getName() : $obj->getName(true);;
$chart_title = 'Absents: '.  $con_name;
$begin_date = $sms->getYearSetting('begin_date');
$end_date = $sms->getYearSetting('end_date');
$b_m = date('m', $begin_date);
$b_y = date('Y', $begin_date);

$cur_date = $begin_date;
$i=1;
$max = 0;
$data_1 = array();
$data_1['label'] = 	$lang['absents_by_day'];
$data_1['values'] = array();
$data_2 = array();
$data_2['label'] = 	$lang['absents_by_lesson'];		
$data_2['values'] = array();

while($cur_date <= $end_date){
	$month_end = mktime(0, 0, 0, ($b_m+$i),-1 , $b_y);
	$query = do_query_array("SELECT id FROM absents WHERE day>=$cur_date AND day<=$month_end AND ( con_id=".implode(' OR con_id=', $stds).")", $sms->db_year);
	$query_by_lesson = do_query_array("SELECT id FROM absents_bylesson WHERE date>=$cur_date AND date<=$month_end AND ( std_id=".implode(' OR std_id=', $stds).")", $sms->db_year);
	$data_1['values'][] = count($query);
	$data_2['values'][] = count($query_by_lesson);
	$max = ( count($query) > $max) ? count($query) : $max;
	$max = ( count($query_by_lesson) > $max) ? count($query_by_lesson) : $max;
	$chart_x_array[] = date('M', $cur_date);
	$cur_date = mktime(0, 0, 0, ($b_m+$i),1 , $b_y);
	$i++;
}

$MyData->addPoints($data_1['values'], 'Serie1'); 
$MyData->addPoints($data_2['values'],"Serie2");
$MyData->addPoints($chart_x_array,"months"); 

$MyData->setAxisName(0, 'Absents');
$MyData->setXAxisName($chart_title);
$MyData->setSerieDescription("months", 'Absents');
$MyData->setAbscissa('months');


 /* Create the pChart object */
 $myPicture = new pImage(500,230,$MyData);


 /* Overlay with a gradient */
 $myPicture->drawGradientArea(0,0,500,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,499,229,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the picture title */ 
$myPicture->setFontProperties(array("FontName"=>"scripts/chart/fonts/calibri.ttf","FontSize"=>8));
 $myPicture->drawText(10,18, $chart_title, array("R"=>255,"G"=>255,"B"=>255));

 /* Write the chart title */ 
$myPicture->setFontProperties(array("FontName"=>"scripts/chart/fonts/calibri.ttf","FontSize"=>8));
 $myPicture->drawText(250,55, $con_name, array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Draw the scale and the 1st chart */
 $myPicture->setGraphArea(60,60,450,190);
 $myPicture->drawFilledRectangle(60,60,450,190,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
 $myPicture->drawScale(array("DrawSubTicks"=>TRUE));
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
$myPicture->setFontProperties(array("FontName"=>"scripts/chart/fonts/pf_arma_five.ttf","FontSize"=>8));
 $myPicture->drawBarChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"Rounded"=>TRUE,"Surrounding"=>30));
 $myPicture->setShadow(FALSE);

 /* Write the chart legend */
 $myPicture->drawLegend(510,205,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

$MyData->drawAll(); 
//print_r($MyData); exit;
 /* Render the picture (choose the best way) */
  while (@ob_end_clean());

 $myPicture->autoOutput("absents.png");

?>