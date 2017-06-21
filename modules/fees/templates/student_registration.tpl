<table width="100%">
	<tr>
    	<td width="50%" valign="top">
        	<fieldset>
            	<legend>Nov.</legend>
                <table class="tablesorter">
                	<thead>
                    	<tr>
                        	<th>[#material]</th>
                            <th width="50">[#fees]</th>
                            <th width="50">Edex</th>
                            <th width="50">Camb</th>
                            <th width="20">[#refund]</th>
                        </tr>
                    </thead>
                    <tbody>
	        			[@nov_trs]
                    </tbody>
                    <tfoot>
                    	<tr>
                        	<th>[#total]</th>
                            <th align="center">[@nov_total_fees]</th>
                            <th align="center">[@nov_reg_edex]</th>
                            <th align="center">[@nov_reg_camb]</th>
                            <th>&nbsp;</th>
                        </tr>
                    	<tr>
                        	<th>[#paid]</th>
                            <th align="center">[@nov_total_fees]</th>
                            <th align="center">[@nov_reg_paid_edex]</th>
                            <th align="center">[@nov_reg_paid_camb]</th>
                            <th align="center">&nbsp;</th>
                        </tr>
                        [@nov_safems_tr]
                    </tfoot>
                </table>
            </fieldset>
            <fieldset>
            	<legend>Jan.</legend>
                <table class="tablesorter">
                	<thead>
                    	<tr>
                        	<th>[#material]</th>
                            <th width="50">[#fees]</th>
                            <th width="50">Edex</th>
                            <th width="50">Camb</th>
                            <th width="20">[#refund]</th>
                        </tr>
                    </thead>
                    <tbody>
	        			[@jan_trs]
                    </tbody>
                    <tfoot>
                    	<tr>
                        	<th>[#total]</th>
                            <th align="center">[@jan_total_fees]</th>
                            <th align="center">[@jan_reg_edex]</th>
                            <th align="center">[@jan_reg_camb]</th>
                            <th>&nbsp;</th>
                        </tr>
                    	<tr>
                        	<th>[#paid]</th>
                            <th align="center">[@jan_total_fees]</th>
                            <th align="center">[@jan_reg_paid_edex]</th>
                            <th align="center">[@jan_reg_paid_camb]</th>
                            <th>&nbsp;</th>
                        </tr>
                        [@jan_safems_tr]
                   </tfoot>
                </table>
            </fieldset>

        	<fieldset>
            	<legend>Jun.</legend>
                <table class="tablesorter">
                	<thead>
                    	<tr>
                        	<th>[#material]</th>
                            <th width="50">Edex</th>
                            <th width="50">Camb</th>
                            <th width="20">[#refund]</th>
                        </tr>
                    </thead>
                    <tbody>
	        			[@jun_trs]
                    </tbody>
                    <tfoot>
                    	<tr>
                        	<th>[#total]</th>
                            <th align="center">[@jun_reg_edex]</th>
                            <th align="center">[@jun_reg_camb]</th>
                            <th>&nbsp;</th>
                        </tr>
                    	<tr>
                        	<th>[#paid]</th>
                            <th align="center">[@jun_reg_paid_edex]</th>
                            <th align="center">[@jun_reg_paid_camb]</th>
                            <th>&nbsp;</th>
                        </tr>
                        [@jun_safems_tr]
                    </tfoot>
                </table>
            </fieldset>
        </td>
    </tr>
</table>    