// JavaScript Document
function submitSettings(){
	MS_jsonRequest('profil', $('#profil_setting_form').serialize(), 'evalprofilAnswer(answer.id)')	;
}

function evalprofilAnswer(answer){
	 if(answer == 'restart') {
		document.location ="index.php";
	} 
}

function validatePass(pass2){
	if($(pass2).val() != $('#pass').val()) {
		MS_alert(getLang('error-password_mismatch'));
		$(pass2).val('');
		$('#pass').val('')
		$('#pass').focus();
	}
}

