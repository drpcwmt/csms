// LMS Document
/***************** Commons ***************************/
function reloadChapters(bookId, $chapter){
	var lms = {
		name:'lms',
		data:'books&reloadchapters&book_id='+bookId,
		div: $chapter
	}
	loadModule(lms);
}

function initBookSearch($scope){
	$title = $scope.find('input[name="title"]');
	$title.focus(function(){
		$(this).val('');
		$(this).nextAll('input.autocomplete_value').val('');
		if($(this).hasClass('ui-autocomplete-input')){
			$(this).autocomplete("destroy");
		}
		autoSummaryTitle($(this), $scope) ;
	});
	$scope.find('select[name="chapter_id"]').change(function(){
		$title.val('');
		$scope.find('input.autocomplete_value').val('');
		
	});

	$scope.find('select[name="book_id"]').change(function(){
		reloadChapters($scope.find('select[name="book_id"] option:selected').val(), $scope.find('select[name="chapter_id"]'));
		$scope.find('select[name="chapter_id"]').change();
		$title.val('');
		$scope.find('input.autocomplete_value').val('');
		
	});
	
}
/***************** Summarys **************************/
	// autocomplete full summary
function setSummaryAutocomplete(input, $scope){
	//	var $scope = $(".summary_form-"+sumId);
		var $attachField = $scope.find('input.attachemets_field');
		var $attachTable = $scope.find('table.attachemets_table');
		var $summaryField = $scope.find('textarea[name="summary"]');
		var $input = $scope.find(input);
		var chapterId = $scope.find('select[name="chapter_id"]').val();

	var source = 'index.php?module=lms&summarys&autocomplete=summary&chapter_id='+chapterId;
	
	$(input).autocomplete({
		source: source,	
		minLength: 0,
		select: function(event, ui) {
			var name = ui.item.title ? ui.item.title : '';
			$(input).val(name);
			if($summaryField.length >0){
				var content = ui.item.summary;
				$summaryField.html(content);
			}
			$(input).attr('term',ui.item.id);
			if($scope.find('input[name="summary_id"]').length >0){
				$scope.find('input[name="summary_id"]').val(ui.item.id);
			}
			if($(input).nextAll('input.autocomplete_value')){
				$(input).nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($(input).nextAll('div.ui-state-error')){
				$(input).nextAll('div.ui-state-error').fadeOut().remove();
			}

			if($attachTable.length >0){
				$attachField.val(ui.item.attachs)
				var module ={};
				module.name = 'lms';
				module.data = 'summary&reload_attachs='+ui.item.id;
				module.title= getLang('attachements'); 
				module.div = $attachTable;
				loadModule(module,"");
			}
			return false;
		},	
		search: function(event, ui) {
			$(input).attr('term', '');	
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		var name = item.title ? item.title : '';
		return $( '<li></li>' )
			.data( "item.autocomplete", item )
			.append( "<a>" + name+"</a>" )
			.appendTo( ul );
	};
	
	$(input).focus(function(){
		$(this).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');
	});
	
	$(input).blur(function(){
		setTimeout(function(){
			$(this).next('.ui-icon-arrowrefresh-1-w').fadeOut().remove();
		}, 500)
	});
	$(input).keypress(function(){
		$(this).next('.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}
	// autocomplete summary name
function autoSummaryTitle(input, $scope){
	var $input = $scope.find(input);
	var chapterId = $scope.find('select[name="chapter_id"]').val();
	var bookId = $scope.find('select[name="book_id"]').val();
	var serviceId = $scope.find('input[name="service_id"]').val();
	var source = 'index.php?module=lms&summarys&autocomplete=summary';
	if(chapterId != '' && chapterId != ' '){
		source += '&chapter_id='+chapterId;
	}
	if(bookId != '' && bookId != ' '){
		source += "&book_id="+bookId;
	}
	if(serviceId != '' && serviceId != ' '){
		source += "&service_id="+serviceId;
	}
	
	$(input).autocomplete({
		source: source,	
		minLength: 0,
		select: function(event, ui) {
			var name = ui.item.title ? ui.item.title : '';
			$(input).val(name);
			$(input).attr('term',ui.item.id);
			if($(input).nextAll('input.autocomplete_value')){
				$(input).nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($(input).nextAll('div.ui-state-error')){
				$(input).nextAll('div.ui-state-error').fadeOut().remove();
			}
			if($(input).attr('after')){
				var func = $(input).attr('after');
				eval(func)($(input).nextAll('input.autocomplete_value'));
			}
			return false;
		},	
		search: function(event, ui) {
			$(input).attr('term', '');	
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		var name = item.title ? item.title : '';
		return $( '<li></li>' )
			.data( "item.autocomplete", item )
			.append( "<a>" + name+"</a>" )
			.appendTo( ul );
	};
	
	$(input).focus(function(){
		$(this).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');
	});
	
	$(input).blur(function(){
		setTimeout(function(){
			$(this).next('.ui-icon-arrowrefresh-1-w').fadeOut().remove();
		}, 500)
	});
	$(input).keypress(function(){
		$(this).next('.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}

function reloadLessonSummary(lessonId){
	var module = {};
	module.name = "lms";
	module.data = "reloadlessonsummary&summary&lesson_id="+lessonId;
	module.div = "#summaryTd";
	loadModule(module, "$('#MS_dialog-summary').dialog('close')");
}

/*******************************************/
// books
/*function reloadBooks(serviceId){
	var module ={};
	module.name = 'lms';
	module.data = 'books&reloadselc=books&parent='+serviceId;
	module.div = '#book_id_td';
	module.title = getLang('books');
	loadModule(module, "reloadChapters($('#book_id_td select options:first').val())");
	
}*/

function editBook($but){
	$('#loading_main_div').fadeOut();
	var serviceId = $but.attr('serviceid');
	if($but.attr('bookid')){
		var bookId = $but.attr('bookid');
		var bookIdHtml = '<input type="hidden" name="book_id" value="'+bookId+'"/>';
		var bookName = $but.attr('bookName');
	} else {
		var bookId = 'new';
		var bookIdHtml = '<input type="hidden" name="book_id" value=""/>';
		var bookName = '';
	}
	
	var html = '<form id="editBookForm">'+bookIdHtml+'<input type="hidden" name="service_id" value="'+serviceId+'" /><table  cellspacing="0"><tr><td><label class="label reverse_align" style="width:100px; float:left">'+getLang('title')+': </label></td><td><input id="book_title" name="title" class="input_double" type="text" value="'+bookName+'" /></td></tr></table></form>';

	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			var bookName = $('#editBookForm input[name="title"]').val();
			if(bookId == 'new'){
				MS_jsonRequest('lms&books&newbook', $("#editBookForm").serialize(), function(answer){
					// update book list
					var $scope = $('#books_list-'+serviceId+' .book_list'); // the accordion
					$scope.append('<h3><text class="holder-book-'+answer.id+'">'+ bookName+'</text><a class="rev_float ui-state-default ui-corner-all hoverable" bookname="'+bookName+'" bookid="'+answer.id+'" action="editBook"><span class="ui-icon ui-icon-pencil"></span></a></h3><div style="padding:0px"><div id="book-'+answer.id+'-chapters" class="chapter_list" style="padding:7px"><ul style="list-style:none; padding:0; margin:0"></ul><a style="padding:5px; display:block;" class="hoverable hand ui-state-default ui-corner-all" action="editChapter" bookid="'+answer.id+'" serviceid="'+serviceId+'"><span style="float:left" class="ui-icon ui-icon-plus"></span>'+getLang('new_chapter')+'...</a></div>');
					$scope.accordion("destroy").accordion();
					// insert books select
					$('select[name="book_id"][serviceid="'+serviceId+'"]').append('<option value="'+answer.id+'">'+$('#editBookForm input[name="title"]').val()+'</option>');
					initiateJquery();
					$('#MS_dialog_edit_book').dialog('close');
				});
			} else { // Edit book
				MS_jsonRequest('lms&books&editbook', $("#editBookForm").serialize(), function(answer){
					// update all this book name;
					$('.holder-book-'+answer.id).html(bookName);
					// update the button attr bookname
					$but.attr('bookname', bookName)
					// update books select
					$('select[name="book_id"][serviceid="'+serviceId+'"]').find('option[value="'+answer.id+'"]').html(bookName);
					$('#MS_dialog_edit_book').dialog('close');
				});
			}
		}
	}];
	
	if(bookId != 'new'){
		buttons.push({ 
			text: getLang('delete'), 
			click: function() { 
				var $scope = $('#books_list-'+serviceId);
				MS_jsonRequest('lms&books&deletebook', $("#editBookForm").serialize(), function(answer){
					// update book list
					var $accHeader = $but.parents('h3');
					$accHeader.next('div').fadeOut().remove();
					$accHeader.fadeOut().remove();
					$scope.find('.book_list').accordion("destroy").accordion();
					// remove books summary list
					$('.summary_list tr[bookid="'+answer.id+'"]').fadeOut().remove();
					// update books select
					$('select[name="book_id"][serviceid="'+serviceId+'"]').find('option[value="'+answer.id+'"]').hide().remove();
					$('#MS_dialog_edit_book').dialog('close');
				});
			}
		});
	}
	buttons.push({ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	});
	
	createHtmlDialog('edit_book', getLang('book'),  html, 450, 170, buttons)
}


function editChapter($but){
	var $scope = $but.parents('.scope');
	var serviceId = $but.attr('serviceid');
	if($but.attr('bookid')){
		var bookId = $but.attr('bookid');
	} else if($scope.find('*[name="book_id"]').length > 0){
		var bookId = $scope.find('*[name="book_id"]').val();
	}
	bookIdHtml = '<input type="hidden" name="book_id" value="'+bookId+'"/>';

	if($but.attr('chapterid')){
		var chapterId = $but.attr('chapterid');
		var chapterName = $but.attr('chapterName');
	} else {
		var chapterId = '';
		var chapterName = '';
	}
		
	var html = '<form id="editChapterForm"><input type="hidden" name="chapter_id" value="'+chapterId+'" /><table  cellspacing="0">'+bookIdHtml+'<tr><td><label class="label reverse_align" style="width:100px; float:left">'+getLang('title')+':</label></td><td><input id="chapter_title" name="title" value="'+chapterName+'" class="input_double" type="text" /></td></tr></table></form>';

	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			if(chapterId != ''){ //update Chapter
				MS_jsonRequest('lms&books&editchapter', $("#editChapterForm").serialize(), function(answer){
					$('.holder-chapter-'+answer.id).html($('#chapter_title').val());
					$but.attr('chaptername', $('#editChapterForm input[name="title"]').val())
					$('#MS_dialog_edit_chapter').dialog('close');
				});
			} else { // new Chapter
				MS_jsonRequest('lms&books&newchapter', $("#editChapterForm").serialize(), function(answer){
					var $chapter_list = $('#book-'+answer.book_id+'-chapters ul');
					$chapter_list.append('<li title="'+answer.title+'" action="displaySummaryList" serviceid="'+serviceId+'" chapterid="'+answer.id+'" style="display:block;padding:5px;" class="hand ui-state-default hoverable clickable ui-corner-all"><span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><text class="holder-chapter-'+answer.id+'">'+answer.title+'</text></span><a class="rev_float ui-state-default ui-corner-all hoverable" serviceid="'+serviceId+'" bookid="'+answer.book_id+'" chaptername="'+answer.title+'" chapterid="'+answer.id+'" action="editChapter"><span class="ui-icon ui-icon-pencil"></span></a></li>');
					
					$scope.find('select[name="chapter_id"]').append('<option value="'+answer.id+'">'+answer.title+'</option>');
					$('#MS_dialog_edit_chapter').dialog('close');
					iniButtonsRoles();
				});
			}
		}
	}];
	
	if(chapterId != ''){
		buttons.push({ 
			text: getLang('delete'), 
			click: function() { 
				MS_jsonRequest('lms&books&deletechapter', $("#editChapterForm").serialize(), function(answer){
					var $chapter_list = $('#book-'+bookId+'-chapters');
					var $scope = $chapter_list.parents('.scope');
					$scope.find('.summarys_list').html('');
					var $chapter = $chapter_list.find('li[chapterid="'+chapterId+'"]').fadeOut().remove();
					$('.summary_list tr[chapterid="'+answer.id+'"]').fadeOut().remove();
					$('#MS_dialog_edit_chapter').dialog('close');
				});
			}
		});
	}
	buttons.push({  
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	});
	createHtmlDialog('edit_chapter', getLang('chapter'),  html, 450, 170, buttons)
}

/*************** TimeLine Scrollablr *************/
function iniTimeline(serviceId) {
	var scrollPane = $( ".timeline[serviceId="+serviceId+"]" ),
	scrollContent = scrollPane.find( ".scroll-content" ),
	scrollItem = scrollContent.find(".scroll-content-item");

	var countItems = scrollContent.find('.scroll-content-item').length;
	scrollContent.find(".scroll-content-item").eq(0).removeClass('faded').addClass('current');
	scrollContent.css('width', (countItems+1) * scrollItem.outerWidth(true));
	
	var scrollbar = scrollPane.find( ".scroll-bar" ).slider({
		step:1,
		max:(countItems-1),
		min:0,
		slide: function( event, ui ) {
			if ( scrollContent.width() > scrollPane.width() ) {
				moveTimeline(ui.value, serviceId);
			} else {
				scrollContent.css( "margin-left", 0 );
			}
		}
	});
	
	scrollItem.click(function(){
		if($(this).hasClass('timeline_end')) return false;
		var index = $(this).index();
		scrollbar.slider('value', index);
		moveTimeline(index, serviceId);
	}).hover(
		function(){$(this).animate({opacity:1});},
		function(){if($(this).hasClass('current') == false){$(this).animate({opacity:0.35});}}
	);
	//append icon to handle
	var handleHelper = scrollbar.find( ".ui-slider-handle" )
		.mousedown(function() {
			scrollbar.width( handleHelper.width() );
		})
		.mouseup(function() {
			scrollbar.width( "100%" );
		})
		.append( "<span class='ui-icon ui-icon-grip-dotted-vertical'></span>" )
		.wrap( "<div class='ui-handle-helper-parent'></div>" ).parent();
		
		scrollPane.css( "overflow", "hidden" );

	
	//change handle position on window resize
//	$( window ).resize(function() {
//		resetValue();
//		sizeScrollbar();
//		reflowContent();
//	});
//	sizeScrollbar();
}

function moveTimeline(index, serviceId){
	var scrollPane = $( ".timeline[serviceId="+serviceId+"]" ),
	scrollContent = scrollPane.find( ".scroll-content" ),
	scrollItem = scrollContent.find(".scroll-content-item");
	var countItems = scrollContent.find('.scroll-content-item').length;

	scrollContent.animate({
		marginLeft :  Math.round(-1 * index * scrollItem.outerWidth(true)) + "px" 
	});
	$(scrollContent.find('.scroll-content-item').eq(index)).animate({opacity:1}).addClass('current');
	scrollContent.find(".scroll-content-item").eq(index-1).animate({opacity:0.35}).removeClass('current')
	scrollContent.find(".scroll-content-item").eq(index+1).animate({opacity:0.35}).removeClass('current');
		// load next lessons
	var lastPaneData = scrollContent.find(".scroll-content-item:last").text();
	var lastPane = scrollContent.find(".scroll-content-item:last");
	if(lastPane.hasClass('timeline_loading')== false && lastPane.hasClass('timeline_end')== false && (index+3) == countItems){
		scrollContent.css('width', parseInt(scrollContent.css('width'))+scrollItem.outerWidth());
		scrollContent.append('<div class="timeline_loading scroll-content-item faded ui-widget-content ui-corner-all"><img src="assets/img/loading_mini.gif" /></div');		
		var module ={};
		module.name = 'lms';
		module.title = getLang('lessons');
		module.data = 'timeline&service_id='+serviceId+'&cur='+ (countItems);
		var newLessons = MS_aJaxRequest(module, 'GET', true, 'addToTimeline(answer, '+serviceId+')');
	}	
}

function addToTimeline(data, serviceId){
	var scrollPane = $( ".timeline[serviceId="+serviceId+"]" ),
	scrollContent = scrollPane.find( ".scroll-content" );
	var scrollbar = scrollPane.find( ".scroll-bar" );
	
	scrollContent.find('.timeline_loading').fadeOut().remove();
	scrollContent.append(data);
	initiateJquery();
	
	var countItems = scrollContent.find('.scroll-content-item').length;
	scrollContent.css('width', (countItems+1) * scrollContent.find(".scroll-content-item").outerWidth(true));
	
	scrollbar.slider('option', 'max', (countItems-1));

	var scrollItem = scrollContent.find(".scroll-content-item");
	scrollItem.click(function(){
		if($(this).hasClass('timeline_end')) return false;
		var index = $(this).index();
		scrollbar.slider('option', 'value', index);
		moveTimeline(index, serviceId);
	}).hover(
		function(){$(this).animate({opacity:1});},
		function(){if($(this).hasClass('current') == false){$(this).animate({opacity:0.35});}}
	);
	//$( ".scroll-bar" ).slider("max", (countItems - 1));
}
/*//size scrollbar and handle proportionally to scroll distance
function sizeScrollbar() {
	var scrollbar = $( ".scroll-bar" )
	var handleHelper = scrollbar.find( ".ui-slider-handle" )
	var scrollPane = $( ".scroll-pane" ),
	scrollContent = $( ".scroll-content" );
	var scrollItem = scrollContent.find(".scroll-content-item");
	
	var remainder = scrollContent.width() - scrollItem.width();;
	var proportion = remainder / scrollContent.width();
	var handleSize = scrollbar.width() - ( proportion * scrollbar.outerWidth(true) );
//	scrollbar.find( ".ui-slider-handle" ).css({
//		width: handleSize,
//		"margin-left": -handleSize / 2
//	});
//	handleHelper.width( "" ).width( scrollbar.width() - handleSize );
}
//reset slider value based on scroll content position
function resetValue() {
	var scrollbar = $( ".scroll-bar" );
	var scrollPane = $( ".scroll-pane" ),
	scrollContent = $( ".scroll-content" );
	var remainder = scrollPane.width() - scrollContent.innerWidth();
	var leftVal = scrollContent.css( "margin-left" ) === "auto" ? 0 :
	parseInt( scrollContent.css( "margin-left" ) );
	var percentage = Math.round( leftVal / remainder * 100 );
	scrollbar.slider( "value", percentage );
}
//if the slider is 100% and window gets larger, reveal content
function reflowContent() {
	var scrollPane = $( ".scroll-pane" ),
	scrollContent = $( ".scroll-content" );
	var showing = scrollContent.width() + parseInt( scrollContent.css( "margin-left" ), 10 );
	var gap = scrollPane.width() - showing;
	if ( gap > 0 ) {
		scrollContent.css( "margin-left", parseInt( scrollContent.css( "margin-left" ), 10 ) + gap );
	}
}*/

/***************** Summarys **********************/
function displaySummaryList($but){
	var $scope = $but.parents('.scope');
	$scope.find('.chapter_list li').removeClass('ui-state-active');
	$but.addClass('ui-state-active');
	var chapterId = $but.attr('chapterid');
	var bookId = $but.attr('bookid');
	var serviceId = $but.attr('serviceid');	// From lms book list
   	var module ={};
	module.name = 'lms';
	module.data = 'books&summary_list&chapter_id='+chapterId;
	module.div = '#books_layout-'+bookId+' .summarys_list';
	module.title = getLang('summary');
	loadModule(module); 
}

function openSummary($but){
	var seek = false;
	var data = 'summary';
	if($but.attr('summaryid')){
		var sumId = $but.attr('summaryid');
		data += '&summary_id='+sumId;
	} else {
		var sumId = 'new';
		data += '&new';
		$scope = $but.parents('.scope');
		if($but.attr('serviceid')) {
			data += '&service_id='+ $but.attr('serviceid');
		} 
		if($but.attr('lessonid')) {
			data += '&lesson_id='+ $but.attr('lessonid');
		} 
		if( $but.attr('chapterid')){
			chapterId = $but.attr('chapterid');
			data += '&chapter_id='+chapterId;
		} else if($scope.find('.book_list li.ui-state-active').length > 0){ // from book list
			$chapter = $scope.find('.book_list li.ui-state-active');
			chapterId = $chapter.attr('chapterid');
			data += '&chapter_id='+chapterId;
		}
		if( $but.attr('bookid')){
			bookId = $but.attr('bookid');
			data += '&book_id='+bookId;
		} else if($scope.find('.book_list h3.ui-state-active').length > 0){ // from book list
			$book = $scope.find('.book_list h3.ui-state-active');
			bookId = $book.attr('bookid');
			data += '&book_id='+bookId;
		}
	}

	var lms = {
		name : 'lms',
		title : getLang('summary'),
		div : 'dialog-summary-'+sumId,
		data : data,
		async : false,
		cache : false,
		callback: function(){ 
			// initiate summary edit form
			var scope = ".summary_form-"+sumId;
			if($(scope).length > 0){
				var $bookIsSelect = $(scope).find('select[name="book_id"]');
				$bookIsSelect.change(function(){
					reloadChapters($(this).val(), $('.summary_form-'+sumId+' select[name="chapter_id"]'))
				});

				title = scope+' input[name="title"]';
				$(title).focus(function(){
					setSummaryAutocomplete(title, $(".summary_form-"+sumId)) ;
				});
				// Set dialog buttons
				buttons = [{ 
					text: getLang('save'), 
					click: function() { 
						submitSummary(sumId);
					}
				}];
				if(sumId != 'new'){ 
					buttons.push({
						text: getLang('delete'), 
						click: function() { 
							MS_jsonRequest('lms&summary&deletesummary', 'id='+sumId, function(answer){
								$('.summary_list table tr[summaryid="'+answer.id+'"]').fadeOut().remove();
								$('#dialog-summary-'+sumId).dialog('close');
							});
						}
					});
				}
				buttons.push({
					text: getLang('close'), 
					click: function() { 
						$(this).dialog('close');
					}
				});
				$('#dialog-summary-'+sumId).dialog({ buttons: buttons});
			}
		}
	};
	
	var dialogOptions = {
		width: 920,
		height: 600,
		minim: false,
		maxim: false,
		cache:true,
		modal:false,
		buttons:[{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	openAjaxDialog(lms,dialogOptions);
}

function submitSummary(sumId){
	$summaryForm = $(".summary_form-"+sumId);
	var data = $summaryForm.serialize();
	var bookId = $summaryForm.find('select[name="book_id"]').val();
	var bookName = $summaryForm.find('select[name="book_id"] option:selected').text();
	var chapterId = $summaryForm.find('select[name="chapter_id"] option:selected').val();
	var chapterName = $summaryForm.find('select[name="chapter_id"] option:selected').text();
	var serviceId = $summaryForm.find('input[name="service_id"]').val();
	var title = $summaryForm.find('input[name="title"]').val();
	var summary = $summaryForm.find('textarea[name="summary"]').val();

	if(validateForm($summaryForm)){
		var url = 'lms&summary&submitsummary';
		MS_jsonRequest(url, data, function(answer){
			if($summaryForm.find('input[name="lesson_id"]').length>0){
				var $scope = $("#lesson_summarys-"+serviceId);
			} else {
				var $scope = $("#books_layout-"+bookId);
			}
			if($scope.length > 0 && $scope.find(".summarys_list").length>0){
				var $summary_list = $scope.find(".summarys_list");
				if(answer.html){
					$summary_list.find('table.result').append(answer.html);
				}
			}
			$('.holder-summary_title-'+answer.id).html(title);
			$('.holder-summary_content-'+answer.id).html(summary);
			iniButtonsRoles();
			$('#dialog-summary-'+sumId).dialog('close');
		});
	}
}

/******************HomeWork****************************/
function openHomework($but){
	var $scope = $but.parents('.scope').eq(0);
	var data = 'homeworks';
	if($but.attr('homeworkid')){
		var hwId = $but.attr('homeworkid');
		data += '&homeworks&hw_id='+hwId;
	} else {
		var hwId = 'new';
	}
	if($but.attr('lessonid')) {
		data += '&lesson_id='+$but.attr('lessonid');
	}
	

	var lms = {
		name : 'lms',
		title : getLang('homework'),
		div : 'dialog-homework-'+hwId,
		data : data,
		async : false,
		cache :false,
		callback: function(){ 
			var scope = ".homework_form-"+hwId;
			initBookSearch($(".homework_form-"+hwId)); 
			if($(scope).length > 0){
				$('#dialog-homework-'+hwId).dialog({
					buttons: [{ 
						text: getLang('save'), 
						click: function() { 
							var submitData = $('#dialog-homework-'+hwId+' form').serialize();
							MS_jsonRequest('lms&submithomework&'+data, submitData,
								function(answer){
									$('.holder_homework-'+answer.id).html($('#MS_dialog-homework textarea[name="homework"]').val());
									if(hwId == 'new'){
										$scope.find('.homework_list').append(answer.html);
										var countStr = $scope.find('.homework_counter').html().replace('(', '').replace(')', '')
										var homeworkCount = parseInt(countStr)>0 ? parseInt(countStr) : 0;
										$scope.find('.homework_counter').html(
											'('+ (homeworkCount+1) +')'
										);
									}
									$('#dialog-homework-'+answer.id).dialog('close');
								}
							);
						}
					},{ 
						text: getLang('close'), 
						click: function() { 
							$(this).dialog('close');
						}
					}]
				});
			}
		}

	};
	
	var dialogOptions = {
		width: 920,
		height: 600,
		minim: false,
		maxim: false,
		cache:true,
		modal:false,
		buttons:[{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	openAjaxDialog(lms,dialogOptions);
}


/************* Exercises ********************/
function updateExerciseValues($scope){
	var points = 0;
	var sec = 0;
	var q = 0;
	$scope.find(".questions_list li[questionid]").each(function(){
		var thisPoint = $(this).find('input[name="points"]').val();
		var thisTime = $(this).find('input[name="time"]').val();
		q++;
		points = points + parseInt(thisPoint);
		sec = sec + parseInt(thisTime);
	});
	$scope.find(".count_questions").html(q);
	$scope.find(".total_points").html(points);
	$scope.find(".total_time").html(unixTimeFormat(sec));
}

function editExercise($but){
	var $scope = $but.parents('.scope').eq(0);
	var data = "exercises";
	var exerciseId = $but.attr('exerciseid');
	var serviceId = $but.attr('serviceid');
	data += "&service_id="+serviceId;
	if($scope.find('select[name="book_id"]').val()!= null){
		data += "&book_id="+$scope.find('select[name="book_id"]').val();
	}
	if($scope.find('select[name="chapter_id"]').val()!= null){
		data += "&chapter_id="+$scope.find('select[name="chapter_id"]').val();
	}
	if($scope.find('input[name="summary_id"]').val()!= null){
		data += "&summary_id="+$scope.find('input[name="summary_id"]').val();
	}
	
	var lms = {
		name:"lms",
		data : data,
		div : "dialog-exercises",
		title : getLang('exercises'),
		cache: false,
		async : false,
		callback: function(){ 
			initBookSearch($("#dialog-exercises"));
			 $("#dialog-exercises .questions_list, #dialog-exercises .question_search_results" ).sortable({
				connectWith: ".connectedSortable",
				update: function(event, ui) {   
					updateExerciseValues($("#dialog-exercises"));
				}
			}).disableSelection();
		}
	}
	
	var dialogOptions = {
		width: '90%',
		height: '600',
		minim: false,
		maxim: false,
		cache:true,
		modal:true,
		buttons:[{ 
			text: getLang('preview'), 
			click: function() { 
				var exerciseData = parseExerciceData();
				var preview = {
					name:"lms",
					data : 'preview&data='+exerciseData+'&'+data+'&'+$("#dialog-exercises form").serialize(),
					div : "dialog-exercises-preview",
					title : getLang('exercises'),
					cache: false,
					async : false,
					callback: function(){ 
						 
					}
				}
				
				var dialogOptions = {
					width: '800',
					height: '600',
					minim: false,
					maxim: false,
					cache:true,
					modal:true,
					buttons:[{ 
						text: getLang('close'), 
						click: function() { 
							$(this).dialog('close');
						}
					}]
				};
				openAjaxDialog(preview,dialogOptions);
			}
		},{ 
			text: getLang('save'), 
			click: function() { 
				
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	openAjaxDialog(lms,dialogOptions);
}

function parseExerciceData(){
	var $scope = $("#dialog-exercises");
	var pages = new Array
	$scope.find('.questions_list').each(function(){
		var out = '';
		var $page = $(this);
		var tags = new Array;
		$page.find('li').each(function(){
			if($(this).attr('questionid')){
				var questionId = $(this).attr('questionid');
				var time = $(this).find('input[name="time"]').val();
				var points = $(this).find('input[name="points"]').val();
				tags.push('<%['+questionId+'-'+points+'-'+time+']%>');
			} else {
				tags.push('<%'+$(this).html()+'%>');
			}
		});
		pages.push('{'+ tags.join('')+'}');
	});
	return pages.join('');
}

function addExercisePage($but){
	var $scope = $but.parents('.scope').eq(0);
	var $lastPage =$scope.find('.questions_list:visible');
	var index = $scope.find('.questions_list').length;
	var $newPage = $('<ol class="questions_list ui-widget-content ui-corner-all connectedSortable"></ol>');
	$lastPage.after($newPage);
	$lastPage.hide();
	var $pageNav = $scope.find('.pageNav');
	$pageNav.find('li').removeClass('ui-state-active');
	$pageNav.append('<li class="ui-state-active ui-state-default hoverable clickable selectable def_float" action="dispalyExerPage" targetpage="'+index+'">'+ (index+1) +'</li>');
	initiateJquery();
	$newPage.sortable({
		connectWith: ".connectedSortable"
	}).disableSelection();
}

function dispalyExerPage($but){
	var $scope = $but.parents('.scope').eq(0);
	var index = $but.attr('targetpage');
	$newPage = $scope.find('.questions_list').eq(index);
	var $lastPage =$scope.find('.questions_list:visible');
	$lastPage.hide();
	$newPage.show();	
}

function addExerciseHeader($but){
	var $scope = $but.parents('.scope').eq(0);
	
	buts = [{
		text: getLang('ok'), 
		click: function() { 
			$scope.find('.questions_list:visible').append('<li class="ui-corner-all ui-widget-header def_align" style="padding:2px; margin-bottom: 5px;"><h3>'+$('#MS_dialog_addExerciseHeader textarea[name="header"]').val()+'</h3></li>');
			$(this).dialog('close');
		}
	},{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}]
	createHtmlDialog('addExerciseHeader', getLang('add_header'), '<textarea name="header"></textarea>', 300, 180, buts, true);
}

function addExerciseText($but){
	var $scope = $but.parents('.scope').eq(0);
	
	buts = [{
		text: getLang('ok'), 
		click: function() { 
			$scope.find('.questions_list:visible').append('<li class="ui-corner-all ui-state-highlight def_align" style="padding:2px; margin-bottom: 5px;"><p>'+$('#MS_dialog_addExerciseText textarea[name="text"]').val()+'</p></li>');
			$(this).dialog('close');
		}
	},{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}]
	createHtmlDialog('addExerciseText', getLang('add_text'), '<textarea name="text"></textarea>', 300, 180, buts, true);
}

function addExerciseHr($but){
	var $scope = $but.parents('.scope').eq(0);
	$scope.find('.questions_list:visible').append('<li><hr style="height:3px; margin:5px 0px 10px"/></li>');
}

function changeQuestionPoints($but){
	var $scopeQuestion = $but.parents('[questionid]');
	var points = parseInt($but.html());
	buts = [{
		text: getLang('ok'), 
		click: function() { 
			var newPoints = $('#MS_dialog_editQuestionPoints input[name="points"]').val();
			$but.html(newPoints);
			$scopeQuestion.find('input[name="points"]').val(newPoints);
			updateExerciseValues($("#dialog-exercises"));
			$(this).dialog('close');
		}
	},{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}]
	var html = '<table cellpadding="0"><tr><td width="15%"><label class="ui-widget-header ui-corner-left label">'+getLang('points')+'</label></td><td><input type="text" value="'+points+'" class="input_half MS_formed" name="points"> '+getLang('points')+'</td></tr></table>';
	createHtmlDialog('editQuestionPoints', getLang('points'), html, 250, 150, buts, true);
	initiateJquery();
}

function questionExists(id){
	var questions = new Array;
	$('.questions_list li').each(function(){
		questions.push($(this).attr('questionid'));
	});
	if(questions.indexOf(id) == -1){
		return false;
	} else {
		return true;
	}
}

function insertQuestion($but){
	var $scope = $but.parents('.scope');
	var $questionBankList = $scope.find(".question_search_results");
	$questionBankList.find('input[name="selected_question"]:checked').each(function(){
		var $parentLi = $(this).parents('[questionid]');
		var questionId = $parentLi.attr('questionid');
		if(questionExists(questionId) ){
			buts = [{
				text: getLang('ok'), 
				click: function() { 
					$parentLi.appendTo($scope.find(".questions_list"));
					$(this).dialog('close');
				}
			},{ 
				text: getLang('skip'), 
				click: function() { 
					$(this).dialog('close');
				}
			}]
			createHtmlDialog('confirm_question_exists', getLang('confirm'), getLang('confirm_question_exists'), 350, 150, buts, true);
		} else {
			$parentLi.appendTo($scope.find(".questions_list"));
		}
	});
}

function removeQuestion($but){
	var $parentLi = $but.parents('[questionid]');
	$parentLi.fadeOut().remove();
}

function exerciseNextPage($but){
	var $scope = $but.parents('.scope');
	var width = parseInt($scope.find('page').eq(0).outerWidth());
	var ml = parseInt($scope.find('.slider_content').css('margin-left'));
	$scope.find('.slider_content').animate({marginLeft:((ml+760)*-1)+'px' });
}

function exercisePrevPage($but){
	var $scope = $but.parents('.scope');
	var width = parseInt($scope.find('page').eq(0).outerWidth());
	var ml = parseInt($scope.find('.slider_content').css('margin-left'));
	$scope.find('.slider_content').animate({marginLeft:(ml+760)+'px' });
}

/*function openQuestionBank($but){
	var $scope = $but.parents('.scope').eq(0);
	var data = "questions";
	var serviceId = $but.attr('serviceid');
	data += "&service_id="+serviceId;
	if($scope.find('select[name="book_id"]').val()!= null){
		data += "&book_id="+$scope.find('select[name="book_id"]').val();
	}
	if($scope.find('select[name="chapter_id"]').val()!= null){
		data += "&chapter_id="+$scope.find('select[name="chapter_id"]').val();
	}
	if($scope.find('input[name="summary_id"]').val()!= ''){
		data += "&summary_id="+$scope.find('input[name="summary_id"]').val();
	}
	
	var lms = {
		name:"lms",
		data : data,
		div : "dialog-questions",
		title : getLang('questions_bank'),
		cache: false,
		async : false,
		callback: function(){ 
			initBookSearch($("#dialog-questions"));
		}
	}
	
	var dialogOptions = {
		width: 800,
		height: 600,
		minim: false,
		maxim: false,
		cache:true,
		modal:true,
		buttons:[{ 
			text: getLang('insert'), 
			click: function() { 
				var questionArray = new Array;
				$('#dialog-questions input[name="question"]:checked').each(function(){
					questionArray.push($(this).val());
					var $tr = $(this).parents('tr').eq(0);
					$tr.appendTo($scope.find('.questions_list'));
					
				});
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	openAjaxDialog(lms,dialogOptions);
}*/

/************* Questions ********************/
function editQuestion($but){
	var $scope = $but.parents('.scope').eq(0);
	var serviceId = $but.attr('serviceid');
	
	var data = "questions";
	if($but.attr('questionid')){
		var questionId = $but.attr('questionid')
		data += "&question_id="+questionId;
	} 
	data += "&service_id="+serviceId;
	if($scope.find('select[name="type"]').val()!= null){
		data += "&type="+$scope.find('select[name="type"]').val();
	}
	if($scope.find('select[name="book_id"]').val()!= null){
		data += "&book_id="+$scope.find('select[name="book_id"]').val();
	}
	if($scope.find('select[name="chapter_id"]').val()!= null){
		data += "&chapter_id="+$scope.find('select[name="chapter_id"]').val();
	}
	if($scope.find('input[name="summary_id"]').val()!= ''){
		data += "&summary_id="+$scope.find('input[name="summary_id"]').val();
	}
	
	var lms = {
		name:"lms",
		data : data,
		div : "dialog-quiz-"+questionId,
		title : getLang('question'),
		cache: false,
		async : false,
		callback: function(){ 
			initBookSearch($("#dialog-quiz-"+questionId))
		}
	}
	buttons = [{ 
		text: getLang('save'), 
		click: function() { 
		var $form = $('#dialog-quiz-'+questionId+' form');
			MS_jsonRequest('lms&questions&submit_question', $form.serialize(), function(answer){
				$('.holder_question-'+questionId).html($form.find('*[name="question"]').val());
				$('.holder_question_answer-'+questionId).html($form.find('*[name="answer"]').val()); 
				if(questionId == 'new'){
					$('#question_search_results').append(answer.html);
				}
				$('#dialog-quiz-'+questionId).dialog('close');
			});
		}
	}];

	if(questionId != 'new'){
		buttons.push({ 
			text: getLang('delete'), 
			click: function() { 
				MS_jsonRequest('lms&questions&delete_question', $('#dialog-quiz-'+$but.attr('questionid')+' form').serialize(), function(answer){
					$('*[questionid="'+$but.attr('questionid')+'"]').fadeOut().remove();
					$('#dialog-quiz-'+$but.attr('questionid')).dialog('close');
				});
			}
		});
	}
	buttons.push({ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	});
	
	var dialogOptions = {
		width: 600,
		height: 600,
		minim: false,
		maxim: false,
		cache:true,
		modal:false,
		buttons:buttons
	}

	openAjaxDialog(lms,dialogOptions);
}

function searchQuestion($but){
	var $scope = $but.parents('.scope').eq(0);	
	var data = "questions&search";
	//data += '&'+$("#dialog-exercises form").serialize();
	var type = $scope.find('select[name="type"]').val()
	data += "&type="+type;
	
	if($but.attr('service_id')){
		data += "&service_id="+$but.attr('service_id');
	} else if($scope.find('input[name="service_id"]').val()!= ''){
		data += "&service_id="+$scope.find('input[name="service_id"]').val();
	} else {
		alert('no service');
		return false;
	}
	if($scope.find('select[name="book_id"]').val()!= null){
		data += "&book_id="+$scope.find('select[name="book_id"]').val();
	}
	if($scope.find('select[name="chapter_id"]').val()!= null){
		data += "&chapter_id="+$scope.find('select[name="chapter_id"]').val();
	}
	if($scope.find('input[name="summary_id"]').val()!= ''){
		data += "&summary_id="+$scope.find('input[name="summary_id"]').val();
	}

	var lms = {
		name:"lms",
		data : data,
		div : $scope.find(".question_search_results"),
		title : getLang('question'),
		callback: function(){ 
			 $("#dialog-exercises .question_search_results" ).sortable({
				connectWith: ".connectedSortable"
			}).disableSelection();
			$("#dialog-exercises .question_search_results li" ).addClass('clickable hoverable');
			initiateJquery();
		}
	}
	loadModule(lms, '');
}
	// Insert quiz image
function insertQuestionImage($but){
	var $scope = $but.parents('.scope');
	var text = $scope.find('textarea').val();
	var module ={};
	module.name= 'documents';
	module.title = getLang('attachements');
	module.type= 'GET';
	module.data= '';
	module.div = 'MS_dialog_documents';
	module.cache = false;
	var dialogOptions = {
		width: 900,
		height: 600,
		minim: false,
		maxim: false,
		cache:false,
		modal:false,
		buttons:[{ 
			text: getLang('insert'), 
			click: function() { 
				$('#explorer_form input:checked').each(function(){
					var thisLink = $(this).val();
					$scope.find('textarea').val(text+'[image:'+thisLink+']');
				});
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
	/*** Editing ***/
	// True or False quiz
function setAnswer($but){
	var val = $but.attr('val');
	var $scope = $but.parents('.scope');
	$scope.find('input[name="answer"]').val(val);
}
	// Complete quiz
function insertPlaceHolder($but){
	var $scope = $but.parents('.scope');
	var text = $scope.find('textarea').val();
	var regExp = new RegExp('\\[\\w+\\]', "g"); 
   	var index = text.match(regExp) ? (text.match(regExp).length +1) : 1;  
	$scope.find('textarea').val( text +' ['+index+'] ');

	var $answerList = $scope.find('ol');
	$answerList.append('<li><input type="text" name="place_holder" class="ui-state-default ui-corner-all" update="updatePlaceholder" /></li>');
	initiateJquery();
}
	// complete quiz
function updatePlaceholder($inp){
	var $li = $inp.parents('li');
	var $scope = $inp.parents('.scope');
	var index = ($li.index() +1);
	var text = $scope.find('textarea').val();
	var newLength = $inp.val().length;
	var xs = ''
	for(x=0; x<newLength; x++){
		xs += "x";
	}

	var regExp = new RegExp('\\['+index+'\\w+\\]|\\['+index+'\\]', "g"); 
	var phs = text.match(regExp);
	text = text.replace(regExp, "["+index+xs+"]");
	$scope.find('textarea').val( text);
	
	var $answer = $scope.find('input[name="answer"]');
	var $ol = $scope.find('ol');
	var phs = new Array;
	$ol.find('input').each(function(){
		phs.push($(this).val());	
	});
	$answer.val( phs.join(',')); 
}
	// Select Quiz
function updateSelectValues($inp){
	var $scope = $inp.parents('.scope');
	var $bool = $scope.find('input[name="bool"]');
	var $ol = $scope.find('ol');
	var phs = new Array;
	$ol.find('input[type="text"]').each(function(){
		phs.push($(this).val());	
	});
	$bool.val( phs.join(',')); 
}
	// Select Quiz
function updateSelectAnswer($inp){
	var $scope = $inp.parents('.scope');
	var $answer = $scope.find('input[name="answer"]');
	var $ol = $scope.find('ol');
	var phs = new Array;
	$ol.find('input[type="checkbox"]').each(function(){
		if($(this).attr('checked') == 'checked'){
			phs.push($(this).val());	
		}
	});
	$answer.val( phs.join(',')); 
}

