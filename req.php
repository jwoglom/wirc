<?php
$m = $_POST["m"];
if($m =="conn") {
    $uid = time();
    echo $uid;
    $arg = escapeshellarg($_POST['server'])." ".escapeshellarg($_POST['port'])." ".escapeshellarg($_POST['nick'])." ".escapeshellarg($_POST['realname'])." ".escapeshellarg($_POST['chan'])." ".$uid;
    touch("./tmp/tmp".$uid);
    touch("./tmp/input".$uid);
    $cmd = "php ./telnet.php ".$arg." >./tmp/tmp".$uid." &";
    $exc = shell_exec($cmd);
    echo $exc;
    clearold();
    die();
} else if($m == "ping") {
    ping($_POST['uid'], $_POST['last']);
} else if($m == "send") {
    send($_POST['uid'], $_POST['msg']);
    clearold();
} else if(isset($_GET['clear'])) clearold();
function ping($uid, $last) {
    file_put_contents("./tmp/ping_".$uid, time());
    $fc = file_get_contents("./tmp/tmp".$uid);
    $s = substr($fc, $last);
    die($s);
}
function send($uid, $msg) {
    $msg = "such not allowed";
    file_put_contents("./tmp/input".$uid, "::".$msg."\n\n");
}
function clearold() {
    $o = shell_exec("ls ./tmp|grep ping_|sed 's/ping_//g'");
    $l = explode("\n", $o);
    foreach($l as $id) {
        if(file_exists("./tmp/ping_".$id)) {
            $f = file_get_contents("./tmp/ping_".$id);
            //echo $id." ".(time() - $f)." \n";
            if(time() - $f > 60) {
                send($id, "QUIT");
                shell_exec("rm ./tmp/ping_".$id);
                shell_exec("rm ./tmp/input".$id);
                shell_exec("rm ./tmp/tmp".$id);
            }
        }
    }
}

?>