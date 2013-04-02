<?
// don't timeout!
set_time_limit(0);
// set some variables
$host = "192.168.1.224";
$port = 8020;
$command = "/usr/games/fortune";
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// bind socket to port
$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
// start listening for connections
$result = socket_listen($socket, 3) or die("Could not set up socket listener\n");
echo "Waiting for connections...\n";
// accept incoming connections
// spawn another socket to handle communication
$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
echo "Received connection request\n";
// run command and send back output
$output = `$command`;
socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
echo "Sent output: $output\n";
// close sockets
socket_close($spawn);
socket_close($socket);
echo "Socket terminated\n";
?>