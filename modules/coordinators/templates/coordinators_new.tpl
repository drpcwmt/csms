<form class="ui-state-highlight ui-corner-all unprintable " id="new_resource_form">
  <table width="100%" cellspacing="0" cellpadding="0" border="0" class="[@fieldset_name]">
    <tbody>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
        <td>
        	<input type="text" id="new_employer_name" class="input_double " />
			<input type="hidden" class="autocomplete_value required" name="id" value="[@id]" />
        </td>
      </tr>
  </table>
  <br />
  <fieldset>
  	<legend>[#levels]</legend>
    <table class="tablesorter chkTable">
		<thead>
			<tr>
				<th width="20" class="{sorter:false}">&nbsp;</th>
                <th>[#level]</th>
            </tr>
        </thead>
        <tbody>
        	[@levels_trs]
        </tbody>
    </table>
  </fieldset>
</form>
