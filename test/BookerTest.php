<?php namespace test;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 2/07/2015
 * Time: 12:30 AM
 */

require_once __DIR__ . '/../app/config.php';

use nishen\Booker;
use PHPUnit_Framework_TestCase;

class BookerTest extends PHPUnit_Framework_TestCase
{
	private static $log;

	private static $booker;

	private static $page;


	public static function setUpBeforeClass()
	{
		global $log;

		self::$log = $log;

		self::$log->debug("beginning test suite");
		self::$log->debug("====================");

		self::$page = file_get_contents('model/availability-sample.html');
		self::$booker = new Booker('', '');
	}

	public static function tearDownBeforeClass()
	{
		self::$log->debug("====================");
		self::$log->debug("completed test suite");

		self::$page = NULL;
		self::$booker = NULL;
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

		$time = '2015-07-03 05:00pm';
		$facility = '753';
		$page = self::$page;
		//$page = self::$booker->getFacilityAvailability($time, $facility);
		$data = self::$booker->extractAvailabilityData($page);
		$slots = self::$booker->findSlot($data, $time, 4);

		$this->assertGreaterThan(0, count($slots), '3 slots not found!');
		$this->assertTrue(array_key_exists('date', $slots[0]));
		$this->assertTrue(array_key_exists('court', $slots[0]));
		$this->assertTrue(array_key_exists('timeu', $slots[0]));
		$this->assertTrue(array_key_exists('times', $slots[0]));
		$this->assertTrue(array_key_exists('slots', $slots[0]));

		$s = $slots[0];
		self::$booker->bookingDialog($facility, $s['court'], $s['timeu'], $s['slots']);
	}

	public function testInvalidData()
	{
		self::$log->debug("test: " . __METHOD__);

		$data = self::$booker->extractAvailabilityData(self::$page);
		$slots = self::$booker->findSlot($data, '2015-07-04 05:00pm', 4);
		$this->assertEquals(0, count($slots), 'found results incorrectly.');
	}
}
