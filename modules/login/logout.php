<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1); 
session_start();
session_unset();
session_destroy();
session_write_close();
session_regenerate_id(true);
header('location:../../../index.php');
?>