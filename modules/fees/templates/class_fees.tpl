<div id="first_class" >
    <div class="toolbox">
        <a action="print_tab"  title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
    </div>
	<h2 class="title showforprint hidden">[@class_name]</h2>
     <h3>[#total_std]: [@total_students]</h3>
     <form class_id="[@id]">
         <table class="tableinput">
            <thead>
                <tr>
                    <th class="unprintable" width="20">&nbsp;</th>
                    <th>[#name]</th>
                    <th width="100">[#join_date]</th>
                    <th width="20">[#employe_son]</th>
                    <th width="20">[#brothers]</th>
                    <th width="20">[#locker]</th>
                    <th width="20">[#bus]</th>
                    <th width="170">[#profil]</th>
                    <th width="60">[#total]</th>
                    <th width="60">[#paid]</th>
                    <th width="60">[#rest]</th>
                    <th width="20" class="unprintable">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                [@student_rows]
            </tbody>
         </table>
     </form>
</div>