<tr>
    <td class="unprintable">
    	<input type="hidden" name="emp_id[]" value="[@id]" />
    	<button module="employers" empid="[@id]" action="openEmployer" class="ui-state-default hoverable circle_button"><span class="ui-icon ui-icon-person"></span></button>
    </td>
    <td>[@code]</td>
    <td>[@name]</td>
    <td>[@position]</td>
    <td>[@join_date]</td>
    <td>[@basic]</td>
    <td>[@var]</td>
    <td>[@allowances]</td>
    <td class="unprintable">
    	<input type="text"  name="basic[]" />
    </td>
    <td class="unprintable">
		<select name="basic_cur[]">[@basic_cur_opts]</select>
    </td>
    <td class="unprintable">
    	<input type="text"  name="var[]" />
    </td>
    <td class="unprintable">
        <select name="var_cur[]">[@var_cur_opts]</select>
    </td>
    <td class="unprintable">
    	<input type="text" name="allowances[]" />
    </td>
    <td class="unprintable">
        <select name="allowances_cur[]">[@var_cur_opts]</select>
    </td>
    <td>
    	<select name="profil_id[]">[@profil_opts]</select>
    </td>
</tr>
