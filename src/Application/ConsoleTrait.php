<?php

namespace SilexCLI\Application;

use SilexCLI\Command;


/**
 * Console trait
 */
trait ConsoleTrait {

	/**
	 * Adds a new command to the console and returns it.
	 *
	 * @param string $pattern Matched route pattern
	 * @param mixed  $to      Callback that returns the response when matched
	 *
	 * @return Command
	 */
	public function command($pattern, $to) {
		return $this['console']->match($pattern, $to);
	}
}
