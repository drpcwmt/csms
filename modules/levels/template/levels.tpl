<div class="tabs">
    <ul>
        <li><a href="#levels-infos-[@id]">[#info]</a></li>
        <li class="[@pro_option]"><a href="index.php?module=services&list&con=level&con_id=[@id]">[#materials]</a></li>
        <li class="[@pro_option]"><a href="index.php?module=groups&list&con=level&con_id=[@id]">[#groups]</a></li>
        <li class="[@pro_option]"><a href="index.php?module=schedule&con=level&con_id=[@id]" after="initTimeTable">[#schedule]</a></li>
        <li class="[@pro_option]"><a href="index.php?module=marks&con=level&con_id=[@id]" after="initExamTable">[#marks]</a></li>
        <li><a href="index.php?module=photos_board&con=level&con_id=[@id]">[#photos_board]</a></li>
        <li><a href="index.php?module=todo&con=level&con_id=[@id]">[#todo]</a></li>
        [@school_fees_li]
   </ul>
   <div id="levels-infos-[@id]"> 
        <form name="level-infos-[@id]" editable="[@editable]">
            <input type="hidden" value="[@id]" name="id" />
            [@resources_toolbox]
            <div class="ui-state-highlight ui-corner-all">
                <table width="100%" cellspacing="0" border="0">
                     <tr>
                        <td width="120" valign="middel">
                            <label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label>
                        </td>
                        <td>
                            <input type="text" value="[@name_ltr]" name="name_ltr" />
                        </td>
                        <td width="120" valign="middel">
                            <label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#name_ar]</label>
                        </td>
                        <td>
                            <input type="text" value="[@name_rtl]" name="name_rtl" dir="rtl" />
                        </td></tr><tr><td width="120" valign="middel">
                            <label class="label ui-widget-header ui-corner-left reverse_align">[#etab]</label>
                        </td>
                        <td colspan="3">
                            <select id="etab_id" class="combobox" name="etab_id">
                                [@etabs_select]
                            </select>
                        </td>
                      </tr>
                  </table>
             </div>
        </form>
        
        <fieldset>
        	<legend>[#students]</legend>
             <h4>[#total_std]: [@total_students]</h4>
           	<button action="openStudentList" module="reports" con="level" conid="[@id]" class="ui-corner-all ui-state-default hoverable">[#students_lists]</button>
       </fieldset>

        <fieldset>
        	<legend>[#principals]</legend>
            <table class="tablesorter">
            	<thead>
                	<tr>
                    	<th style="background-image:none" class="unprintable" width="22">&nbsp;</th>
                        <th>[#name]</th>
                     </tr>
                </thead>
                <tbody>
                	[@principals_trs]
                </tbody>
            </table>
       </fieldset>

        <fieldset>
        	<legend>[#classes]</legend>
            <table class="tablesorter">
            	<thead>
                	<tr>
                    	<th style="background-image:none" class="unprintable" width="22">&nbsp;</th>
                        <th>[#name]</th>
                     </tr>
                </thead>
                <tbody>
                	[@classes_trs]
                </tbody>
            </table>
       </fieldset>
       
   </div>
