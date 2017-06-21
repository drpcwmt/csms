<div class="std_income_div">
    <div class="ui-state-highlight ui-corner-all">
        <table border="0" cellspacing="0" width="100%">
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#fees]</label>
                </td>
                <td valign="top">
                   <select class="combobox" name="feec_acc" update="filterIncomes">
                        [@fees_accs_opts]
                    </select>
                </td>
              </tr>  
         </table>             
    </div>
    <h2 class="title">[#total]: [@total_paid]</h2>
    <table class="result">
        <thead>
            <th width="80">[#paid]</th>                
            <th width="80">[#refund]</th>                
            <th width="80">[#date]</th>
            <th width="80">[#recete_no]</th>
            <th width="60">[#payment_type]</th>
            <th>[#title]</th>
            <th>[#user]</th>
        </thead>
        <tbody>
            [@trs]
        </tbody>
    </table>
</div>