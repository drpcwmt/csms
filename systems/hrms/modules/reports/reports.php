<?php
## hrms reports

$job_id =isset($_GET['job_id']) ? safeGet('job_id') : '';
$month =isset($_GET['month']) ? safeGet('month') : '';
$cc =isset($_GET['cc']) ? safeGet('cc') : '';


echo Reports::loadMainLayout($job_id, $month, $cc);
?>