<?php
if (empty($_GET['server'])) {
    header("HTTP/1.1 400 Bad Request");
    echo "400 Bad Request: Missing server parameter.";
    exit;
}

$server = $_GET['server'];

if (!preg_match('/^[A-Za-z0-9.\-]+(:\d+)?$/', $server)) {
    header("HTTP/1.1 400 Bad Request");
    echo "400 Bad Request: Invalid server format.";
    exit;
}

$domain = !empty($_GET['domain']) ? preg_replace('/[^A-Za-z0-9.\-_]/', '', $_GET['domain']) : '';
$username = !empty($_GET['username']) ? preg_replace('/[^A-Za-z0-9.\-_]/', '', $_GET['username']) : '';
$password = !empty($_GET['password']) ? str_replace(array("\r", "\n"), '', $_GET['password']) : '';
$safe_filename = preg_replace('/[^A-Za-z0-9.\-]/', '_', $server);

header("Content-type: application/rdp");
header("Content-disposition: attachment; filename=\"" . $safe_filename . ".rdp\"");
header("X-Content-Type-Options: nosniff");
?>
screen mode id:i:2
desktopwidth:i:1280
desktopheight:i:1024
session bpp:i:16
winposstr:s:0,1,87,48,1119,843
compression:i:1
keyboardhook:i:2
audiomode:i:2
redirectdrives:i:0
redirectprinters:i:0
redirectcomports:i:0
redirectsmartcards:i:1
displayconnectionbar:i:1
autoreconnection enabled:i:1
alternate shell:s:
shell working directory:s:
disable wallpaper:i:1
disable full window drag:i:1
disable menu anims:i:1
disable themes:i:1
disable cursor setting:i:0
bitmapcachepersistenable:i:1
full address:s:<?php echo $server . "\n"; ?>
<?php if ($domain !== '') echo "domain:s:" . $domain . "\n"; ?>
<?php if ($username !== '') echo "username:s:" . $username . "\n"; ?>
<?php if ($password !== '') echo "password 51:b:" . $password . "\n"; ?>