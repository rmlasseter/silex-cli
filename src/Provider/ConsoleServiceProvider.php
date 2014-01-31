<?php

namespace SilexCLI\Provider;

use SilexCLI\Console;
use SilexCLI\ConsoleExceptionHandler;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;


class ConsoleServiceProvider implements ServiceProviderInterface {

	public function register(Application $app) {
		$app['console'] = $app->share(function(Application $app) {
			return new Console($app);
		});

		$app['console.input'] = $app->share(function() {
			return new ArgvInput();
		});

		$app['console.output'] = $app->share(function() use ($app) {
			return new ConsoleOutput();
		});

		$app['console.status'] = 0;
	}

	public function boot(Application $app) {
		if(php_sapi_name() == 'cli') {
			//replace default exception handler
			$app['exception_handler'] = $app->share(function() use ($app) {
				return new ConsoleExceptionHandler($app);
			});

			//discard existing controllers
			$app['controllers']->flush();

			//add route for the default request
			$app->get('/', function() use ($app) {
				//run console application
				$app['console.status'] = $app['console']->run(
					$app['console.input'],
					$app['console.output']
				);

				return new Response();
			});
		}
	}
}
