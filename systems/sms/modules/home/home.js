// Home.js
function initiateHomeScreen(){
	var toLoad = new Array;
		// Messages
	if($("#home_content").find("#home_messages").length > 0){
		messages = {
			name: 'messages',
			data: 'list&home',
			div: "#home_content #home_messages",
			title:getLang('messages'),
			async:true,
			mute:true
		}
		toLoad.push(messages);
	}
		// ToDo
	if($("#home_content").find("#home_todo").length > 0){
		todo = {
			name: 'todo',
			data: 'list&home',
			div: "#home_content #home_todo",
			title:getLang('todo'),
			async:true,
			mute:true
		}
		toLoad.push(todo);
	}
		// homeworks
	if($("#home_content").find("#home_homework").length > 0){
		homework = {
			name: 'lms',
			data: 'homeworks&home',
			div: "#home_content #home_homework",
			title:getLang('homeworks'),
			async:true,
			mute:true
		}
		toLoad.push(homework);
	}
		// Notes
	if($("#home_content").find("#home_notes").length > 0){
		homework = {
			name: 'lessons',
			data: 'notes&home',
			div: "#home_content #home_notes",
			title:getLang('notes'),
			async:true,
			mute:true
		}
		toLoad.push(homework);
	}
		// Schedule
	if($("#home_content").find("#home_schedule").length > 0){
		schedule = {
			name: 'schedule',
			data: 'home',
			div: "#home_content #home_schedule",
			title:getLang('schedule'),
			async:true,
			mute:true,
			callback: function(){
				var $scope = $('#home_schedule  schedule_table');
				iniLessonTooltip($scope);
				iniIndecitor($scope);
			}
		}
		toLoad.push(schedule);
	}
	if(toLoad.length > 0){
		loadMultiModules(toLoad, function(){
			$('#home_content img.loading').fadeOut();
		}, true);
	}

}



