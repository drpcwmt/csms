<?php
/** UPload
*
*/

class Upload{
	public $filename = '';
	public $overwite =0;
	public $multible =0;
	public $destination = 'attachs/';
	
	private $allowed_array = array("jpg", "jpeg", "gif", "png", "gd", "wbmp", "xbm", "xpm", 
	"x-ms-wmv", "mp4", "avi", "flv", "ogg", "x-ms-asf", "mpeg", "xsl", "xsls", "doc", "docx", "ppt", "pptx", "csv");

	public function __construct(){
		global $this_system;
		if($this_system->getSettings('docs_user') == 1){
			$user = new Users($_SESSION['group'], $_SESSION['user_id']);
			$this->destination = $user->doc_path;
		}
	}
	
	public function loadUploadForm(){
		$layout = new Layout($this);
		$layout->template = 'modules/upload/templates/upload_form.tpl';
		$layout->multiple = ($this->multiple ? 'multiple="multiple"' : '');
		$layout->extra = '';
		$layout->extra .= ($this->filename !='' ? '<input type="hidden" value="'.$this->filename.'" id="filename" />' : '');
		$layout->extra .= ($this->overwrite!='' ? '<input type="hidden" value="1" id="overwrite" />' : '');
		$layout->max_size = formatSize(max_size_upload);
		return $layout->_print();
	}
}