<?php

class MyApi
{
    public function Index($server){
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = @$_SERVER['REMOTE_ADDR'];
        if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
        elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
        else $ip = $remote;

        if(session_status() == PHP_SESSION_NONE) session_start();
        elseif(session_status() == PHP_SESSION_NONE) die('The session mechanism is disabled in your server settings. The app cannot be launched!');
        $_SESSION[session_id()] = $ip;
        session_write_close();
    }
}