<?php
$m = $_POST["m"];

if($m =="conn") {
    $uid = time();
    echo $uid;
    $arg = escapeshellcmd($_POST['server'])." ".escapeshellcmd($_POST['port'])." ".escapeshellcmd($_POST['nick'])." ".escapeshellcmd($_POST['realname'])." ".escapeshellcmd($_POST['chan'])." ".$uid;
    touch("./tmp/tmp".$uid);
    touch("./tmp/input".$uid);
    $cmd = "php ./telnet.php ".$arg." >./tmp/tmp".$uid." &";
    $exc = shell_exec($cmd);
    echo $exc;
    die();
} else if($m == "ping") {
    $fc = file_get_contents("./tmp/tmp".$_POST['uid']);
    $s = substr($fc, $_POST['last']);
    die($s);
} else if($m == "send") {
    file_put_contents("./tmp/input".$_POST['uid'], "::".$_POST['msg']."\n\n");
}

?>