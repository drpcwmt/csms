<?php
/* Session Manager
*
*/

class Session {

	static function setSession($field, $value){
		return $_SESSION[$field] = $value;
		
	}

}

?>
