<div class="parcour_main">
    <h2 class="title">
      <span>[@route_name]</span>
    </h2>
    <div>
    	<div class="toolbox">
        	<a action="toogleAddParcourTd">[#add] <span class="ui-icon ui-icon-plus"></span></a>
        </div>
      <table id="tblAttachAttributes" width="100%" cellspacing="5">
        <tbody>
          <tr>
            <td class="add_parcour_td hidden"  width="40%">
              <ul id="unassigned_attributes" style="list-style:none; padding:0; margin:0; cursor:move" class="sortable list_menu">
                [@non_list]
              </ul>
            </td>
            <td valign="top">
            	<fieldset class="ui-widget-content">
                	<legend>[#parcour] <a action="openRouteMap" route_id="[@id]" rel="[@r]"><img src="assets/img/map.png" width="24" height="24" /></a></legend>
                  <ul id="assigned_attributes" class="sortable list_menu" style="list-style:none; padding:0; margin:0; cursor:move">
                    [@yes_list]
                  </ul>
                </fieldset>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
	

  <script type="text/javascript">
	$(document).ready(function () {
		$('table#tblAttachAttributes').find('ul.sortable').sortable({
			connectWith: 'ul.sortable'
		});
	});
	
	$.fn.extend({ 
	  getMaxHeight: function() {  
		  var maxHeight = -1;
			this.each(function() {     
			  var height = $(this).height();
			  maxHeight = maxHeight > height ? maxHeight : height;   
			}); 
			return maxHeight;
	  }
	});

	function setMenusDivHeight($attributeDivs) {
			return $attributeDivs.css('min-height', $attributeDivs.getMaxHeight());
	}
	
	setMenusDivHeight($('table#tblAttachAttributes').find('ul.sortable')).sortable({
	  connectWith: 'ul.sortable',
	  start: function( event, ui ) {
		setMenusDivHeight(ui.item.closest('table#tblAttachAttributes').find('ul.sortable'))
			.css('box-shadow', '0 0 10px blue');
	  },
	  stop: function( event, ui ) {
		setMenusDivHeight(ui.item.closest('table#tblAttachAttributes').find('ul.sortable')).css('box-shadow', '');
		ui.item.find('input').focus();
		$( "#assigned_attributes td.time_td").fadeIn();
		$( "#unassigned_attributes td.time_td").fadeOut();
	  }
	});
	
	// map
	function showAddressMap($btn) {
		/*if(markers.length > 0){
			clearMarkers();
		}*/
		results = getGeoCode($btn.parent('a').text(), function(locations){
			if(locations != false){
				map.setZoom(18);
				map.setCenter(locations[0].geometry.location);
				marker = addMarker(locations[0].geometry.location, true);
				//SetMapsInfoWindows(markers[0], $btn.attr('infos'));
				//if($btn.attr('info') != $btn.attr('info')!=''){
					content = $btn.attr('info');
					var mapinfowindow  = new google.maps.InfoWindow()
					google.maps.event.addListener(marker,'click', (function(marker,content,mapinfowindow){ 
						return function() {
							mapinfowindow.setContent(content);
							mapinfowindow.open(map,marker);
						};
					})(marker,content,mapinfowindow)); 
				//}
				
				google.maps.event.addListener(marker, 'dragend', function(evt){
					alert(evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) );
				});

				document.getElementById("map-display").setAttribute('style','visibility:visible;');
			}
		});
	}
	
	
</script>