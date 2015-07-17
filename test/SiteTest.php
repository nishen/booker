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

        self::$log->debug("logging in...");

        $location = $booker->login();
        $this->assertNotNull($location, "location header not set for dashboard");
        $this->assertTrue(strpos($location, 'dashboard') !== false, "location for dashboard is incorrect: $location");

        self::$log->debug("sleeping...");
        sleep(1);

        self::$log->debug("getting dashboard...");
        $body = $booker->dashboard();
        $this->assertGreaterThan(1000, strlen($body), "dashboard contains no text");

        self::$log->debug("sleeping...");
        sleep(3);

        self::$log->debug("getting availability...");
        $availability = $booker->getFacilityAvailability();
        $this->assertNotNull($availability, "no availability");

        self::$log->debug("finding slot...");
        $slot = $booker->findSlot($availability, '05:00pm', 4);

        $this->assertNotNull($slot, "no slots found...");
        self::$log->debug("court: {$slot['court']}, time: {$slot['time']}, slots: {$slot['slots']}");
    }
}
