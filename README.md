SilexCLI
========


## Services

### console
An instance of SilexCLI\Console (which extends Symfony\Component\Console\Application).

### console.input
An instance of Symfony\Component\Console\Input\ArgvInput, accessible to command callbacks.

### console.output
An instance of Symfony\Component\Console\Output\ConsoleOutput, accessible to command callbacks.

### console.status
Contains the exit code of the last console execution.


## Registering

```php
	use SilexCLI\Provider\ConsoleServiceProvider;

	$app->register(new ConsoleServiceProvider());
```


## Usage
Commands can be registered with the console's route method. The route pattern is used to identify arguments, and dependency injection is used to identify argument defaults and options.

If a callback parameter is present in the route pattern, it is defined as an argument. Parameters not present in the route pattern are 

### Command Arguments
If a callback parameter is present in the route pattern, it is defined as an argument. If it has a default value, then it will not be required by the console.
```php
	$app['console']->match('example {arg1}', function($arg1) use ($app) {
		...
	});
```

### Command Options
Callback parameters not present in the route pattern will be added to the command definition as options. All options must have a default value.
```php
	$app['console']->match('example', function($opt1 = 'default') use ($app) {
		...
	});
```

If the option default is a boolean value, it will be treated as a flag.
```php
	$app['console']->match('example', function($flag1 = false) use ($app) {
		...
	});
```

### Array Inputs
Arguments and options can accept array values as well (though only the last argument may be an array).
```php
	$app['console']->match('example {array-arg}', function(array $array_arg, array $array_opt = array()) use ($app) {
		...
	});
```


## Traits

SilexCLI\Application\ConsoleTrait adds the following shortcuts:

### command
Adds a new command to the console and returns it.
```php
	$app->command($pattern, $callback);
```