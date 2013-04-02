#!/usr/bin/php -Cq
<?

ini_set('mbstring.func_overload', '0');
ini_set('output_handler', '');

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

@ob_end_flush();
set_time_limit(0);
include("phpsocketdaemon/socket.php");
include("gpsServer.php");

$daemon = new socketDaemon();
$server = $daemon->create_server('gpsServer', 'gpsServerClient', 0, 8020);
$daemon->process();