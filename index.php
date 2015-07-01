<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/06/2015
 * Time: 7:00 PM
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/nishen/Booker.php';

use \nishen\Booker;

$booker = new Booker('nishen.naidoo@mq.edu.au', 'ishmael8');

$booker->getLoginPage();
$location = $booker->login();
if (strpos($location, 'dashboard') === false)
    exit;
$booker->dashboard();
$booker->getFacilityAvailability();
