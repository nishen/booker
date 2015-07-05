<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/06/2015
 * Time: 7:00 PM
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/nishen/Booker.php';

use \Katzgrau\KLogger\Logger;
use \Psr\Log\LogLevel;
use \nishen\Booker;

$log = new Logger(__DIR__ . '/log', LogLevel::DEBUG);
$log->debug("instantiated class...");

$booker = new Booker('nishen.naidoo@mq.edu.au', 'nishen');

$booker->getLoginPage();
$location = $booker->login();

if ($location == null || strpos($location, 'dashboard') === false)
{
    $log->error("login failure...");
    exit;
}

$booker->dashboard();
$availability = $booker->getFacilityAvailability(new DateTime('2015-07-06 05:00pm'));
$slot = $booker->findSlot($availability, '05:00pm', 2);
if ($slot == null)
{
    echo "no slot found" . PHP_EOL;
} else
{
    echo "court: {$slot['court']}, time: {$slot['time']}, slots: {$slot['slots']}" . PHP_EOL;
}
