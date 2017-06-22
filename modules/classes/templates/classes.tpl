<div class="tabs">
    <ul>
        <li><a href="#classes-infos-[@id]">[#info]</a></li>
        <li class="[@pro_option]"><a href="index.php?module=services&list&con=class&con_id=[@id]">[#materials]</a></li>
        <li class="[@pro_option]"><a href="index.php?module=groups&list&con=class&con_id=[@id]">[#groups]</a></li>
        <li class="[@pro_option]"><a href="index.php?module=schedule&con=class&con_id=[@id]" after="initTimeTable">[#schedule]</a></li>
        <li class="[@pro_option]"><a href="index.php?module=marks&con=class&con_id=[@id]" after="initExamTable">[#marks]</a></li>
        <li><a href="index.php?module=photos_board&con=class&con_id=[@id]">[#photos_board]</a></li>
        <li><a href="index.php?module=todo&con=level&con_id=[@id]">[#todo]</a></li>
        [@extra_tabs_lis]
   </ul>
   <div id="classes-infos-[@id]" class="scope"> 
      
       <form name="class-infos-[@id]">
            [@resources_toolbox]
          <input type="hidden" value="[@id]" name="id" >
            <div class="ui-state-highlight ui-corner-all" style="margin-top:0px; padding:0px">
             <h3 align="center" class="hidden showforprint" style="margin:3px">[#class_list] [@year]</h3>
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tbody>
                  <tr>
                    <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" dir="ltr">[#name]</label></td>
                    <td><input type="text" value="[@name_ltr]" name="name_ltr" id="class_name_en" class="required"></td>
                    <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#name_ar]</label></td>
                    <td width="185"><input type="text" value="[@name_rtl]" name="name_rtl" id="class_name_ar" dir="rtl" class="required"></td>
                  </tr>
                  <tr>
                    <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#level]</label></td>
                    <td>
                        <select name="level_id" class="combobox required">
                            [@levels_select]
                        </select>
                    </td>
                    <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#hall]</label></td>
                    <td>
                        <select id="room_no" class="combobox" name="room_no">
                            [@halls_select]
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#class_teacher]</label></td>
                    <td>
                        <input type="text" class="input_double required" value="[@resp_name]" name="emp_sug_div" id="emp_sug_div">
                        <input type="hidden" value="" class="autocomplete_value" id="resp" name="resp">
                    </td>
                    <td width="120" valign="middel"><label class="label ui-widget-header reverse_align">[#total_std]</label></td>
                    <td>
                        <div class="fault_input ui-corner-right">[@total_students]</div>

                  </tr>
                </tbody>
              </table>
           	</div>
        </form>
        [@student_list_table]
   </div>
    [@extra_tabs_divs]
</div>