<?php

namespace monitor;

$config = parse_ini_file('../config.ini') or die ("Missing config");
set_include_path($_SERVER['DOCUMENT_ROOT'].$config['app_root']) or die ("Couldn't initialize app");

require_once ('app/Application.php');
$app = new Application($config);
$app->handleReqeust();
?>


