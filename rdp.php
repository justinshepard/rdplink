<?php
   header("Content-type: application/rdp");
   header("Content-disposition: attachment; filename=".$_GET['server'].".rdp");
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
full address:s:<?php echo $_GET['server']. "\n"?>
<?php if (!empty($_GET['domain'])) echo "domain:s:".$_GET['domain']."\n" ?>
<?php if (!empty($_GET['username'])) echo "username:s:".$_GET['username']."\n" ?>
<?php if (!empty($_GET['password'])) echo "password 51:b:".$_GET['password']."\n" ?>
