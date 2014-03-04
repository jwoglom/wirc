<?php
require_once 'websockets.php';
class ircServer extends WebSocketServer {
    protected function process($user, $message) {
        echo $message;
        list($uid, $msg) = explode("@", $message, 2);
        echo "\nMessage from $uid: $msg";
        if(substr($msg, 0, 5) == "conn:") {
            $m = substr($msg, 5);
            $j = json_decode($m);
            echo "Connecting:\n";
            print_r($j);
            self::ircc($uid, $j);
        }
        if(substr($msg, 0, 5) == "ping:") {
            $this->send($user, ircp($uid));
        }
    }
    protected $conns = [];
    function ircc($uid, $j) {
        $arg = escapeshellcmd($j->server)." ".escapeshellcmd($j->port)." ".escapeshellcmd($j->nick)." ".escapeshellcmd($j->realname)." ".escapeshellcmd($j->chan);
        echo "php ./telnet.php ".$arg.">./tmp".$uid." &";
        system("php ./telnet.php ".$arg.">./tmp".$uid." &");
        $conns[$uid] = 0;
        
    }
    function ircp($uid) {
        $f = file_get_contents("./tmp".$uid);
        $f = substr($f, $conns[$uid]);
        $conns[$uid] = sizeof($f);
        echo $f;
        return $f;

    }
    protected function connected($user) {

    }

    protected function closed($user) {

    }
}

$irc = new ircServer("0.0.0.0", "9002");
try {
    $irc->run();
} catch(Exception $e) {
    $irc->stdout($e->getMessage());
}
?>