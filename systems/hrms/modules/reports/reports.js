// JavaScript Document

function openReport(){
	var module = {
		name: 'reports',
		data: '',
		div: '#home_content',
		title: getLang('discounts_report')
	}
	loadModule(module)
}

function subRewDis($btn){
	var $form = $btn.parents('form');
		var module = {
		name: 'reports',
		data: $form.serialize(),
		div: '#reports_main_td',
		title: getLang('report')
	}
	loadModule(module)
}