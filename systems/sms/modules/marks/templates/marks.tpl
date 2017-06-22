<div class="tabs scope" con="[@con]" con_id="[@con_id]">
	<ul>
    	<li><a href="#exams_tabs_div-[@con]-[@con_id]">[#exams]</a></li>
        <li><a href="index.php?module=marks&amp;skills&amp;con=[@con]&con_id=[@con_id]">[#skills]</a></li>
        <li><a href="index.php?module=marks&amp;appreciation&amp;con=[@con]&con_id=[@con_id]">[#appreciations]</a></li>
        <li><a href="index.php?module=marks&amp;reports&amp;con=[@con]&con_id=[@con_id]">[#reports]</a></li>
        [@student_reports_list]
        [@student_gpa]
    </ul>
    <div id="exams_tabs_div-[@con]-[@con_id]">
    	[@toolbox]
        <div class="exam_table">
    		[@mark_table]
        </div>
    </div>
</div>