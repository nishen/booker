<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/06/2015
 * Time: 7:00 PM
 */

require_once 'nishen/Booker.php';

use \nishen\Booker;

$booker = new Booker('nishen.naidoo@mq.edu.au', 'ishmael8');

$booker->viewLogin();

echo "\n------------------------------------------\n";

$location = $booker->login();
if (strpos($location, 'dashboard') === false)
    exit;

echo "\n------------------------------------------\n";

$booker->dashboard();

echo "\n------------------------------------------\n";

$booker->getFacilityAvailability();
