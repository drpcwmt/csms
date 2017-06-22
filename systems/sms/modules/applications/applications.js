function openApplications(){
	
	var module = {};
	module.name = 'applications';
	module.div ='#home_content';
	module.title = getLang('applications');
	module.data = '';
	loadModule(module);
}

function getAppliList($btn){
	var list = $btn.attr('rel');
	var module = {};
	module.name = 'applications';
	module.div ='#application_list';
	module.title = getLang('applications');
	module.data = 'getlist='+list;
	loadModule(module);
	
}