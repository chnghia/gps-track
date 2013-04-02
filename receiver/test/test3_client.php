<html>
<head>
</head>
<body>
<?
// form not yet submitted
if (!$submit)
{
?>
<form action="<? echo $PHP_SELF; ?>" method="post">
Enter some text:<br>
<input type="Text" name="message" size="15"><input type="submit"
name="submit" value="Send">
</form>
<?
}
else
{
// form submitted
// where is the socket server?
$host="192.168.1.224";
$port = 8020;
// open a client connection
$fp = fsockopen ($host, $port, $errno, $errstr);
if (!$fp)
{
$result = "Error: could not open socket connection";
}
else
{
// get the welcome message
fgets ($fp, 1024);
// write the user string to the socket
fputs ($fp, $message);
// get the result
$result .= fgets ($fp, 1024);
// close the connection
fputs ($fp, "END");
fclose ($fp);
// trim the result and remove the starting ?
$result = trim($result);
$result = substr($result, 2);
// now print it to the browser
}
?>
Server said: <b><? echo $result; ?></b>
<?
}
?>
</body>
</html>