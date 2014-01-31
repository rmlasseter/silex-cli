<?php

namespace SilexCLI;

use SilexCLI\Command;
use SilexCLI\Helper\SilexHelper;

use Silex\Application as Application;
use Symfony\Component\Console\Application as BaseConsole;


class Console extends BaseConsole {
	private $app;

	public function __construct(Application $app) {
		$this->app = $app;

		$class = new \ReflectionClass($app);
		$name = str_replace('\\', ' :: ', $class->getNamespaceName());

		$version = $app::VERSION;

		parent::__construct($name, $version);

		$this->setAutoExit(false);
	}

	public function match($pattern, $to) {
		$command = new Command($pattern, $to);
		return $this->app['console']->add($command);
	}

	protected function getDefaultHelperSet() {
		$helperset = parent::getDefaultHelperSet();
		$helperset->set(new SilexHelper($this->app));
		return $helperset;
	}
}
