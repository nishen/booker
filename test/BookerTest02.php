<?php namespace Test;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 2/07/2015
 * Time: 12:30 AM
 */

require_once __DIR__ . '/../app/config.php';

use Analog\Logger;
use Nishen\Booker;
use PHPUnit_Framework_TestCase;

class BookerTest02 extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Logger
	 */
	private static $log;

	/**
	 * @var Booker
	 */
	private static $booker;

	private static $page;


	public static function setUpBeforeClass()
	{
		global $log;

		self::$log = $log;

		self::$log->debug("beginning test suite");
		self::$log->debug("====================");

		self::$page = file_get_contents('data/availability-sample.html');
		self::$booker = new Booker('', '');
	}

	public static function tearDownBeforeClass()
	{
		self::$log->debug("====================");
		self::$log->debug("completed test suite");

		self::$page = null;
		self::$booker = null;
	}

	public function setUp()
	{
		self::$log->debug("beginning test");
		self::$log->debug("-------------");
	}

	public function tearDown()
	{
		self::$log->debug("-------------");
		self::$log->debug("completed test");
	}

	public function testValidData()
	{
		self::$log->debug("test: " . __METHOD__);

		$time = '2015-08-01 02:00pm';
		$page = self::$page;
		$data = self::$booker->extractAvailabilityData($page);
		$slots = self::$booker->findSlots($data, $time, 4);

		$this->assertGreaterThan(0, count($slots), '3 slots not found!');

		$s = self::$booker->selectSlot($slots, array_reverse([1, 2, 3, 4, 5, 6]));
		self::$log->debug("selected slot: " . print_r($s, true));

		$this->assertTrue(array_key_exists('date', $s));
		$this->assertTrue(array_key_exists('court', $s));
		$this->assertTrue(array_key_exists('timeu', $s));
		$this->assertTrue(array_key_exists('times', $s));
		$this->assertTrue(array_key_exists('slots', $s));
	}

	public function testInvalidData()
	{
		self::$log->debug("test: " . __METHOD__);

		$data = self::$booker->extractAvailabilityData(self::$page);
		$slots = self::$booker->findSlots($data, '2015-08-01 06:00pm', 4);
		$this->assertEquals(0, count($slots), 'found results incorrectly.');
	}

	public function testCheckBookingValid()
	{
		$page = file_get_contents('data/dashboard-sample02.html');
		$slot = [
			"date" => "2016-12-10",
			"court" => 4595,
			"timeu" => 1481328000,
			"times" => "10:00am",
			"slots" => 4
		];

		$result = self::$booker->checkBooking($page, $slot);
		self::$log->debug("result: {$result}");
		$this->assertTrue($result, "booking not found");
	}

	public function testCheckBookingInvalid()
	{
		$page = file_get_contents('data/dashboard-sample02.html');
		$slot = [
			"date" => "2016-12-10",
			"court" => 4596,
			"timeu" => 1481328000,
			"times" => "10:00am",
			"slots" => 4
		];

		$result = self::$booker->checkBooking($page, $slot);
		self::$log->debug("result: {$result}");
		$this->assertFalse($result, "booking not found");
	}
}
