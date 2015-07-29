<?php namespace Test;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 25/07/2015
 * Time: 12:50 AM
 */

require_once __DIR__ . '/../app/config.php';

use Analog\Logger;
use GuzzleHttp\Client;
use Model\Booking;
use Model\User;
use PHPUnit_Framework_TestCase;

class RequestHandlerBookingTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Logger Logger instance
	 */
	private static $log;

	/**
	 * @var Client HTTP client
	 */
	private static $client;

	/**
	 * @var User the local test user that will be generated.
	 */
	private static $user;

	public static function setUpBeforeClass()
	{
		self::$log = $GLOBALS['log'];

		self::$log->debug("beginning test suite");
		self::$log->debug("====================");

		self::$client = new Client([
			'base_uri' => 'http://localhost/api/booker/v1/',
			'cookies' => true,
			'timeout' => 120.0,
			'verify' => false,
			//'proxy' => 'tcp://localhost:8888',
			'headers' => [
				'Accept' => 'application/json',
			],
			'allow_redirects' => false
		]);

		$userData = '{
			"Username": "nishen",
			"Password": "nishen",
			"Name": "Nishen Naidoo",
			"Email": "nish.naidoo@gmail.com",
			"Created": "2015-07-25 15:00:00"
		}';

		// create the user
		$res = self::$client->post("user?XDEBUG_SESSION_START=booker", [
			'body' => $userData
		]);

		$user = new User();
		$user->fromJSON(strval($res->getBody()));

		self::$user = $user;
	}

	public static function tearDownAfterClass()
	{
		// delete the user
		$id = self::$user->getId();
		self::$client->delete("user/{$id}");

		self::$log->debug("====================");
		self::$log->debug("completed test suite");
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

	public function testBookingCRD()
	{
		self::$log->debug("test: " . __METHOD__);

		$bookingData = '{
			"UserId":39,
			"Time":"2015-08-01T00:00:00Z",
			"Court":"6",
			"Status":"new",
			"Created":"2015-07-29T15:44:24Z"
		}';

		$booking = new Booking();
		$booking->fromJSON($bookingData);
		$booking->setUserId(self::$user->getId());

		// create the booking
		$bookingData = $booking->toJSON();
		$res = self::$client->post("booking?XDEBUG_SESSION_START=booker", [
			'body' => $bookingData
		]);

		$booking1 = strval($res->getBody());
		$this->assertEquals(201, $res->getStatusCode(), "incorrect status");
		$this->assertContains('"Id":', $booking1, "'Id' attribute not found");
		$this->assertContains('"UserId":', $booking1, "'UserId' attribute not found");

		// get the booking and check the data
		$booking->fromJSON($booking1);

		$bookingId = $booking->getId();
		$res = self::$client->get("booking/{$bookingId}");
		$booking2 = strval($res->getBody());
		$this->assertEquals(200, $res->getStatusCode(), "incorrect status");
		$this->assertContains('"Id":' . $bookingId, $booking2, "'Id:{$bookingId}' attribute not found");
		$this->assertContains('"UserId":', $booking2, "'Username' attribute not found");

		// delete the booking
		$res = self::$client->delete("booking/{$bookingId}");
		$this->assertEquals(200, $res->getStatusCode(), "incorrect status");

		// get the booking again to ensure it is gone.
		$this->setExpectedException('GuzzleHttp\Exception\ClientException', 'Client error: 404', 404);
		self::$client->get("booking/{$bookingId}");
	}

	public function testUserCUD()
	{
		self::$log->debug("test: " . __METHOD__);

		$bookingData = '{
			"UserId":39,
			"Time":"2015-08-01T00:00:00Z",
			"Court":"6",
			"Status":"new",
			"Created":"2015-07-29T15:44:24Z"
		}';

		// create the booking
		$booking = new Booking();
		$booking->fromJSON($bookingData);
		$booking->setUserId(self::$user->getId());

		// create the booking
		$bookingData = $booking->toJSON();
		$res = self::$client->post("booking?XDEBUG_SESSION_START=booker", [
			'body' => $bookingData
		]);

		$booking1 = strval($res->getBody());
		$this->assertEquals(201, $res->getStatusCode(), "incorrect status");
		$this->assertContains('"Id":', $booking1, "'Id' attribute not found");
		$this->assertContains('"UserId":', $booking1, "'UserId' attribute not found");

		// reload the booking
		$booking->fromJSON($booking1);
		$bookingId = $booking->getId();

		// update the booking
		$booking->setCourt(4);
		$bookingData = $booking->toJSON();
		$res = self::$client->put("booking/{$bookingId}?XDEBUG_SESSION_START=booker", [
			'body' => $bookingData
		]);

		$booking2 = strval($res->getBody());
		$this->assertEquals(200, $res->getStatusCode(), "incorrect status");
		$this->assertContains('"Id":', $booking2, "'Id' attribute not found");
		$this->assertContains('"UserId":', $booking2, "'UserId' attribute not found");
		$this->assertContains('"Court":"4"', $booking2, "'Court' not updated");

		// delete the booking
		$res = self::$client->delete("booking/{$bookingId}");
		$this->assertEquals(200, $res->getStatusCode(), "incorrect status");

		// get the booking again to ensure it is gone.
		$this->setExpectedException('GuzzleHttp\Exception\ClientException', 'Client error: 404', 404);
		self::$client->get("booking/{$bookingId}");
	}
}