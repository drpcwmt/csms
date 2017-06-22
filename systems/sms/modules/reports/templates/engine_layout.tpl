<h4>[#layout]</h4>
<ul>
	<li style="margin-bottom:7px; padding:3px" class="ui-corner-all ui-widget-content">
        <label style="font-size:130%; font-weight:bolder"><input type="radio" value="0" name="layout" checked />[#table] 
        	<span class="ui-button-text ui-state-default hoverable" style="display: inline-block;vertical-align: top; padding:4px">
            	<img src="/assets/img/table_view.png" width="60" height="60" />
            </span>
       </label>
    </li>
    <li style="margin-bottom:7px; padding:3px" class="ui-corner-all ui-widget-content">
		<label style="font-size:130%; font-weight:bolder">
        <input type="radio" value="new" name="layout" />
            [#new]
        </label>
        <div class="buttonSet" style="text-align:center; direction: initial">
            <input type="radio" id="new_layout-a4" name="new_layout" >
            <label for="new_layout-a4"><img src="/assets/img/a4.png" width="60" height="60" /><br>A4</label>

            <input type="radio" id="new_layout-a5" name="new_layout">
            <label for="new_layout-a5"><img src="/assets/img/a5.png" width="60" height="60"/><br>A5</label>

            <input type="radio" id="new_layout-a6" name="new_layout">
            <label for="new_layout-a6"><img src="/assets/img/a6.png" width="60" height="60"/><br>A6</label>            

            <input type="radio" id="new_layout-custom" name="new_layout">
            <label for="new_layout-custom"><img src="/assets/img/custom_size.png" width="60" style="vertical-align:text-bottom" /><br>[#custom]</label>            
        </div>
    </li>
    <li style="padding:3px" class="ui-corner-all ui-widget-content">
        <label style="font-size:130%; font-weight:bolder"><input type="radio" value="saved" name="layout" />
        	[#custom]
         </label>
        <div style="padding:0px 30px 10px">
            <table width="100%" cellspacing="1" cellpadding="0" border="0">
                <tr >
                  <td width="80" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#title]</label></td>
                  <td valign="top" colspan="2" class="def_align">
                    <select name="layout_template" class="combobox" style="width:300px">
                        [@layout_templates_opts]
                    </select>
                  </td>
                </tr>
            </table>
        </div>
       
    </li>
</ul>    