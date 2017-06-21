<form >
   <input type="hidden" name="service_id" value="[@service_id]" />
   <input type="hidden" name="std_id" value="[@std_id]" />
   <input type="hidden" name="exam" value="[@exam]" />
   <h3 class="title">[@pay_name]</h3>
    <table class="tableinput">
    	<thead>
        	<tr>
            	<th>&nbsp;</th>
            	<th>[#school_fees]</th>
                <th>[#registration_fees]</th>
                <th>[#total]</th>
            </tr>
        </thead>
        <tbody>
          <tr>
          	<td>[#paid]</td>
            <td valign="middel" width="120">[@fees_paid]</td>
            <td valign="middel">[@reg_paid]</td>
            <td valign="middel">[@total_paid]</td>
          </tr>
          <tr>
          	<td>[#refund]</td>
            <td class="reverse_align" valign="middel" width="120">
            	<input type="text" name="fees" value="[@fees_paid]" />
            </td>
            <td valign="top">
            	<input type="text" name="reg" value="[@reg_paid]" />
             </td>
            <td valign="top">
            	<span class="total_refund">[@total_paid]</span>
             </td>
          </tr>
          
		</tbody>
	</table>    
</form>   