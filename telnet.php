<?php
$path = "/Users/james/Sites/wirc/";
$opts = array(
    "server" => "irc.bouncerstation.com",//$argv[1],
    "port" => $argv[2],
    "nick" => $argv[3],
    "realname" => $argv[4],
    "chan" => $argv[5],
    "uid" => $argv[6]
);
print_r($opts);
$ds = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("file", $path."tmp/error.txt", "a")
);
$process = proc_open('tail -f '.$path.'/tmp/input'.$opts['uid'].'&telnet '.$opts['server'].' '.$opts['port'], $ds, $pipes, $cwd, $env);
if(is_resource($process)) {
    echo "\n\n**Opened the process.\n\n";
    print_r($_SERVER);
    echo "my IP is ".$_SERVER['REMOTE_ADDR']."\n";
    echo "xfwfor: ".$_SERVER['HTTP_X_FORWARDED_FOR']."\n";
    echo "ip: ".getenv("REMOTE_ADDR")."\n";
    $readBuf = [];
    $readBufAt = -1;
    $connected = false;
    $joined = false;
    $newBuf = false;
    $cbuf = null;
    while(true) {
        usleep(100000);
        $stat = proc_get_status($process);
        if($stat['running']) {
            $r = fread($pipes[1], 4096);
            if($r) $readBuf[$readBufAt] = $r;
            echo $readBuf[$readBufAt];
            $cbuf = $readBuf[$readBufAt];
            $newBuf = true;
        }

        if(substr($readBuf[$readBufAt], 0, 4) == "PING") {
            //$b = explode($readBuf[$readBufAt], "\n");
            echo "\nPing request: ".$cbuf;
            $b = explode("PING :",$cbuf);
            if(isset($b[1])) {
                fwrite($pipes[0], "PONG :".$b[1]."\n");
            }
        }
        if(strpos($cbuf, "Found your hostname") !== false && !$connected) {
            $connected = true;
            //echo "\n\n**CONNECTED**\n\n";
            echo "\nLogging you in..\n";
            fwrite($pipes[0], "NICK ".$opts['nick']."\n");
            fwrite($pipes[0], "USER ".$opts['nick']." 8 * :".$opts['realname']."\n");
        }
        else if($connected && !$joined) {
            fwrite($pipes[0], "JOIN ".$opts['chan']."\n");
            $joined = true;
        } else if($joined) {
            // whee it's joined
            //echo "\nJoined.";
            //echo $cbuf."\n";
            if(substr($cbuf, 0, 2) == "::") {
                $c = substr($cbuf, 2);
                //echo "RUNNING: ".$c;
                fwrite($pipes[0], $c."\n");
                if($c == "QUIT") {
                    die();
                }
            }
            //fwrite($pipes[0], "PRIVMSG #telneta ".time()."\n");
        }
        if($newBuf) {
            $readBufAt++;
            $newBuf = false;
        }
    }
}