<?php
$ds = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("file", "/tmp/error.txt", "a")
);
$process = proc_open('tail -f ./input&telnet irc.bouncerstation.com 6667', $ds, $pipes, $cwd, $env);
if(is_resource($process)) {
    echo "\n\n**Opened the process.\n\n";
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
            echo "\n\n**CONNECTED**\n\n";
            fwrite($pipes[0], "NICK telnet_test".time()."\n");
            fwrite($pipes[0], "USER telnet_test".time()." 8 * :RealName\n");
        }
        else if($connected && !$joined) {
            fwrite($pipes[0], "JOIN #telneta\n");
            $joined = true;
        } else if($joined) {
            // whee it's joined
            echo "\nJoined.";
            echo $cbuf."\n";
            if(substr($cbuf, 0, 2) == "::") {
                $c = substr($cbuf, 2);
                echo "RUNNING: ".$c;
                fwrite($pipes[0], $c."\n");
            }
            //fwrite($pipes[0], "PRIVMSG #telneta ".time()."\n");
        }
        if($newBuf) {
            $readBufAt++;
            $newBuf = false;
        }
    }
}