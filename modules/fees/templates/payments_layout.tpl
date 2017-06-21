<div class="tabs">
	<ul>
    	<li><a href="#payment_table_tab">[#payments]</a></li>
        <li><a href="#payment_dates_tab">[#times]</a></li>
    </ul>
   	<div id="payment_table_tab">
    	<input type="hidden" name="payment_calendar" value="1" />
        <table class="tableinput">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    [@payments_ths]
                    <th>[#total]</th>
                </tr>
            </thead>
            <tbody>
                [@payments_rows]
            </tbody>
            <tfoot>
            	[@payments_tfoot]
            </tfoot>
        </table>
    
    </div>
    <div id="payment_dates_tab">
        [@dates_table]
	</div>
</div>