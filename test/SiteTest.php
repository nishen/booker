<?php namespace Test;

	/**
	 * Created by PhpStorm.
	 * User: nishen
	 * Date: 2/07/2015
	 * Time: 12:30 AM
	 */

/*
 * Court 1: 4591
 * Court 2: 4592
 * Court 3: 4593
 * Court 4: 4594
 * Court 5: 4595
 * Court 6: 4596
 */

require_once __DIR__ . '/../app/config.php';

use Nishen\Booker;
use PHPUnit_Framework_TestCase;

class SiteTest extends PHPUnit_Framework_TestCase
{
	private static $log;

	private static $booker;

	public static function setUpBeforeClass()
	{
		global $log;

		self::$log = $log;

		self::$log->debug("starting test: " . date("Y-m-d H:i:s"));

		self::$booker = new Booker('nishen.naidoo@mq.edu.au', 'nishen');
	}

	public function testBooking()
	{
		$booker = self::$booker;

		self::$log->debug("logging in");
		$booker->getLoginPage();
		sleep(1);
		$location = $booker->login();
		$this->assertNotNull($location, "location header not set for dashboard");
		$this->assertTrue(strpos($location, 'dashboard') !== false, "location for dashboard is incorrect: $location");

		self::$log->debug("sleeping...");
		sleep(1);

		self::$log->debug("getting dashboard");
		$body = $booker->dashboard();
		$this->assertGreaterThan(1000, strlen($body), "dashboard contains no text");

		self::$log->debug("sleeping...");
		sleep(2);

		$numSlots = 4;
		$time = '2015-07-29 05:00pm';
		//$time = NULL;
		$facility = '753';
		self::$log->debug("getting availability");
		$page = $booker->getFacilityAvailability($time, $facility);
		$data = $booker->extractAvailabilityData($page);
		$this->assertNotNull($data, "no availability");

		self::$log->debug("finding slot");
		$slots = $booker->findSlots($data, $time, $numSlots);

		$this->assertGreaterThan(0, count($slots), 'slots not found!');

		$s = $booker->selectSlot($slots, [6, 5, 4, 3, 2, 1]);
		self::$log->debug("court: {$s['court']}, time: {$s['timeu']}, slots: {$s['slots']}");

		self::$log->debug("booking dialog");
		$res = $booker->bookingDialog($facility, $s['court'], $s['timeu']);
		self::$log->debug("response:\n{$res}\n");

		self::$log->debug("booking");
		$res = $booker->book($facility, $s['court'], $s['timeu'], $s['slots']);
		self::$log->debug("response:\n{$res}\n");
		$this->assertFalse(strpos($res, '"error":true'), $res);

		self::$log->debug("sleeping...");
		sleep(2);

		self::$log->debug("getting confirmation");
		$res = $booker->getConfirmation($facility, $s['timeu']);
		self::$log->debug("response:\n{$res}\n");

		self::$log->debug("confirming booking");
		$res = $booker->confirm($facility);
		self::$log->debug("response:\n{$res}\n");

		self::$log->debug("logging out");
		$res = $booker->logout();
		self::$log->debug("response:\n{$res}\n");
	}
}
