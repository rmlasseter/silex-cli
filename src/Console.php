<?php

namespace SilexCLI;

use SilexCLI\Command;
use SilexCLI\Helper\SilexHelper;

use Silex\Application as Application;
use Symfony\Component\Console\Application as BaseConsole;


/**
 * Symfony Console for Silex
 */
class Console extends BaseConsole {
	private $app;

	/**
	 * Constructor.
	 *
	 * @param Application $app Silex Application
	 */
	public function __construct(Application $app) {
		$this->app = $app;

		$class = new \ReflectionClass($app);
		$name = str_replace('\\', ' :: ', $class->getNamespaceName());

		$version = $app::VERSION;

		parent::__construct($name, $version);

		$this->setAutoExit(false);
	}

	/**
	 * Adds a new command to the console and returns it.
	 *
	 * @param string $pattern Matched route pattern
	 * @param mixed  $to      Callback that returns the response when matched
	 *
	 * @return Command
	 */
	public function match($pattern, $to) {
		$command = new Command($pattern, $to);
		return $this->app['console']->add($command);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultHelperSet() {
		$helperset = parent::getDefaultHelperSet();
		$helperset->set(new SilexHelper($this->app));
		return $helperset;
	}
}
