<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bootstrap\App;

// configure to continue processing even if the client disconnects
ignore_user_abort(true);

$app = new App();
$app->run();
