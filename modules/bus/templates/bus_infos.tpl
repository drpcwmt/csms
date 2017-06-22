<form id="bus_form">
  <input type="hidden" id="bus_id" name="id" value="[@id]"/>
  <fieldset>
    <table>
      <tr>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#code] </label>
        </td>
        <td>
        	<div class="fault_input">[@id]</div>
         <!-- <input type="text" id="code" name="code" class="required" value="[@code]"/>-->
        </td>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#max_seats] </label>
        </td>
        <td>
          <input type="text" id="max" name="max" class="required hafe_input" value="[@max]"/>
        </td>
      </tr>
      <tr>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#model] </label>
        </td>
        <td>
          <input type="text" id="model" name="model" value="[@model]"/>
        </td>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#year] </label>
        </td>
        <td>
          <input type="text" id="year" name="year" value="[@year]"/>
        </td>
    </tr>
  </table>
  </fieldset>

  <fieldset>
    <legend>  [#license] </legend>
    <table>
      <tr>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#license] </label>
        </td>
        <td>
          <input type="text" id="lic_no1" name="lic_no1" style="width:70px" value="[@lic_no1]"/>
          <input type="text" id="lic_no2" name="lic_no2" style="width:70px" value="[@lic_no2]"/>
        </td>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#cc] </label>
        </td>
        <td>
          <input type="text" id="cc" name="cc" value="[@cc]"/>
        </td>
      </tr>
      <tr>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#issue_date] </label>
        </td>
        <td>
          <input type="text" id="issue_date" name="issue_date" class="mask-date" value="[@issue_date]"/>
        </td>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#exp_date] </label>
        </td>
        <td>
          <input type="text" id="exp_date" name="exp_date" class="mask-date" value="[@exp_date]"/>
        </td>
      </tr>
      <tr>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#lic_from] </label>
        </td>
        <td>
          <input type="text" id="lic_from" name="lic_from" value="[@lic_from]"/>
        </td>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#examination_date] </label>
        </td>
        <td>
          <input type="text" id="examination_year" name="examination_year" value="[@examination_year]"/>
        </td>
      </tr>
      <tr>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#chassis_no] </label>
        </td>
        <td>
          <input type="text" id="chassis_no" name="chassis_no" value="[@chassis_no]"/>
        </td>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#motor_no] </label>
        </td>
        <td>
          <input type="text" id="motor_no" name="motor_no" value="[@motor_no]"/>
        </td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <table>
      <tr>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#license] </label>
        </td>
        <td>
           <span class="buttonSet">
            <input type="radio" id="owned1[@new]" name="owned" value="1" [@owned_check_1]/>
            <label for="owned1[@new]"> [#owned] </label>
            <input type="radio" id="owned2[@new]" name="owned" value="0" [@owned_check_0]/>
            <label for="owned2[@new]"> [#hired] </label>
          </span>
        </td>
      </tr>
      <tr>
        <td width="100" class="reverse_align">
          <label class="label ui-widget-header ui-corner-left"> [#owner_name] </label>
        </td>
        <td>
          <input type="text" id="owner_name" name="owner_name" class="input_double" value="[@owner_name]"/>
        </td>
      </tr>
    </table>
  </fieldset>
</form>
