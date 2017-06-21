<?php
## Config

set_time_limit (360);

ini_set('max_execution_time', 360);

ini_set('memory_limit', '128M');

ini_set('default_charset','UTF-8');

define('WMTekDydns', 'dyndns.local');

// session time out
define('MS_timeout' , (30*60));

// paths
define('MS_fullpath', get_full_url().'/');

define('MS_assetspath', 'assets/');

define('MS_tmp', 'attachs/tmp/');

// dates and local 
mb_internal_encoding('UTF-8'); 

date_default_timezone_set( "Africa/Cairo"); 


// Mysql Connection
define( 'MySql_HostName' , '127.0.0.1');
define( 'MySql_UserName' , 'csms');
define( 'MySql_Password' , 'webctrl/WMT');
