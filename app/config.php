<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 16/07/2015
 * Time: 11:25 PM
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/model/config.php';
require_once __DIR__ . '/nishen/Booker.php';
require_once __DIR__ . '/nishen/RequestHandler.php';

use Analog\Analog;
use Analog\Logger;
use Analog\Handler\Multi;
use Analog\Handler\File;
use Analog\Handler\Stderr;

/*
 * ==========================================================================
 *  Logging Config
 * ==========================================================================
 */
Analog::$default_level = Analog::DEBUG;

$logFile = 'D:/Temp/php.log';
$log = new Logger;
$log->handler(
	Multi::init([
		Analog::DEBUG => [
			Stderr::init(),
			File::init($logFile)
		],
		Analog::INFO => [
			File::init($logFile)
		]
	])
);

/*
  Logging has these 4 parameters in order:
    machine, date, level, message
  using %1$s style (sprintf) formatting to modify
*/
$log->format("%2\$s [%3\$d]: %4\$s\n");
