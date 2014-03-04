<?php
$fp = fsockopen("irc.bouncerstation.com", 6667, $errno, $errstr, 99999);
if($fp) {
    $out = ""
}