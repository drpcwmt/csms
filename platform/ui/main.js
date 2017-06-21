// main ui
	initiateJquery();
	$('.main_nav li:first').addClass('ui-corner-left');
	$('.main_nav li:last').addClass('ui-corner-right');
		
	// luanch in full screen
//	launchFullScreen(document.documentElement);
	
	// Windows height
	maximizeContent();
	window.onresize = function(event) {
		maximizeContent();
	};
	$("#dialog-extend-fixed-container").fadeIn();
	
	// main navigation bar	
	$('.main_nav li, .mainnav_loader').click(function(){
		var $selected = $(this);
		$('.main_nav li, .mainnav_loader').not($selected).each(function(){
			$(this).removeClass('ui-state-active');
			var module = $(this).attr('module');
			$('#'+module+'_menus').hide();
		});
		
		$selected.addClass('ui-state-active');
		if($selected.attr('module') != ''){
			var moduleName = $selected.attr('module');
			$('#'+moduleName+'_menus').show('slow').fadeIn('slow');
			var moduleTitle = $selected.text();
			var callback = function(){
				eval($selected.attr('after'));
			}
			loadMainModules( moduleName, moduleTitle, '',  $selected.attr('after'));
		}
	}).disableSelection();
	
	var oldHeight = $('#page_content').height();
	var pageScrolled = false;
	$("#page_content").on('scroll', function() {
		var newHeight = $('#page_content').height() + 45;
		//alert($(document).outerHeight()+'-'+$(screen).height())
		//if(parseInt($(document).outerHeight()) > parseInt($(window).height()) + 40){
			var toScrollHeight = 37;
			var pos = $("#page_content").scrollTop();
			if(pos >50 && pageScrolled== false){
				pageScrolled = true;
				$('#top_div').animate({'top':'-'+toScrollHeight+'px'}, 300);
				$("#page_content").animate({'margin-top':'48px'}, 300, 'linear', function(){$("#page_content").height(newHeight)});
				$("#main-logo").animate({'height':'40px'}, 300, 'linear');
				$('.main_nav li').animate({'height':'40px', 'width':'120px'}, 300, 'linear');
				$('.main_nav img').animate({'height':'30px', 'width':'30px', 'margin-top':'7px'}, 300, 'linear');
				$('.main_nav b').css('display', 'inline-block');
			} else if(pos <=50 &&pageScrolled ==true){
				pageScrolled = false;
				$('#top_div').animate({'top':'0px'}, 300, 'linear');
				$("#page_content").animate({'margin-top':'85px'}, 300, 'linear', function(){$("#page_content").height(oldHeight)});
				$("#main-logo").animate({'height':'80px'}, 300, 'linear');
				$('.main_nav li').animate({'height':'80px', 'width':'90px'}, 300, 'linear');
				$('.main_nav img').animate({'height':'60px', 'width':'60px', 'margin-top':'0px'}, 300, 'linear');
				$('.main_nav b').css({display: 'block'});
			}
		//}
	})
	
	
	chkServerConx(0);
	// start mesages count deamon
	if(configFile.MSEXT_msg == 1 ){
		getNewMsgCount();
	}


//************ functions ****************************************/
function maximizeContent(){
	var pageContentHeight = ($('body').innerHeight()- ($('#top_div').outerHeight() + $('#dialog-extend-fixed-container').outerHeight()));
	$('#page_content').css({'height': pageContentHeight});
	
}



function changeYear($inp){
	var curYear = $inp.val();
	serverConfig = window.configFile.MS_codeName;
	localStorage[serverConfig+".year"] = curYear;
	MS_jsonRequest('system&session', 'field=year&value='+curYear, function(){
		document.location ="index.php"}
	);
}

function changeCurClass($inp){
	var curClass = $inp.val();
	serverConfig = window.configFile.MS_codeName;
	localStorage[serverConfig+".class"] = curClass;
	MS_jsonRequest('system&session', 'field=cur_class&value='+curClass, function(){
		document.location ="index.php"}
	);
}

// New Year
function startNewYear(){
	if($('body').find('#new_year_wizard').length == 0){
		$('body').append('<div id="new_year_wizard" class="hidden">');
	}
	var buttons = [{ 
		text: getLang('next'), 
		click: function() { 
			nextYearStep();
		}
	},{ 
		text: getLang('cancel'), 
		click: function() {
			$("#new_year_wizard").dialog("close");
		}
	}];
	$("#new_year_wizard").dialog({
		autoOpen: false,
		height:600,
		width: 900,
		modal: true,
		title : getLang('new_year'),
		close : function(){
			if($('#wizard_step').val() != '3' && $('#wizard_step').val() != '1'){
				cancelNewYear();
			}
		},
		buttons:buttons
	});
	var module ={};
	module.name= 'new_year';
	module.title = getLang('new_year');
	module.data= 'wizard_step=0';
	module.type= 'POST';
	module.div = '#new_year_wizard';
	module.callback = function(){
		$("#new_year_wizard").dialog("open")
	}
	loadModule(module);
} 


function chkServerConx(i){
	$tr = $("#connections_tables tr").eq(i);
	var url = $tr.find('input[name="url"]').val();
	var ping = {
		name: 'connections',
		param: 'do_ping='+url,
		post: '',
		muted: true,
		async: true,
		callback: function(answer){
			var d = new Date();
			var $img = $tr.find('img.status');
			if(answer.result=='1'){
				$img.attr('src', 'assets/img/success.png?'+d.getTime());
			} else {
				$img.attr('src', 'assets/img/error.png?'+d.getTime());
			}
			if(i < $("#connections_tables tr").length){
				chkServerConx(i+1)
			}
		}
	}
	getModuleJson(ping);
}