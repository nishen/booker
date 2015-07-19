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

	public function testLogin()
	{
		$booker = self::$booker;

		self::$log->debug("logging in");
		$location = $booker->login();
		$this->assertNotNull($location, "location header not set for dashboard");
		$this->assertTrue(strpos($location, 'dashboard') !== FALSE, "location for dashboard is incorrect: $location");

		self::$log->debug("sleeping...");
		sleep(1);

		self::$log->debug("getting dashboard");
		$body = $booker->dashboard();
		$this->assertGreaterThan(1000, strlen($body), "dashboard contains no text");

		self::$log->debug("sleeping...");
		sleep(2);

		//$time = '2015-07-22 05:00pm';
		$time = NULL;
		$facility = '753';
		self::$log->debug("getting availability");
		$page = $booker->getFacilityAvailability($time, $facility);
		$data = $booker->extractAvailabilityData($page);
		$this->assertNotNull($data, "no availability");

		self::$log->debug("finding slot");
		$slots = $booker->findSlot($data, $time, 4);

		$this->assertGreaterThan(0, count($slots), 'slots not found!');

		$s = $slots[0];
		self::$log->debug("court: {$s['court']}, time: {$s['timeu']}, slots: {$s['slots']}");

		self::$log->debug("booking dialog");
		$res = $booker->bookingDialog($facility, $s['court'], $s['timeu']);
		self::$log->debug("response:\n{$res}\n");

		self::$log->debug("booking");
		$res = $booker->book($facility, $s['court'], $s['timeu'], $s['slots']);
		self::$log->debug("response:\n{$res}\n");

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
