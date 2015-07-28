<?php namespace Test;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 2/07/2015
 * Time: 12:30 AM
 */

require_once __DIR__ . '/../app/config.php';

use Nishen\Booker;
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

		$time = '2015-07-03 05:00pm';
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
		$slots = self::$booker->findSlots($data, '2015-07-04 05:00pm', 4);
		$this->assertEquals(0, count($slots), 'found results incorrectly.');
	}
}
