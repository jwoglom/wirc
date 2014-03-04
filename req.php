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
    ping($_POST['uid'], $_POST['last']);
} else if($m == "send") {
    send($_POST['uid'], $_POST['msg']);
    clearold();
}
function ping($uid, $last) {
    file_put_contents("./tmp/ping_".$uid, time());
    $fc = file_get_contents("./tmp/tmp".$uid);
    $s = substr($fc, $last);
    die($s);
}
function send($uid, $msg) {
    file_put_contents("./tmp/input".$uid, "::".$msg."\n\n");
}
function clearold() {
    $o = shell_exec("ls ./tmp|grep ping_|sed 's/ping_//g'");
    $l = explode("\n", $o);
    foreach($l as $id) {
        $f = file_get_contents("./tmp/ping_".$id);
        if(time() - $f > 60000) {
            send($id, "QUIT");
        }
    }
}

?>