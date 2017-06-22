<fieldset>
	<table width="100%">
    	<tr>
        	<td class="def_align">
                <h2>[@emp_name]</h2>
                <h3>[@position]</h3>
            </td>
          	<td class="reverse_align">
            	<h4>[#join_date]: [@join_date]</h4>
                <h4>[#salary_profil]: [@profil_name]</h4>
            </td>
        </tr>
    </table>
</fieldset>
   
<h2 class="title" align="center">[#salary_sheet] [@month] [@year]</h2>

<table class="result">
    <tbody>
        <tr>
            <td width="80">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td><h4>[#credit_txt]</h4></td>
        </tr>
        [@credit_trs]
        <tr>
            <td width="80"><b>[@total_credit]</b></td>
            <td width="80">&nbsp;</td>
            <td><b>[#total_salary_credit]</b></td>
        </tr>
        <tr>
            <td width="80">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td><h4>[#debit_txt]</h4></td>
        </tr>
        [@debit_trs]
        <tr>
            <td>&nbsp;</td>
            <td><b>[@total_debit]</b></td>
            <td><b>[#total_salary_debit]</b></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2"  width="160">[@net]</th>
            <th>[#salary_net]</th>
        </tr>
    </tfoot>
</table>