function openBehaviors(){
	var module = {};
	module.name = 'behavior';
	module.div ='#home_content';
	module.title = getLang('absents');
	module.data = '';
	loadModule(module);
}

function addBehavior($but){
	var module ={};
	module.name = 'behavior';
	module.data = "addform";
	module.title = getLang('behavior');
	module.div = 'MS_dialog-add_behavior';
	var dialogOpt = {
		width:900,
		height:500,
		title:getLang('behavior'),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if(validateForm('#addBehaviorForm')){
					var submitSave = {
						name: 'behavior',
						param: 'save_behavior',
						post: $('#addBehaviorForm').serialize(),
						callback: function(answer){
							$('#MS_dialog-add_behavior').dialog('close');
						}
					}
					getModuleJson(submitSave);
				}
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

$("#std_class_list li").click(function(){
	var list = new Array();
	$(this).toggleClass("ui-state-active");
	$("#std_class_list li.ui-state-active").each(function(){
		list.push($(this).attr("val"));
	});
	$('#stdIds').val(list.join(','));
});

$('#cur_day').change(function(){
	 loadBehaveResult();
})

$('#class_id').change(function(){
	var conId = $(this).val();
	var con = 	$(this).attr('rel');
	getLessonsByCon(con, conId, $('#cur_day').val(), '#lesson_no')
})

$('#sugName').blur(function(){
	var con = 'student';
	var conId = $(this).attr('term');
	getLessonsByCon(con, conId, $('#cur_day').val(), '#lesson_no')
})

$("a.infos").click(function(){
	$("#dialog_info").html($(this).attr("title"));
	$("#dialog_info").dialog("open");
})

function loadBehaveResult(){
	loadtodata('blocks/behavior.php?date='+$('#cur_day').val());
}

function addbehavior(){
	if($('#sugName').attr('term') != ''){
		$('#stdIds').val($('#sugName').attr('term'))
	} 
	
	var data = $('#addBehavior_form').serialize();
	$.ajax({
		url : 'blocks/behavior.php?date=' +$('#cur_day').val(),
		type : 'POST',
		data : data,
		success :  function(data){
			if(data == '1'){
				loadBehaveResult();
			}else {
				alert('Error');
			}
		}
	});
	$('#std_class_list').empty();

}


function deleteBehavior(id){
	$.ajax({
		url: 'blocks/behavior.php?delbehavior='+id,
		success : function(data){
			if(data == '1'){
				loadBehaveResult()
			} else {
				alert('Error');
			}
		}
	})
}

function addBehavComments(id){
	var html ='<form><input type="hidden" name="id" value="'+id+'" /><textarea name="comments" row="7"></textarea></from>';
	dialogOpt = {
			buttons: [{ 
			text: getLang('send'), 
			click: function() { 
				updateRow('year','behavior', id, $('#dialog_from form').serialize(), 'loadBehaveResult()');
				$(this).dialog('close');
	
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		div: 'add_behavior_dialog',
		width:600,
		height:400,
		minim:false
	}
	openHtmlDialog(html, dialogOpt)
}


function newPattern(){
	$('#pattern_div').replaceWith('<input type="text" id="new_pattern" name="pattern" style="width:250px" />')
}