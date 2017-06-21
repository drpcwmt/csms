// Login JS
initLoginForm();
initiateJquery();

function initLoginForm(){
	var serverConfig = window.configFile.MS_codeName;
	$('#loginForm').keypress(function(e) {
		if(e.which == 13) {
			submitLoginForm();
		}
	});
	if("localStorage" in window && localStorage[serverConfig+".username"] && localStorage[serverConfig+".username"] !=''){
		$('#loginForm #user').val(localStorage[serverConfig+".username"]);
		$('#loginForm #pass').val(localStorage[serverConfig+".password"] ? localStorage[serverConfig+".password"] : '');
		$('#loginForm #keep_signin').attr('checked', 'checked');
	}
}


function submitLoginForm(){
	serverConfig = window.configFile.MS_codeName;
	if($('#loginForm #user').val() == ''){
		$('#login_error_div').html(getLang('empty_user_name'));
		$('#login_error_div').fadeIn().effect('shake');
	} else if($('#loginForm #pass').val() == ''){
		$('#login_error_div').html(getLang('empty_password'));
		$('#login_error_div').fadeIn().effect('shake');
	} else {
		$('#login_error_div').fadeOut();
		if(localStorage[serverConfig+".year"] && localStorage[serverConfig+".year"] !=''){
			$("#loginForm").append('<input type="hidden" name="session" value="year-'+localStorage[serverConfig+".year"]+'" />');
			
		} else if(localStorage[serverConfig+".class"] && localStorage[serverConfig+".class"] != ""){
			$("#loginForm").append('<input type="hidden" name="session" value="cur_id-'+localStorage[serverConfig+".class"]+'" />');
			
		}else if(localStorage[serverConfig+".std"] && localStorage[serverConfig+".std"] != ""){
			$("#loginForm").append('<input type="hidden" name="session" value="std-'+localStorage[serverConfig+".std"]+'" />')
		}
		var module = {
			name: 'login',
			param: 'dologin',
			post: $('#loginForm').serialize(),
			async:false,
			callback: function(answer){
				if(answer['login'] == 'no'){
					$('#login_error_div').html(answer.errorlogin);
					$('#login_error_div').fadeIn().effect('shake');
				} else {
					if($('#loginForm #keep_signin').attr('checked') == 'checked'){
						if("localStorage" in window){
							localStorage[serverConfig+".username"] = $('#loginForm #user').val();
							localStorage[serverConfig+".password"] = $('#loginForm #pass').val();
							if(answer.year){
								localStorage[serverConfig+".year"] = answer.year;
							}
							if(answer.class){
								localStorage[serverConfig+".class"] = answer.class;
							}
							if(answer.std){
								localStorage[serverConfig+".std"] = answer.std;
							}
						}
					} else {
						localStorage.removeItem(serverConfig+".username");
						localStorage.removeItem(serverConfig+".password");
					}
					window.location = 'index.php';
				}
			}
		}
		getModuleJson(module);
	}
	return false;
}
