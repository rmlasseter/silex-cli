<?php

namespace SilexCLI\Application;

use SilexCLI\Command;


trait ConsoleTrait {

	public function command($pattern, $to) {
		return $this['console']->match($pattern, $to);
	}
}
