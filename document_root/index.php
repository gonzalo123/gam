<?php
header('Content-Type: text/html; charset=utf-8');
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'application.php';

$zfApp = new ZfApplication;
$zfApp->setEnvironment('staging');
$zfApp->bootstrap();
