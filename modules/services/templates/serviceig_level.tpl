<h2 class="hidden showforprint">[@level_name]</h2>
<h2 class="hidden showforprint">[@lvl]-[@level_year']</h2>
<table class="tablesorter">
	<thead>
    	<tr>
        	<th class="unprintable {sorter:false}" rowspan="3" width="20">&nbsp;</th>
            <th rowspan="3" width="30" ><span class="rotated">[#cand_no]</span></th>
            <th rowspan="3" width="120">[#name]</th>
            <th colspan="[@OL_th_colspan]" class="{sorter:false} [@OL_th_hidden]" width="[@OL_th_width]">OL</th>
            <th colspan="[@AS_th_colspan]" class="{sorter:false} [@AS_th_hidden]" width="[@AS_th_width]">AS</th>
            <th colspan="[@A2_th_colspan]" class="{sorter:false} [@A2_th_hidden]" width="[@A2_th_width]">A2</th>
            <th colspan="[@AL_th_colspan]" class="{sorter:false} [@AL_th_hidden]" width="[@AL_th_width]">AL</th>
       </tr>
       <tr>
       		[@OL_ths]
            [@AS_ths]
            [@A2_ths]
            [@AL_ths]
       </tr>
       <tr>
       		[@OL_total]
            [@AS_total]
            [@A2_total]
            [@AL_total]
       </tr>
    </thead>
    <tbody>
    	[@students_trs]
    </tbody>
</table>