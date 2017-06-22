// Lessons Js

function openLessonDetails($but){
	var lessonId = $but.attr('lessonid');
	var curDate = $but.attr('date');
	var module ={};
	module.name = 'lessons';
	module.data = 'lesson_id='+lessonId+'&curdate='+curDate
	module.title = getLang('lesson');
	module.div = 'MS_dialog-lesson-'+lessonId;
		
	var dialogOptions = {
		width: '90%',
		height: '600',
		minim: true,
		maxim: true,
		cache:true,
		modal:false,
		buttons:[{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	openAjaxDialog(module,dialogOptions);
}

function dettachSummary($but){
	var sumId = $but.attr('homeworkid');
	var LessonId = $but.attr('lessonid');
	var $tr = $but.parents('tr');
	MS_jsonRequest('lms&summary&dettachsummary', 'lesson_id='+lessonId+'&id='+sumId, 
		function(answer){
			$tr.fadeOut().remove()
		}
	)
}

/*********************** Homework *********************/
function deleteHomework($but){
	var homeworkId = $but.attr('homeworkid');
	var $tr = $but.parents('tr');
	MS_jsonRequest('lms&homeworks&delhomework', 'id='+homeworkId, 
		function(answer){
			$tr.fadeOut().remove()
		}
	);
}


/****************** Notes *************************/
function openNote($but){
	var data = '';
	var seek = false;
	var noteIdVal = '';
	var $scope = $but.parents('tr').eq(0);
	var lessonId = $but.attr('lessonid');
	if($but.attr('noteid')){
		seek = true;
		var noteId = $but.attr('noteid');
		data = $scope.find('.holder_note-'+noteId).html();
		noteIdVal = noteId
	}
	var shared = $but.attr('shared') && $but.attr('shared') == '1' ? 1 : 0;
	var html ='<form id="note_form"><input type="hidden" name="id" value="'+noteIdVal+'" /><input type="hidden" name="lesson_id" value="'+lessonId+'" /><div class="ui-corner-all ui-state-highlight" style="padding:10px; margin-bottom:10px"><label><input type="checkbox" name="shared" value="1" '+(shared == '0' ?  '' :'checked="checked"' )+'  />'+getLang('share_note')+'</label></div><textarea name="content" class="tinymce" style="width:100%; min-height:300px;">'+data+'</textarea></form>';
	var buttons = [{ 
		text: getLang('save'), 
		click: function(){ 
			MS_jsonRequest('lessons&notes&submitnote', $('#note_form').serialize(), 
				function(answer){
					$('.holder_note-'+noteId).html($('#note_form textarea[name="content"]').val());
					$scope.replaceWith(answer.html);
					if(seek == false){
						$(".session-holder-"+lessonId).find(".notes_list").append(answer.html);
					}
					$('#MS_dialog_lesson_note').dialog('close'); 
					var countStr = $(".session-holder-"+lessonId+" .notes_counter").html()
					countStr = countStr.replace('(', '').replace(')', '');
					var notesCount = parseInt(countStr)>0 ? parseInt(countStr) : 0;
					$(".session-holder-"+lessonId+" .notes_counter").html(
						'('+ (notesCount+1) +')'
					);
					initiateJquery();
				}
			);
		}
	}];
	
	if(seek){
		buttons.push({ 
			text: getLang('delete'), 
			click: function() { 
				MS_jsonRequest('lessons&notes&deletenote', "id="+noteId+'&lesson_id='+lessonId, 
					function(answer){
						$('#MS_dialog_lesson_note').dialog('close');
						$but.parents('tr').eq(0).fadeOut().remove();
						var countStr = $(".session-holder-"+lessonId).find('.notes_counter').html().replace('(', '').replace(')', '');
						var notesCount = parseInt(countStr)>0 ? parseInt(countStr) : 0;
						$(".session-holder-"+lessonId).find('.notes_counter').html(
							notesCount>1 ? '('+ (notesCount-1) +')' : ''
						);
					}
				);
			}
		});
	}
	
	buttons.push({ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	});
	
	createHtmlDialog("lesson_note", getLang('note'), html, 800, 600, buttons, true)	;
	initTinymce();
}


function reloadNotes(){
	var module ={};
	module.name = 'lessons';
	module.title = getLang('notes');
	module.div = '#notes_div';
	module.data = 'note&loadnotes&lesson_id='+$('#lesson_id').val();
	loadModule(module, "");
}

