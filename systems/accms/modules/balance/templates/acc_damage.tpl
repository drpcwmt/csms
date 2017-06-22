<div class="toolbox">
    <a action="print_pre" rel="#acc_damage_f-[@full_code]" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
</div>
<form id="acc_damage_f-[@full_code]">
   <!-- <input type="hidden" name="main_code" value="[@main_code]" />
    <input type="hidden" name="sub_code" value="[@sub_code]" />-->
    <h2>[@title]</h2>
    <h2>[@full_code]</h2>
    <h3>[#percent]: [@damage] %</h3>
    <fieldset>
    	<table class="tableinput">
        	<thead>
            	<tr>
                	<th width="100">[#value]</th>
                    <th width="120">[#date]</th>
                    <th>[#notes]</th>
                    <th width="100">[#period]</th>
                    <th width="100">[#currency]</th>
                    <th width="100">[#value]</th>
                    <th width="100">[#total]</th>
                 </tr>
            </thead>
            <tbody>
            	[@trs]
            </tbody>
            <tfoot>
            	<tr>
                	<th colspan="4">[#total] [#year] [@cur_year]</th>
                    <th width="100">EGP</th>
                    <th width="100">EGP</th>
                    <th width="100">[@total]</th>
                 </tr>
            	[@damage_start_trs]
            	<tr>
                	<th colspan="4">[#total]</th>
                    <th width="100">EGP</th>
                    <th width="100">EGP</th>
                    <th width="100">[@final_total]</th>
                 </tr>
            </tfoot>
        </table>
    </fieldset>
</form>