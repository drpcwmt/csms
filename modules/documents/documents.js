// Documets Js
loadModuleJS('upload');

function initDocumentModule(attachement){
	var $mainDiv
	if(!attachement){
		if(window.docClipboard && window.docClipboard != '') {
			$('#paste_but').show();
		}
		$mainDiv = $('#module_documents');
	} else {
		$mainDiv = $('#MS_dialog_documents');
		$mainDiv.find('div.ui-widget-header:first').hide();
		$mainDiv.find('.toolbox a[action="downloadFiles"]').hide();
		$mainDiv.find('.toolbox a[action="createNewDir"]').hide();
		$mainDiv.find('.toolbox a[action="cutFile"]').hide();
		$mainDiv.find('.toolbox a[action="pasteFile"]').hide();
		$mainDiv.find('.toolbox a[action="renameFile"]').hide();
		$mainDiv.find('.toolbox a[action="shareFile"]').hide();
		$mainDiv.find('.toolbox a[action="deleteFiles"]').hide();
	}
	
	$mainDiv.find('#explorer_form input[type="checkbox"]').click(function(){
		if($mainDiv.find('#explorer_form input:checked').length > 0){
			$('#browser_td .toolbox a.click-disabled').removeClass('click-disabled').addClass('click-enabled');
		} else {
			$('#browser_td .toolbox a.click-enabled').removeClass('click-enabled').addClass('click-disabled');
		}
	});

	var sizeValue = $( "#sizeDiv").attr('sizevalue');
	$mainDiv.find( "#sizeDiv" ).progressbar({
		value: parseInt(sizeValue)
	});
	$mainDiv.find('#library_list li').click(function(){
		$('#library_list li').removeClass('ui-state-active');
		$(this).addClass('ui-state-active');
	});
	
	$mainDiv.find('#explorer_form li.item').hover(
		function(){
			if(attachement == true){
				if($(this).find('a[action="openFile"]').length > 0){
					$(this).addClass('ui-state-hover');
					$(this).find('ul').slideDown();
				}
			} else {
				$(this).addClass('ui-state-hover');
				$(this).find('ul').slideDown();
			}
		},
		function(){
			$(this).removeClass('ui-state-hover');
			$(this).find('ul').slideUp(); 
		}
	);
	$mainDiv.find('#explorer_form .file_tools input:checkbox').click(function(){
		if($(this).attr('checked') == 'checked'){
			$(this).parents('li.item').addClass('ui-state-active');
		} else {
			$(this).parents('li.item').removeClass('ui-state-active');
		}
	});
}

function openMyDocument(){
	var module = {};
	module.name = "documents";
	module.data = 'type=mydoc';
	module.title = getLang('documents');
	module.div = "#browser_td";
	module.callback = function(){
		initDocumentModule();
	}
	loadModule(module);
}

function openSharedFiles(){
	var module = {};
	module.name = "documents";
	module.data = 'type=shared';
	module.title = getLang('shares');
	module.div = "#browser_td";
	module.callback = function(){
		initDocumentModule();
	}
	loadModule(module);
}

function openLibrarys($but){
	var libId = $but.attr('libid');
	var module = {};
	module.name = "documents";
	module.data = 'type=lib&lib='+libId;
	module.title = getLang('documents');
	module.div = "#browser_td";
	module.callback = function(){
		initDocumentModule();
	}
	loadModule(module);
}

function openServiceFiles($but){
	var serviceId = $but.attr('serviceid');
	var module = {};
	module.name = "documents";
	module.data = 'type=services&service_id='+serviceId;
	module.title = getLang('documents');
	module.div = "#browser_td";
	module.callback = function(){
		initDocumentModule();
	}
	loadModule(module);
}

