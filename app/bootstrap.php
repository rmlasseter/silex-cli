<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use Silex\Application;
use SilexCLI\Provider\ConsoleServiceProvider;


$app = new Application();

$app->register(new ConsoleServiceProvider());


return $app;
