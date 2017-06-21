<?php
## certificate of Scolarity ##
####################
// sql Query
if(isset($_GET['std_id']) && $_GET['std_id'] != ''){
	$std_id = safeGet($_GET["std_id"]);
	$std = new Students($std_id);
}

$report_lang = isset($_GET['lang']) ? $_GET['lang'] : $_SESSION['lang'];
// Check if student status is valid 
## 0 = desincriped
## 1 = inscriped
## 2 = waiting list
## 3 = temp suspension
## 5 = Guratuated

if( !isset($std_id) || $std == false){
	echo write_error($lang['cant_find_std_code']);
} elseif($std->status == 0){
	echo write_error($lang['error-std_unregistred']);
} elseif($std->status == 2){
	echo write_error($lang['error-std_waiting']);
} elseif($std->status == 3){
	echo write_error($lang['error-std_suspended']);
} elseif($std->status == 5){
	echo write_error($lang['error-std_graduated']);
} else {
	$studentname = $report_lang == 'ar' ? $std->name_ar : $std->name;
	$birthdate = unixToDate($std->birth_date);
	$curyear = $_SESSION["year"] . ' / '. ($_SESSION["year"] + 1);
	$birthplace = $report_lang == 'ar' ? $std->birth_city_ar.' - '.$std->birth_country_ar : $std->birth_city.' - '.$std->birth_country;
	$nationality = $report_lang == 'ar' ? $std->nationality_ar : $std->nationality;
	$today = date ( 'd / m / Y');
	$old_direction = $_SESSION['dirc'];
	if($report_lang == 'ar') {
		$_SESSION['dirc'] = 'rtl';
	} else {
		$_SESSION['dirc'] = "ltr";
	}
	$level = $std->getLevel()->getName();
	$proviseur = $MS_settings['school_cp'];
	if($report_lang == 'ar'){
		// Arabic version
		$html = "<div align=\"center\" class=\"content\" style=\"margin:60px; font-size:15px\"><h2>شهادة قيد</h2><br /><br />
		  <p align=\"right\">تفيد المدرسة بأن ".($std->sex == 1 ? "الطالب" : "الطالبة")."  :<br />
		  	<ul>
		  		<li><b>$studentname</b></li>
			</ul>
		  </p>
		  <p align=\"right\"><b>تاريخ الميلاد :</b> $birthdate في $birthplace</p>
		  <p align=\"right\"><b>الجنسية : $nationality</p>
		  <p align=\"right\">مقيد لديها في الصف $level للسنة الدراسية $curyear</p>
		  <p align=\"right\">وهذا أقرار منا بذلك</p>
		  <p align=\"left\">القاهرة $today</p><br /><br />
		  <p align=\"left\">مدير شؤن الطلبة </p>
		</div>";
	} elseif($report_lang == 'fr'){
		// Frensh Version
		$html = "<div align=\"center\" class=\"content\" style=\"margin:60px; font-size:15px\"><h2>Certificat de Scolarit&eacute;</h2><br /><br />
		  <p align=\"left\">L'&eacute;tablissementcertifie que l`&eacute;l&egrave;ve <b>$studentname</b></p>
		  <p align=\"left\"><b>N&eacute;(e) le  :</b> $birthdate - $birthplace</p>
		  <p align=\"left\"><b>Nationalit&eacute;</b> : $nationality</p>
		  <p align=\"left\">Est inscrit(e) dans notre &eacute;tablissement en classe de $level </p>
		  <p align=\"left\">pour l`ann&egrave; scolaire $curyear</p>
		  <p align=\"right\">Fait au Caire, le $today</p><br /><br />
		  <p align=\"right\">Le proviseur</p>
		</div>";
	} elseif($report_lang == 'en'){
		// English Version
		$html = "<div align=\"center\" class=\"content\" style=\"margin:60px; font-size:15px\"><h2>Enrollement certificate</h2>
<br /><br />
		  <p align=\"left\" style=\"margin-top:120px\">This is to certify that the student: <b>$studentname</b></p><br /><br />
		  <p align=\"left\"><b>Born :</b> $birthdate in $birthplace</p>
		  <p align=\"left\"><b>Nationality</b> : $nationality</p><br /><br />
		  <p align=\"left\">Is enrolled at ".$MS_settings['school_name']."  in class of $level for the school year $curyear</p><br /><br /><br />
		  <p align=\"right\">Cairo, $today</p><br /><br />
		  <p align=\"right\">School administration</p>
		</div>";
	}
	// ---------------------------------------------------------
	echo $html;
	$_SESSION['dirc'] = $old_direction;
}