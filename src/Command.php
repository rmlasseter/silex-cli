<?php

namespace SilexCLI;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Command extends BaseCommand {
	private $callback;
	private $callbackParams;
	private $argnames;
	private $optnames;


	public function __construct($pattern, $to) {
		$this->callback = $to;
		$this->callbackParams = array();
		$this->argnames = array();
		$this->optnames = array();

		$parameters = array();
		if(is_array($this->callback)) {
			$method = new \ReflectionMethod($this->callback[0], $this->callback[1]);
			$parameters = $method->getParameters();
		} elseif(is_object($this->callback) && !$this->callback instanceof \Closure) {
			$object = new \ReflectionObject($this->callback);
			$method = $object->getMethod('__invoke');
			$parameters = $method->getParameters();
		} else {
			$function = new \ReflectionFunction($this->callback);
			$parameters = $function->getParameters();
		}

		foreach($parameters as $param) {
			//fix variable name
			$varName = str_replace('_', '-', $param->getName());
			$this->callbackParams[$varName] = $param;
		}

		


		$name = null;

		$words = preg_split('/\s+/', trim($pattern));
		if(count($words) > 0) {
			$name = array_shift($words);

			while(count($words) > 0) {
				$argname = strtolower(array_shift($words));
				if(substr($argname, 0, 1) == '{' && substr($argname, -1) == '}') {
					$argname = substr($argname, 1, -1);
				}

				if(strlen($argname) > 0) {
					$this->argnames[] = $argname;
				}
			}
		}

		foreach($this->callbackParams as $varName => $param) {
			if(!in_array($varName, $this->argnames) && $param->isOptional()) {
				$this->optnames[] = $varName;
			}
		}

		parent::__construct($name);
	}

	protected function configure() {
		$definition = new InputDefinition();

		foreach($this->argnames as $argname) {
			if(isset($this->callbackParams[$argname])) {
				$param = $this->callbackParams[$argname];

				if(!$param->getClass()) {
					$mode = 0;

					if($param->isOptional()) {
						$mode |= InputArgument::OPTIONAL;
					} else {
						$mode |= InputArgument::REQUIRED;
					}

					if($param->isArray()) {
						$mode |= InputArgument::IS_ARRAY;
					}

					$description = '';

					$default = null;
					if($param->isDefaultValueAvailable()) {
						$default = $param->getDefaultValue();
					}

					$definition->addArgument(new InputArgument($argname, $mode, $description, $default));
				}
			}
		}

		foreach($this->optnames as $optname) {
			if(isset($this->callbackParams[$optname])) {
				if(!$param->getClass()) {
					$shortcut = null;
					$mode = 0;
					$description = '';
					
					$default = $param->getDefaultValue();
					
					if(is_bool($default)) {
						$mode |= InputOption::VALUE_NONE;
					} else {
						$mode |= InputOption::VALUE_REQUIRED;
						
						if($param->isArray()) {
							$mode |= InputOption::VALUE_IS_ARRAY;
						}
					}

					$definition->addOption(new InputOption($optname, $shortcut, $mode, $description, $default));
				}
			}
		}

		$this->setDefinition($definition);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$arguments = $input->getArguments();

		$options = $input->getOptions();

		$objects = array(
			$input,
			$output,
			$this,
			$this->getApplication(),
			$this->getHelperSet(),
			$this->getHelper('dialog'),
			$this->getHelper('formatter'),
			$this->getHelper('silex'),
			$this->getHelper('silex')->app()
		);

		$callbackArgs = array();
		foreach($this->callbackParams as $name => $param) {
			if($param->getClass()) {
				foreach($objects as $object) {
					if($param->getClass()->isInstance($object)) {
						$callbackArgs[] = $object;
						break;
					}
				}
			} elseif(isset($arguments[$name])) {
				$callbackArgs[] = $arguments[$name];
			} elseif(isset($options[$name])) {
				$callbackArgs[] = $options[$name];
			} else {
				//missing parameter (probably due to some earlier failure)
			}
		}

		call_user_func_array($this->callback, $callbackArgs);
	}
}
