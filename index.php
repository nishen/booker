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
$booker->get();
echo "\n\n\n";
$booker->login();