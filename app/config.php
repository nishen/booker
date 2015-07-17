<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 16/07/2015
 * Time: 11:25 PM
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/nishen/Booker.php';

use Analog\Logger;
use Psr\Log\LogLevel;

/*
  Logging has these 4 parameters in order:
    machine, date, level, message
  using %1$s style (sprintf) formatting to modify

*/

$log = new Logger(__DIR__ . '/log', LogLevel::DEBUG);
$log->handler(Analog\Handler\Stderr::init());
$log->format("%2\$s [%3\$d]: %4\$s\n");
