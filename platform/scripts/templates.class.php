<?php
class Template {
    protected $file;
    protected $values = array();
  
    public function __construct($file) {
        $this->file = docRoot.'/'.$file;
    }
	
	public function set($key, $value) {
		$this->values[$key] = $value;
	}
	  
	public function output() {
		global $lang;
		if (!file_exists($this->file)) {
			return "Error loading template file ($this->file).";
		}
		$output = file_get_contents($this->file);
	  
		foreach ($this->values as $key => $value){
			if($value != '' && (strpos($key, '_date') !== false || $key == 'date')){
				
				$value = unixToDate($value);
			}
			if($value != '' && (strpos($key, '_time') !== false || $key == 'time' )){
				$value = unixToTime($value);
			}
			$tagToReplace = "[@$key]";
			if(is_array($value) || is_object($value)){ echo $key;}
			$output = str_replace($tagToReplace, $value, $output);
		}
		$paterns =array();
		//$paterns[0] = "/(\[@.*\])/";
		$paterns[0] = "/\[@(.*?)\]/";
		$paterns[1] = "/\[#(.*?)\]/";
		
		$replace = array();
		$replace[0] = "";
		$replace[1] = '$lang["$1"]';//${"lang[$1]"};
		
		$output = preg_replace($paterns[0], $replace[0], $output);
		
		$output= preg_replace_callback($paterns[1], 
      	 "translatePatern", $output);
		 
	  
		return $output;
	}
	static public function merge($templates, $separator = "\n") {
		$output = "";
		foreach ($templates as $template) {
			$content = (get_class($template) !== "Template")
				? "Error, incorrect type - expected Template."
				: $template->output();
			$output .= $content . $separator;
		}
	 
		return $output;
	}
}

	
