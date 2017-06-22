<?php
// Student data
$sms = new SMS(3);
$levels = $sms->getLevelList();
$classes = array();
foreach($levels as $level){
	$classes = array_merge($classes, $level->getClassList());
}
$classes_li = '';
foreach($classes as $class){
	$classes_li .= write_html('li', '', 
		write_html('a', 'class="ui-state-default hoverable" module="members" action="openClassList(\''.$class->id.'\');"', $class->getName())
	);	
}
$li_student_data = '<li>
	<a class="ui-state-default hoverable">'.$lang['members'].'</a>
	<span class="ui-icon ui-icon-triangle-1-s"></span>
	<ul>
		<li><a class="ui-state-default hoverable">'.$lang['student'].'</a>
			<span class="ui-icon ui-icon-triangle-1-e"></span>
			<ul>
				<li><a class="ui-state-default hoverable" module="members" action="openSeachStudentByName;">'.$lang['search'].'</a></li>
				<li>
					<a class="ui-state-default hoverable">'.$lang['classes'].'</a>
					<span class="ui-icon ui-icon-triangle-1-e"></span>
					<ul class="scrollable">'.$classes_li.'</ul>
				</li>	
			</ul>
		</li>
		<li><a class="ui-state-default hoverable">'.$lang['profs'].'</a>
			<span class="ui-icon ui-icon-triangle-1-e"></span>
			<ul>
				<li><a class="ui-state-default hoverable" module="members" action="openSeachAdminByName">'.$lang['search'].'</a></li>
			</ul>
		</li>	
	</ul>
</li>';
 
// Books data
$li_books = '<li>
	<a class="ui-state-default hoverable">'.$lang['books'].'</a>
	<span class="ui-icon ui-icon-triangle-1-s"></span>
	<ul>
		<li><a class="ui-state-default hoverable" module="books" action="openNewBook">'.$lang['new_book'].'</a></li>
		<li><a class="ui-state-default hoverable" module="books" action="openSeachBook">'.$lang['search'].'</a></li>'.
		//<li><a class="ui-state-default hoverable" action="openAdvancedSeachBook">'.$lang['advanced_search'].'</a></li>
		'<li><a class="ui-state-default hoverable" module="books" action="openCategorys">'.$lang['categorys'].'</a></li>
	</ul>
</li>';

// Books data
$li_borrow = '<li>
	<a class="ui-state-default hoverable">'.$lang['borrow'].'</a>
	<span class="ui-icon ui-icon-triangle-1-s"></span>
	<ul>
		<li><a class="ui-state-default hoverable" module="borrow" action="newBorrow">'.$lang['new_borrow'].'</a></li>
		<li><a class="ui-state-default hoverable" module="borrow" action="returnBook">'.$lang['return_book'].'</a></li>
		<li><a class="ui-state-default hoverable" module="borrow" action="loadLateList">'.$lang['late_list'].'</a></li>
	</ul>
</li>';

// Books data
$li_inventory = '<li><a class="ui-state-default hoverable">'.$lang['inventory'].'</a></li>';
//////////////////
$menus = '<ul id="menus" class="nav">';
	$group = $_SESSION['group'];
	switch($group){
		case "superadmin":
			$menus .= $li_student_data. $li_books. $li_borrow ;
		break;
		case "admin":
			$menus .= $li_student_data. $li_books.$li_borrow ;
		break;
		case "user":
			$menus .= $li_student_data . $li_books.$li_borrow;
		break;
	}
$menus .= '</ul>';