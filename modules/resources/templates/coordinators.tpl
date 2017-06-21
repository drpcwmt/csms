<div class="tabs">
    <ul>
        <li><a href="#coordinators-infos-[@id]">[#info]</a></li>
        <li><a href="index.php?module=employers&id=[@id]">[#personel_infos]</a></li>
   </ul>
   <div id="coordinators-infos-[@id]"> 
        <h2 class="title">[@coordinator_name]</h2> 
        [@toolbox]
        <table class="tablesorter">
			<thead>
				<tr>
					<th width="20" class="{sorter:false} unprintable [@level_read_hidden]">&nbsp;</th>
					<th width="20" class="{sorter:false} unprintable [@editable_hidden]">&nbsp;</th>
                    <th>[#level]</th>
                </tr>
            </thead>
            <tbody>
            	[@level_trs]
            </tbody>
        </table>
    </div>
</div>
