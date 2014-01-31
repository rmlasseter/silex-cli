<?php

use Silex\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$app = include(dirname(__FILE__) . '/bootstrap.php');

$app->match('/', function() {
	return '<h2>SilexCLI</h2>';
});

$app['console']->match('echo {string}', function(OutputInterface $output, $string) {
	$output->writeln($string);
});


return $app;
