<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 16/07/2015
 * Time: 11:25 PM
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/nishen/Booker.php';

use \Katzgrau\KLogger\Logger;
use \Psr\Log\LogLevel;

$log = new Logger(__DIR__ . '/log', LogLevel::DEBUG);
