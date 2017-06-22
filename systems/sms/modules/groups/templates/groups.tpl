<div class="ui-corner-all ui-widget-content" style="padding:5px">
	<h2 class="title">[@group_name] <em class="mini">([@parent_name])</em></h2> 
	<div class="tabs">
    	<ul>
            <li><a href="#group-infos-[@id]">[#info]</a></li>
            <li><a href="index.php?module=schedule&con=group&con_id=[@id]" after="initTimeTable">[#schedule]</a></li>
            <li><a href="index.php?module=photos_board&con=group&con_id=[@id]">[#photos_board]</a></li>
            <li><a href="index.php?module=todo&con=group&con_id=[@id]">[#todo]</a></li>
       </ul>
       <div id="group-infos-[@id]"> 
			
            <form name="group-infos-[@id]">
              <input type="hidden" value="[@id]" name="id" >
              <input type="hidden" value="[@parent]" name="parent" >
              <input type="hidden" value="[@parent_id]" name="parent_id" >
              <input type="hidden" value="[@editable]" name="editable" >
                <div class="toolbox"> 
                    <a action="saveGroup" class="[@edit_button]" > 
                        <span class="ui-icon ui-icon-disk"></span>[#save] 
                    </a> 
                     <a action="deleteGroup" groupid="[@id]" class="[@edit_button]" > 
                        <span class="ui-icon ui-icon-trash"></span>[#delete] 
                    </a> 
                     <a action="print_pre" rel="#group-infos-[@id]"> 
                        <span class="ui-icon ui-icon-print"></span>[#print] 
                    </a> 
                    <a onClick="exportTable" rel="#group-infos-[@id]"> 
                        <span class="ui-icon ui-icon-disk"></span>[#export] 
                    </a> 
                </div>
                <fieldset  class="ui-state-highlight ui-corner-all">
                  <table width="90%" cellspacing="0" border="0">
                    <tbody>
                      <tr>
                        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" dir="ltr">[#name]</label></td>
                        <td><input type="text" value="[@name]" name="name" class="required"></td>
                      </tr>
                      <tr>
                        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#material]</label></td>
                        <td>
                            <select name="service_id" class="combobox required">
                                [@services_select]
                            </select>
                        </td>
                      </tr>
                      <tr>
                        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#resp]</label></td>
                        <td>
                            <input type="text" class="input_double sug_emp" value="[@resp_name]" name="emp_sug_div" id="emp_sug_div">
                            <input type="hidden" value="[@resp]" class="autocomplete_value" id="resp" name="resp">
                        </td>
                      </tr>
                      <tr>
                        <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#comments]</label></td>
                        <td>
                            <textarea name="comments">[@comments]</textarea>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </fieldset>
            </form>
            <div class="toolbox">
                <a action="addGroupStd"><span class="ui-icon ui-icon-plus"></span>[#add_manual]</a>
                <a action="addGroupStdAuto"><span class="ui-icon ui-icon-plusthick"></span>[#add_auto]</a>
            </div>
            <h3>[#total_std]: [@total_students]</h3>
            [@student_list_table]
       </div>
</div>