/************ Dynamic functions *************/
function browseDir($but){
	if($but.attr('path')){
		var Path = $but.attr('path');
	} else {
		var $item = $but.parents('.item');
		var Path = $item.find('input').val();
	}

	var type = $('#explorer_form #doc_type').val();
	
	var extra = '';
	if(type == 'lib'){
		extra = '&lib='+$('#libid').val();
	} else if( type == 'services'){
		extra = '&service_id='+$('#serviceid').val();
	}
	var view = $('#doc_view').val();
	var module = {};
	module.name = "documents";
	module.data = 'dir='+Path+'&type='+type+extra+'&view='+view;
	module.title = getLang('documents');
	module.div = "#browser_td";
	module.callback = function(){
		initDocumentModule();
	}
	loadModule(module);
}

function reloadCurrent(){
	var curDir = $('#cur_folder').val();
	var view = $('#doc_view').val();
	var type = $('#explorer_form #doc_type').val();
	var extra = '';
	if(type == 'lib'){
		extra = '&lib='+$('#libid').val();
	} else if( type == 'services'){
		extra = '&service_id='+$('#serviceid').val();
	}
	var module = {};
	module.name = "documents";
	module.data = 'dir='+curDir+'&type='+type+extra+'&view='+view;
	module.title = getLang('documents');
	module.div = "#browser_td";
	module.callback = function(){
		initDocumentModule();
	}
	loadModule(module);
}

function setView($but){
	var viewType = $but.attr('viewtype');
	$('#doc_view').val(viewType);
	reloadCurrent();
}

function openFile($but){
	var type = $but.attr('type');
	var path = $but.attr('rel');
	var height = 480;
	var width = 600;
	var dialogId = 'MS_dialog_openFile' + new Date().getTime();
	var $dialogId = $('#'+dialogId);
	
	if(checkIfMobile()){
		window.open(path);
	} else {
		if(type == 'img'){
			var html = '<img src="'+path+'"  width="100%" height="100%" />';
		} else if( type == 'html'){
			width = '90%';
			height = '640';
			var html = '<ifram src="'+path+'"  width="100%" height="100%" ></ifram>';
		} else if( type == 'ppt'){
			var a  = document.createElement('a');
         	a.href = window.location.href;
			width = '90%';
			height = '640';
			var html = '<ifram src="http://docs.google.com/gview?url=http://'+a.hostname+'/'+path+'&embedded=true"  width="100%" height="100%" ></ifram>';
		} else if( type == 'pdf'){
			width = '90%';
			height = '640';
			var html = '<ifram src="'+path+'" width="100%" height="100%"  >'+
				'<html>'+
					'<body style="background: transparent url(assets/img/loading_mini.gif) no-repeat">'+
						'<object data="'+path+'" type="application/pdf" width="100%" height="100%" style="background: transparent url(assets/img/loading_mini.gif) no-repeat">'+
							'<embed src="'+path+'" type="application/pdf" width="100%" height="100%" />'+
							 '<img src="assets/img/loading_mini.gif" />'+
						'</object>'+
					'</body>'+
				'</html>'+
			'</ifram>';
		} else if( type.indexOf('video') == 0) {
			$('#MS_dialog_openFile').remove('video');
			var html = '<div style="align-text:center; height:inherit"><video  height="100%" autoplay controls poster="assets/img/loading_mini.gif">'+
			  '<source src="'+path+'" type="'+type+'">'+
			'</video></div>';
		} else {
			window.open('index.php?module=documents&download&file[]='+path,'download','');
			return false;
		}
		var dialogOpt = {
			width:width,
			height:height,
			div: dialogId,
			title:getLang('open'),
			minim:true,
			maxim:true,
			buttons : [{ 
				text: getLang('close'), 
				click: function() { 
					if($dialogId.find('video').length > 0){
						$dialogId.find('video').each(function(){
							$(this)[0].pause();
							//$(this).remove();
						});
						$dialogId.empty();						
					}
					$(this).dialog('close');
					$(this).dialog("destroy").remove();
				}
			}]
		}
	
		openHtmlDialog(html, dialogOpt)	
	}
}

function downloadFiles(){
	if($('#explorer_form input:checked').length > 0){
		window.open('index.php?module=documents&download&'+$('#explorer_form').serialize(),'download','');
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error-at_leat_one_file')+'</h2>');
	}
}

