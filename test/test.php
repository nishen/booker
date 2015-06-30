<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 30/06/2015
 * Time: 12:45 AM
 */

require_once __DIR__ . '/../nishen/Booker.php';

use \nishen\Booker;

$booker = new Booker('', '');

$doc = file_get_contents('availability-sample.html');
$data = $booker->extractAvailabilityData($doc);

$booker->findSlot($data, '05:00pm', 4);

//print_r($data);

/*
[8] => Array
(
    [0] => <a href="/customer/mobile/facility/book_dialog/1435793400/4591" data-rel="dialog">09:30am</a>
    [1] => 1435793400
    [2] => 4591
    [3] => 09:30am
)
*/