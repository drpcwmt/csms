function addAddressBook($btn){
	var con=$btn.attr('con');
	var conId = $btn.attr('con_id');
	var sysId = $btn.attr('sys_id');
	var module = {
		name: 'addressbook',
		data: 'new&con='+con+'&con_id='+conId+'&sys_id='+sysId,
		title: getLang('addressbook'),
		div: "new_addressbook",
		callback:function(){
			if(configFile.MapsApiKey && typeof google === 'object' && typeof google.maps === 'object'){
			//	setAutoAddressFileds("#new_addressbook");
				 var options = {
				  map: "#addressbook-new .map-canvas",
				  country: 'eg',
				  markerOptions: {
					draggable: true
				  }
				};
				autocompleteAddress($("#addressbook-new #address_ar"), options);
			}
		}
	}
	
	dialogOpt = {
		buttons: [{ 
			text: getLang('save'), 
			modal:true,
			click: function() { 
				if(validateForm('#new_addressbook form')){
					var submitSave = {
						name: 'addressbook',
						param: 'save&sys_id='+sysId,
						post: $('#new_addressbook form').serialize(),
						callback: function(answer){
							var $fieldset = $btn.parents('.addressbook_holder').eq(0);
							var $ul = $fieldset.find('ul');
							$ul.append('<li item_id="'+answer.id+'" class="hoverable ui-state-default ui-corner-all" style="border: 1px solid #CCC"><span>'+$('#new_addressbook input[name="address_ar"]').val()+'</span> <a class="rev_float ui-state-default hoverable mini_circle_button" module="addressbook" action="deleteAddressBook" sys_id="'+sysId+'" rel="'+answer.id+'"><span class="ui-icon ui-icon-trash"></span></a></li>');
							iniButtonsRoles();
							$('#new_addressbook').dialog('close');
						}
					}
					getModuleJson(submitSave);
				} else {return false;}
	
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		width:900,
		height:470,
		minim:false
	}
	openAjaxDialog(module, dialogOpt);	
}


function editAddressBook($btn){
	var $form = $btn.parent('form');
	var addressId = $btn.attr('rel');
	var sysId = $btn.attr('sys_id');
	var module = {
		name: 'addressbook',
		data: 'edit&address_id='+addressId+'&sys_id='+sysId,
		title: getLang('addressbook'),
		div: "addressbook-"+addressId,
		callback:function(){
			//setAutoAddressFileds("#addressbook-"+addressId);
			var latlng = new google.maps.LatLng(30.160, 31.439);
			var options = {
			  map: "#addressbook-"+addressId+" .map-canvas",
			  type: ['street_address route intersection political'],
//			  blur: true,//	
//				geocodeAfterResult: false,
//				restoreValueAfterBlur: true,
			  country: 'eg',
			  center: latlng,
//			  details: "#addressbook-"+addressId+" form",
//			  detailsAttribute: "data-geo",
			  zoom: 18,
			  markerOptions: {
				draggable: true
			  },
			};
			autocompleteAddress($("#addressbook-"+addressId+" #address_ar"), options);			
		}
	}
	
	dialogOpt = {
		buttons: [{ 
			text: getLang('save'), 
			modal:true,
			click: function() { 
				var dialog = "#addressbook-"+addressId;
				if(validateForm(dialog+' form')){
					var submitSave = {
						name: 'addressbook',
						param: 'save&sys_id='+sysId,
						post: $(dialog+' form').serialize(),
						callback: function(answer){
							var $li = $btn.parent('li');
							var $ul = $li.parent('ul');
							$li.hide().remove();
							$ul.append('<li item_id="'+answer.id+'" class="hoverable ui-state-default ui-corner-all" style="border: 1px solid #CCC"><a class="rev_float ui-state-default hoverable mini_circle_button" module="addressbook" action="deleteAddressBook" sys_id="'+sysId+'" rel="'+answer.id+'"><span class="ui-icon ui-icon-trash"></span></a><a class="rev_float ui-state-default hoverable mini_circle_button" module="addressbook" action="editAddressBook" sys_id="'+sysId+'" rel="'+answer.id+'"><span class="ui-icon ui-icon-pencil"></span></a><div align="right">'+$(dialog+' input[name="address_ar"]').val()+' - '+$(dialog+' input[name="region_ar"]').val()+' - '+$(dialog+' input[name="city_ar"]').val()+' - '+$(dialog+' input[name="country_ar"]').val()+'</div><div >'+$(dialog+' input[name="address"]').val()+' - '+$(dialog+' input[name="region"]').val()+' - '+$(dialog+' input[name="city"]').val()+' - '+$(dialog+' input[name="country"]').val()+'</div> </li>');
							iniButtonsRoles();
							$('#addressbook-'+addressId).dialog('close');
						}
					}
					getModuleJson(submitSave);
				} else {return false;}
	
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		width:900,
		height:470,
		minim:false
	}
	openAjaxDialog(module, dialogOpt);	
}

function autocompleteAddress($input, options){
	var srch = false;
	var $form = $input.parents('form');
	if($form.find('input[name="lng"]').val() != ''){
		var latlng = new google.maps.LatLng(parseFloat($form.find('input[name="lat"]').val()) , parseFloat($form.find('input[name="lng"]').val()));
		alert(parseFloat($form.find('input[name="lat"]').val()) );
		options.center = latlng;
	  //	options.markerOptions.position = latlng;
	}
	$input.geocomplete(options).bind("geocode:result", function(event, result){
		var name = result['address_components'][0]['long_name'] +' '+result['address_components'][1]['long_name'];
		var region = result['address_components'][2]['long_name'];
		var city = result['address_components'][3]['long_name'];
		var locality ='';
		for(x=4; x<result['address_components'].length; x++){
			locality += result['address_components'][x]['long_name']+' ' ;
		}
		$form.find('*[name="address_ar"]').val(name)
		$form.find('*[name="city_ar"]').val(city);
		$form.find('*[name="region_ar"]').val(region);
		$form.find('*[name="country_ar"]').val(locality);
		$form.find('*[name="lat"]').val(result.geometry.location.lat);
		$form.find('*[name="lng"]').val(result.geometry.location.lng);
		srch =true;
		
	 }).
	 bind("geocode:dragged", function(event, latLng){
	  $("input[name='lat']").val(latLng.lat());
	  $("input[name='lng']").val(latLng.lng());
	});
	
			

	/*if($input.val() != ''){
		 $input.trigger("geocode");
	}*/
}

function searchMap($btn){
	var $form =$btn.parents('form');
	var $input = $form.find('input[name="address_ar"]');
	$input.trigger("geocode")
}

function deleteAddressBook($btn){
	var id=$btn.attr('rel');
	var sysId = $btn.attr('sys_id');
	var deletePhone = {
		name: 'addressbook',
		param: 'delete&sys_id='+sysId,
		post: 'id='+id,
		callback: function(answer){
			var $li = $btn.parents('li').eq(0);
			$li.fadeOut().remove();
		}
	}
	getModuleJson(deletePhone);
}

function setAutoAddressFileds(dialog){
	var $dialog= $(dialog);
	$dialog.find('form .ena_auto').focus(function(){
		setDefAutocomplete($(this), configFile.DB_student, 'addressbook');	
		$(this).autocomplete('search');
	});
}


function copyAddressBook($btn){
	var con = $btn.attr('con');
	var conId=$btn.attr('con_id');
	var sysId=$btn.attr('sys_id');
	var option = '';
	if(con == 'student'){
		option += '<option value="father">'+getLang('father')+'</option>';
		option += '<option value="mother">'+getLang('mother')+'</option>';
	} else if(con == 'father'){
		option += '<option value="mother">'+getLang('mother')+'</option>';
	} else if(con == 'mother'){
		option += '<option value="father">'+getLang('father')+'</option>';
	}
	var html = '<form><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px;">'+getLang('from')+': </label></td><td><select name="from">'+option+'</select></td></tr></table></form>';
	var dialogOpt = {
		width:350,
		height:180,
		div:'copy_address',
		title:getLang('address'),
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var copyPhone = {
					name: 'addressbook',
					param: 'copy&con='+con+'&con_id='+conId+'&sys_id='+sysId,
					post: 'from='+$('#copy_address select').val(),
					callback: function(answer){
						var $fieldset = $btn.parents('.addressbook_holder').eq(0);
						var $ul = $fieldset.find('ul');
						$ul.replaceWith(answer.html);
						$('#copy_address').dialog('close');
					}
				}
				getModuleJson(copyPhone);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
	
}
	