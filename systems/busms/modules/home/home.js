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
	if(toLoad.length > 0){
		loadMultiModules(toLoad, function(){
			$('#home_content img.loading').fadeOut();
		}, true);
	}
}


