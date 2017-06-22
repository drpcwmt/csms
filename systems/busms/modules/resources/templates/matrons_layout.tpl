<h2 id="matron_name_title" class="title"> [@name] </h2>
<div class="tabs">
  <ul>
      <li><a href="#routes">[#routes]</a></li>
        <li><a href="#personel_info">[#personel_info]</a></li>
    </ul>
    <div id="routes">
        <div class="toolbox">
            <a action="deleteMatron" matron_id=[@id]> [#delete] <span class="ui-icon ui-icon-close"></span></a>
            <a action="print_but" rel="#matrons_list"> [#print] <span class="ui-icon ui-icon-print"></span></a>
        </div>

        <div class="route_table">
          [@routes]
        </div>
    </div>
    <div id="personel_info">
      [@personel_info]
    </div>
</div>