<?php namespace test;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 2/07/2015
 * Time: 12:30 AM
 */

require_once __DIR__ . '/../nishen/Booker.php';

use \nishen\Booker;
use \PHPUnit_Framework_TestCase;

class BookerTest extends PHPUnit_Framework_TestCase
{
    protected $booker;

    protected function setUp()
    {
        $this->booker = new Booker('', '');
    }

    public function testData()
    {
        $doc = file_get_contents('availability-sample.html');
        $res1 = $this->booker->extractAvailabilityData($doc);
        $res2 = $this->booker->findSlot($res1, '05:00pm', 4);
        $this->assertTrue(array_key_exists('court', $res2));
        $this->assertTrue(array_key_exists('time', $res2));
        $this->assertTrue(array_key_exists('slots', $res2));
   }
}