function deleteFiles(){
	if($('#explorer_form input:checked').length > 0){
		var html = '<fieldset class="ui-corner-all ui-state-highlight">'+getLang('cfm_delete_file')+'</fieldset>';
		var dialogOpt = {
			width:300,
			height:150,
			div:'MS_dialog_deleteFileCfm',
			title:getLang('delete_file'),
			buttons : [{ 
				text: getLang('yes'), 
				click: function() {
					var module = {
						name: 'documents',
						param:'delete',
						post: $('#explorer_form').serialize(),
						callback: function(){
							$('#MS_dialog_deleteFileCfm').dialog('close');
							$('#explorer_form input:checked').each(function(){
								var $item = $(this).parents('.item');
								$item.fadeOut().remove();
							});
							reloadSpaceBar();
							//reloadCurrent();
						}
					}
					getModuleJson(module);
				}
			}, { 
				text: getLang('no'), 
				click: function() { 
					$(this).dialog('close');
				}
			}]
		}
	
		openHtmlDialog(html, dialogOpt)	
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error-at_leat_one_file')+'</h2>');
	}
}

function createNewDir(){
	var curDir = $('#cur_folder').val();
	var type = $('#doc_type').val();
	var extra = '';
	if(type == 'lib'){
		extra = '&lib='+$('#libid').val();
	} else if( type == 'services'){
		extra = '&service_id='+$('#serviceid').val();
	}
	var html = '<table border="0" cellspacing="0"><tbody><tr><td><label style="width:100px; float:left" class="label reverse_align ui-widget-header ui-corner-left">'+getLang('name')+': </label></td><td><input type="text" id="newFolderName"  class="input_double" value="New folder" /></td></tr></tbody></table>';

	var dialogOpt = {
		width:450,
		height:150,
		div:'MS_dialog_createFolder',
		title:getLang('new_folder'),
		buttons : [{ 
			text: getLang('create'), 
			click: function() {
				var module = {
					name: 'documents',
					param:'newdir&type='+type+extra,
					post: 'new='+$('#newFolderName').val()+'&dir='+curDir,
					callback: function(){
						$('#MS_dialog_createFolder').dialog('close');
						var view = $('#doc_view').val();
						var newFolderName = $('#newFolderName').val();
						if(view == 'icon'){
							var newItem = '<li class="item file_name ui-corner-all ui-state-default hoverable hand" title="'+newFolderName+'"><ul class="file_tools hidden" style="display: none;"><li><input type="checkbox" name="folder[]" value="'+curDir+'/'+newFolderName+'" class="ui-corner-right"></li></ul><a action="browseDir"><img src="assets/img/filemanger_icons/folder.png" border="0" height="60" width="60"> <br><span class="filename">'+newFolderName+'</span></a></li>';
						} else {
							var newItem = '<tr class="item"><td style="border: 0px;"><input type="checkbox" name="folder[]" value="'+curDir+'/'+newFolderName+'" class="ui-corner-right"></td>'+(type!='lib'&& type!='services' ? '<td style="border: 0px;">&nbsp;</td>' :'')+'<td style="border: 0px;"><button class="ui-state-default hoverable circle_button" action="downloadFiles" title="Download"><span class="ui-icon ui-icon-circle-arrow-s"></span></button></td><td class="" style="border: 0px;"><button class="ui-state-default hoverable circle_button MS_formed" action="browseDir"><span class="ui-icon ui-icon-extlink"></span></button></td><td style="border: 0px;"><button class="ui-state-default hoverable circle_button" action="deleteDir" title="Delete"><span class="ui-icon ui-icon-close"></span></button></td><td style="border: 0px;"> <a href="#" class="file_name" action="browseDir"><img src="assets/img/filemanger_icons/folder.png" border="0" height="24" width="24" style="vertical-align:middle;"> <span class="filename">'+newFolderName+'</span></a></td><td style="border: 0px;"></td><td style="font-size: 8px; border: 0px;" class="">0Byte</td></tr>';
						}
						$("#explorer_form .items_contener").prepend(newItem);
						initiateJquery();
						initDocumentModule();
					//	reloadCurrent();
					}
				}
				getModuleJson(module);
			}
		}, { 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}

	openHtmlDialog(html, dialogOpt)	
}

function renameFile(){
	if($('#explorer_form input[type="checkbox"]:checked').length == 1){
		var curDir = $('#cur_folder').val();
		var type = $('#doc_type').val();
		var extra = '';
		if(type == 'lib'){
			extra = '&lib='+$('#libid').val();
		} else if( type == 'services'){
			extra = '&service_id='+$('#serviceid').val();
		}
		var fileLink = $('#explorer_form input[type="checkbox"]:checked').val();
		var $contener =  $('#explorer_form input[type="checkbox"]:checked').parents('.item');
		var oldFilename = $contener.find('span.filename').html();
		var html = '<table border="0" cellspacing="0"><tbody><tr><td><label style="width:100px; float:left" class="label reverse_align ui-widget-header ui-corner-left">'+getLang('name')+': </label></td><td><input type="text" id="newName" class="input_double" value="'+oldFilename+'" /></td></tr></tbody></table>';

		var dialogOpt = {
			width:450,
			height:150,
			div:'MS_dialog_renameFile',
			title:getLang('rename'),
			buttons : [{ 
				text: getLang('rename'), 
				click: function() {
					var module = {
						name: 'documents',
						param:'rename&type='+type+extra,
						post: 'new='+$('#newName').val()+'&link='+fileLink,
						callback: function(answer){
							$('#MS_dialog_renameFile').dialog('close');
							 	var b = answer.path
								var c = b.split('/');
								var filename = c[c.length-1];
								if(oldFilename.indexOf('.') < 0){
									var d =filename.split('.');
									var extension = d[d.length-1];
									oldFilename += '.'+extension;
								}
								
							$contener.find('span.filename').html($('#newName').val());
							// Titel:
							if($contener.attr('title')){
								var oldTitle = $contener.attr('title')
								$contener.attr('title', oldTitle.replace(oldFilename, $('#newName').val()));
							}
							// a rel
							var $a = $contener.find('a[action="openFile"]');
							if($a.attr('rel')){
								var oldRel = $a.attr('rel');
								$a.attr('rel', oldRel.replace(oldFilename, filename));
							}
							// change the check box value if it a folder
							if(filename.indexOf('.')<0){
								$checkbox = $('#explorer_form input[type="checkbox"]:checked');
								var oldVal = $checkbox.val();							
								$checkbox.val( oldVal.replace(oldFilename, $('#newName').val()));
							}
							//reloadCurrent();
						}
					}
					getModuleJson(module);
				}
			}, { 
				text: getLang('cancel'), 
				click: function() { 
					$(this).dialog('close');
				}
			}]
		}
	
		openHtmlDialog(html, dialogOpt)	
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error-select-one_file')+'</h2>');
	}
}

function cutFile(){
	if($('#explorer_form input[type="checkbox"]:checked').length > 0){
		var links = new Array;
		$('#explorer_form input[type="checkbox"]:checked').each(function(){
			links.push($(this).val());
		})
		window.docClipboard = links.join(',');
		MS_alert('<h2><img src="assets/img/cut.png" width="48" /> '+getLang('cut')+' </h2>');
	} else return false;
}

function pasteFile(){
	var links = window.docClipboard;
	if(links != '' ){
		MS_alert('<h2><img src="assets/img/paste.png" width="48" /> '+getLang('paste')+' </h2>');
		var curDir = $('#cur_folder').val();
		var type = $('#doc_type').val();
		var extra = '';
		if(type == 'lib'){
			extra = '&lib='+$('#libid').val();
		} else if( type == 'services'){
			extra = '&service_id='+$('#serviceid').val();
		}
		var module = {
			name: 'documents',
			param:'move&type='+type+extra,
			post: 'links='+links+'&dir='+curDir,
			callback: function(){
				reloadCurrent();
				reloadSpaceBar();
			}
		}
		getModuleJson(module);
		window.docClipboard = '';
		
	} else {
		MS_alert('<h2><img src="assets/img/error.png" width="48" /> '+getLang('clipboard_is_empty')+' </h2>');
	}
}

/*********** File Share ************/
function shareFile($but){
	if($but.attr('link')){
		var posts = 'file[]='+$but.attr('link');
	} else {
		var posts = $('#explorer_form').serialize();
	}
	var module ={};
	module.name = 'documents';
	module.data = 'share&'+posts;
	module.title = getLang('share');
	module.div = 'MS_dialog-shares';
	
	var buttons = [{ 
		text: getLang('add'), 
		click: function() { 
			openSelectDialog();
		}
	},{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];

	createAjaxDialog(module, buttons, false, 700, 550, false, '')	
}

function openSelectDialog(){
	var module ={};
	module.name = 'students';
	module.data = 'stdfp';
	module.title = getLang('students');
	module.div = 'MS_dialog-shares_students';
	
	var buttons = [{ 
		text: getLang('share'), 
		click: function() { 
			var links = $('#shared_links').val();
			MS_jsonRequest('documents&share', 'link='+links+'&'+$('#MS_dialog-shares_students form').serialize(), "$('#MS_dialog-shares_students').dialog('close');reloadShares();");
		}
	},{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 600, 500, false, '')	
}

function reloadShares(){
	var links = $('#shared_links').val();
	if(links.indexOf(',') > -1){
		var posts_arr = new Array();
		var l = links.split(',');
		var posts ='';
		for(x=0;x<l.length; x++){
			posts_arr.push('file[]='+l[x]);	
		}
		var posts = posts_arr.join('&');
	} else {
		var posts = 'file[]='+links;		
	}
	var module = {};
	module.name = "documents";
	module.data = 'share&reload&'+posts;
	module.title = getLang('share');
	module.div = "#shares_table";
	loadModuleToDiv(new Array(module), "");
}

function removeShare(Link, con, conId){
	MS_jsonRequest('documents&share&delshare', 'link='+Link+'&con='+con+'&con_id='+conId, "reloadShares();");
}

/*********************** Librarys *********************/
function addNewLibrary(){
	var module ={};
	module.name = 'documents';
	module.data = 'type=lib&edit';
	module.title = getLang('file_libarays');
	module.div = 'MS_dialog-libararys';
	
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			if(validateForm('#lib_form') != false ){
				var module = {
					name: 'documents',
					param:'type=lib&save',
					post: $('#lib_form').serialize(),
					callback: function(){
						$('#MS_dialog-libararys').dialog('close');
						$("#library_list").prepend('<li action="openLibrarys" libid="'+libId+'" class="hand ui-state-default ui-corner-all clickabel hoverable">'+$('#new_lib_name').val()+'<a action="editLib" libid="'+libId+'" class="mini_circle_button rev_float ui-state-default  hoverable"><span class="ui-icon ui-icon-pencil"></span></a></li>');
						initiateJquery();
					}
				}
				getModuleJson(module);
			}
		}
	},{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 800, 600, false, '')	
}

function editLib($but){
	var libId = $but.attr('libid');
	var module ={};
	module.name = 'documents';
	module.data = 'type=lib&edit&lib_id='+libId;
	module.title = getLang('file_libarays');
	module.div = 'MS_dialog-libararys';

	var dialogOpt = {
		width:800,
		height:600,
		div:'MS_dialog-libararys',
		title:getLang('new_folder'),
		buttons : [{ 
			text: getLang('save'), 
			click: function() {
				if(validateForm('#lib_form') != false ){
					var module = {
						name: 'documents',
						param:'type=lib&save',
						post: $('#lib_form').serialize(),
						callback: function(){
							$('#MS_dialog-libararys').dialog('close');
							$("#library_list li.ui-state-active").replaceWith('<li action="openLibrarys" libid="'+libId+'" class="hand ui-state-default ui-corner-all clickabel hoverable">'+$('#new_lib_name').val()+'<a action="editLib" libid="'+libId+'" class="rev_float ui-state-default mini_circle_button hoverable"><span class="ui-icon ui-icon-pencil"></span></a></li>');
							initiateJquery();
						}
					}
					getModuleJson(module);
				}
			}
		},{ 
			text: getLang('delete'), 
			click: function() { 
				var module = {
					name: 'documents',
					param:'type=lib&deletelib',
					post: 'lib_id='+libId,
					callback: function(){
						$('#MS_dialog-libararys').dialog('close');
						$("#browser_td").html('');
						$("#library_list li").each(function(){
							if($(this).attr('libid') == libId){
								$(this).fadeOut().remove();
							}
						});
					}
				}
				getModuleJson(module);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	
	openAjaxDialog(module, dialogOpt)	
}

function toogleNext(a){
	var $legend = $(a).parent('legend');
	var $span = $legend.find('span');
	var $table = $legend.next();
	if($($span).hasClass('ui-icon-carat-1-s')){
		$table.slideDown();
		$span.removeClass('ui-icon-carat-1-s').addClass('ui-icon-carat-1-n');
	} else {
		$table.slideUp();
		$span.removeClass('ui-icon-carat-1-n').addClass('ui-icon-carat-1-s');
	}
}

function attachFile($but){
	var $scope = $but.parents('.scope');
	var $attachField = $scope.find('input.attachemets_field');
	var $attachTable = $scope.find('table.attachemets_table');
	
	var module ={};
	module.name= 'documents';
	module.title = getLang('attachements');
	module.type= 'GET';
	module.data= '';
	module.div = 'MS_dialog_documents';
	module.cache = false;
	module.callback = function(){
		initDocumentModule(true);
	}
	var dialogOptions = {
		width: 900,
		height: 600,
		minim: false,
		maxim: false,
		cache:false,
		modal:false,
		buttons:[{ 
			text: getLang('attach'), 
			click: function() { 
				var links_str = $attachField.val();
				var links = links_str.split(',');
				$('#explorer_form input:checked').each(function(){
					var thisLink = $(this).val();
					if(links.indexOf(thisLink) < 0){
						links.push(thisLink);
						var $fileItem = $(this).parents('.item');
						var title = $fileItem.find('.filename');
						var thumb = $fileItem.find('img');
						$attachTable.append('<tr><td width="20"><button class="ui-state-default circle_button hoverable" title="'+getLang('remove')+'" onclick="dettachFile(this,\''+thisLink+'\')"><span class="ui-icon ui-icon-close"></span></a></td><td width="20"><img src="'+thumb.attr('src')+'" width="24" /></td><td>'+title.html()+'</td></tr>');
					}
				});
				$attachField.val( links.join(','));
				$(this).dialog('close');
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	openAjaxDialog(module,dialogOptions);
}

function dettachFile(handler){
	var thisLink = handler.attr('link');
	var $scope = $(handler).parents('.scope');
	var $attachField = $scope.find('input.attachemets_field');

	var newLink = new Array;
	var $tr = $(handler).parents('tr:first');
	$tr.fadeOut().remove();
	
	var links = $attachField.val().split(',');
	for (x=0; x<links.length; x++){
		if(links[x] != '' && links[x] != thisLink) newLink.push(links[x]);
	}
	$attachField.val( newLink.join(','));
}


function filterFilesList(val){
	$('#explorer_form .item').each(function(){
		var name = $(this).find('span.filename').text();
		if(val != ''){
			nameLower = name.toLowerCase();
			if(name.indexOf(val) >= 0 || nameLower.indexOf(val) >= 0){
				$(this).fadeIn();
			} else {
				$(this).fadeOut();
			}
		} else {
			$(this).fadeIn();
		}
	})
}

function reloadSpaceBar(){
	if($('#spaceBarHolder').length>0){
		var module = {};
		module.name = "documents";
		module.data = 'reloadspacebar';
		module.title = getLang('documents');
		module.muted = true,
		module.div = "spaceBarHolder";
		module.callback = function(){
			var sizeValue = $( "#sizeDiv").attr('sizevalue');
			$( "#sizeDiv" ).progressbar({
				value: parseInt(sizeValue)
			});	
		}
		loadModule(module);
	}
}
	
	