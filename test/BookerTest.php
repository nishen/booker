<?php namespace test;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 2/07/2015
 * Time: 12:30 AM
 */

require_once __DIR__ . '/../app/config.php';

use \PHPUnit_Framework_TestCase;
use \nishen\Booker;

class BookerTest extends PHPUnit_Framework_TestCase
{
    private static $log;

    private static $booker;

    private static $data;


    public static function setUpBeforeClass()
    {
        global $log;

        self::$log = $log;

        self::$log->debug("starting test: " . date("Y-m-d H:i:s"));

        self::$data = file_get_contents('data/availability-sample.html');
        self::$booker = new Booker('', '');
    }

    public function testData()
    {
        $res1 = self::$booker->extractAvailabilityData(self::$data);
        $res2 = self::$booker->findSlot($res1, '05:00pm', 4);
        $this->assertTrue(array_key_exists('court', $res2));
        $this->assertTrue(array_key_exists('time', $res2));
        $this->assertTrue(array_key_exists('slots', $res2));
    }
}
