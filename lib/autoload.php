<?php
/**
 * Load classes as needed from the include path.
 * Underscores in the class name map to directories.
 * 
 * Example:
 * 
 *     Foo_Bar => Foo/Bar.php
 */

spl_autoload_register(function($class) {
	if ($class[0] == '_') {
		// classes beginning with '_' map to dir '/...', which would be bad
		return;
	}
	$file = str_replace('_', '/', $class).'.php';
	include $file;
});
