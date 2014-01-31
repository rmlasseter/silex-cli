<?php

namespace SilexCLI\Helper;

use Silex\Application;
use Symfony\Component\Console\Helper\Helper;


class SilexHelper extends Helper {
	private $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}

	public function app() {
		return $this->app;
	}

	public function getName() {
		return 'silex';
	}
}
