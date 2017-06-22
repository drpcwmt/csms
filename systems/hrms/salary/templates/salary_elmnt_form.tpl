<div class="tabs">
	<ul>
		<li><a href="#select_elmnt_tab">[#select]</a></li>
    	<li><a href="#new_elmt_tab">[#new]</a></li>
    </ul>
	<div id="select_elmnt_tab">
        <form>
             <input type="hidden" id="profil_id" name="profil_id" />
             <input type="hidden" name="field" value="[@field]" />
             <table width="100%" cellspacing="0">
                <tr>
                   <td width="100"><label class="label ui-widget-header ui-corner-left reverse_align">[#select]</label></td>
                   <td>
                        [@elmnts_select]
                   </td>
                </tr>
             </table>
        </form>
    </div>
    
	<div id="new_elmt_tab">
        <form>
            <fieldset class="ui-state-highlight ui-corner-all">
                <input type="hidden" id="profil_id" name="profil_id" />
                <input type="hidden" name="field" value="[@field]" />
                 <table width="100%" cellspacing="0">
                    <tr>
                       <td width="100"><label class="label ui-widget-header ui-corner-left reverse_align">[#title]</label></td>
                       <td>
                        <input class="input_double required" name="name"  type="text"/>
                       </td>
                    </tr>
                    <tr>
                        <td class="reverse_align" valign="middel" width="100">
                            <label class="label ui-widget-header ui-corner-left reverse_align">[#acc_code]</label>
                        </td>
                        <td valign="top">
                          <span class="account_code" style="float:right">
                            <input name="main_code" style="width:40px" value="[@main_code]" maxlength="5" class="main_code required" />
                            <input style="width:40px" name="sub_code" value="[@sub_code]" maxlength="5" class="sub_code required"/>
                          </span>
                          <input type="text" class="acc_title" />
                        </td>
                    </tr>
                    <tr>
                       <td width="100"><label class="label ui-widget-header ui-corner-left reverse_align">[#title]</label></td>
                       <td>
                        <select class="input_half required combobox" name="currency">[@currency_opts]</select>
                       </td>
                    </tr>
                </table>                
            </fieldset>
            <ul style="list-style:none; padding:0; margin:10px">
                <li class="ui-state-default hoverable ui-corner-all" style="padding:3px; margin-bottom:3px"  onClick="$(this).find('input:radio').attr('checked', 'checked')">
                    <input type="radio" name="type" value="fx" checked /> [#fixed_value]
                    <table width="100%" cellspacing="0">
                        <tr>
                           <td width="100"><label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label></td>
                           <td>
                            <input type="text" name="value" class="input_half" />
                             <select name="currency" class="combobox">
                                [@currency_opts]
                            </select>
                           </td>
                        </tr>
                    </table>
                </li>
                <li class="ui-state-default hoverable ui-corner-all" style="padding:3px; margin-bottom:3px" onClick="$(this).find('input:radio').attr('checked', 'checked')">
                    <input type="radio"  name="type" value="eq" />[#equation]
                     <table width="100%" cellspacing="0">
                        <tr>
                           <td width="100"><label class="label ui-widget-header ui-corner-left reverse_align">[#equation]</label></td>
                           <td>
                            <input class="input_double" name="equation"  type="text"/>
                           </td>
                        </tr>
                    </table>                
               </li>
            </ul>
            <div class="ui-corner-all ui-state-error" style="margin:10px; padding:7px">
                <ul style="font-size:9px">
                    <li><strong>basic</strong> => [#basic]</li>
                    <li><strong>var</strong> => [#var]</li>
                    <li><strong>allowances</strong> => [#salary_allow]</li>
                    <li><strong>bonus</strong> => [#bonus]</li>
                    <li><strong>salary_plus</strong> => [#salary_plus]</li>
                    <li><strong>salary_extra</strong> => [#salary_extra]</li>
                    <li><strong>absents</strong> => [#absents]</li>
                    <li><strong>discounts</strong> => [#discounts]</li>
                    <li><strong>insur_soc</strong> => [#insur_soc_share]</li>
                    <li><strong>insur_tot</strong> => [#insur_total]</li>
                    <li><strong>tax_stamp</strong> => [#tax_stamp]</li>
                    <li><strong>tax_gain</strong> => [#tax_worker]</li>
                </ul>
            </div>
        </form>
    </div>
</div>