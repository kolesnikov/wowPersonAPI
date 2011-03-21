<?
$autoload = function ($class) {

	$path = explode('\\', $class);

	if ( 'WOWAPI' != array_shift($path) )
		throw new \WOW\EXCEPTIONS\SYSTEM\NamespaceIsWrong();

	$filename = array_pop($path);

	require __DIR__ . DIRECTORY_SEPARATOR .
		implode(DIRECTORY_SEPARATOR, array_map('strtolower', $path)) . DIRECTORY_SEPARATOR . 
		$filename . '.php';

};

spl_autoload_register($autoload);
require __DIR__ . '/settings.php';
