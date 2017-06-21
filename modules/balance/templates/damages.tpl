<div class="toolbox">
    <a action="print_pre" rel="#damages_form" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
    <a action="saveDamages"  title="[#save]">[#save]<span class="ui-icon ui-icon-disk"></span></a>
</div>
<form id="damages_form">
	<h2 class="title">[#damages]: [@acc_title]</h2>
    <h3 class="hidden showforprint">[#year]: [@cur_year]</h3>
    <table class="tableinput">
        <thead>
            <tr>
            	<th width="20" class="unprintable {sorter:false}">&nbsp;</th>
                <th width="80">[#code]</th>
                <th >[#title]</th>
                <th width="60">[#currency]</th>
                <th width="100">قيمة الأصل</th>
                <th width="60">[#percent]</th>
                <th width="100">مصروف الأهلاك</th>
                <th width="100">مجمع الأهلاك</th>
                <th width="100">أجمالي</th>
                <th width="100">قيمة النهائية</th>
            </tr>
        </thead>
        <tbody>
            [@rows]
        </tbody>
        <tfoot>
            [@tfoot]
        </tfoot>
    </table>
</form>