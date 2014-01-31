<?php

namespace SilexCLI;

use Silex\Application;
use Silex\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;


/**
 * Replaces standard silex exception handler for CLI requests.
 */
class ConsoleExceptionHandler extends ExceptionHandler {
	protected $app;

	/**
	 * Constructor.
	 *
	 * @param Application $app Silex Application
	 */
	public function __construct(Application $app) {
		$this->app = $app;

		parent::__construct($app['debug']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function onSilexError(GetResponseForExceptionEvent $event) {
		$exception = $event->getException();
		
		$code = $exception->getCode();
		if(is_numeric($code)) {
			$code = max(1, intval($code));
		} else {
			$code = 1;
		}

		$this->app['console.status'] = $code;

		$this->app['console']->renderException($event->getException(), $this->app['console.output']);

		$event->setResponse(new Response());
	}
}
