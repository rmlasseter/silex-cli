<?php

namespace SilexCLI\Helper;

use Silex\Application;
use Symfony\Component\Console\Helper\Helper;


/**
 * A helper class for accessing the Silex application within commands.
 */
class SilexHelper extends Helper {
	private $app;

	/**
	 * Constructor.
	 *
	 * @param Application $app Silex Application
	 */
	public function __construct(Application $app) {
		$this->app = $app;
	}

	/**
	 * Returns a Silex Application object.
	 *
	 * @return Application
	 */
	public function app() {
		return $this->app;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'silex';
	}
}
