<?php

// Kickstart the framework
$f3=require('lib/base.php');

$f3->set('DEBUG',1);
if ((float)PCRE_VERSION<8.0)
    trigger_error('PCRE version is out of date');

// Load configuration

$f3->config('app/config/config.ini');
$f3->config('app/config/routes.ini');

// On Error
/*$f3->set('ONERROR',function($f3){
    echo \Template::instance()->render('error.htm');
    $e = $f3->get('EXCEPTION');
    if (!$e instanceof Throwable) {
        $logger = new Log('logs/'.date("Ymd").'error.log');
        $logger->write( $f3->get('ERROR.code') . ": ". $f3->get('ERROR.text'). " trace: ". $f3->get('ERROR.trace'),'r'  );
    }
});*/

$f3->logger = new Log('logs/'.date("Ymd").'.log');
$f3->session = new Session();
$f3->run();
