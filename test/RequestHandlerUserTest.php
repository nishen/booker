<?php namespace Test;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 25/07/2015
 * Time: 12:50 AM
 */

require_once __DIR__ . '/../app/config.php';

use GuzzleHttp\Client;
use Model\User;
use PHPUnit_Framework_TestCase;

class RequestHandlerUserTest extends PHPUnit_Framework_TestCase
{
	private static $log;

	private static $client;

	public static function setUpBeforeClass()
	{
		global $log;

		self::$log = $log;

		self::$log->debug("beginning test suite");
		self::$log->debug("====================");

		self::$client = new Client([
			'base_uri' => 'http://localhost/v1/',
			'cookies' => TRUE,
			'timeout' => 120.0,
			'verify' => FALSE,
			'proxy' => 'tcp://localhost:8888',
			'headers' => [
				'Accept' => 'application/json',
			]
		]);
	}

	public static function tearDownBeforeClass()
	{
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

	public function testUserAdd()
	{
		self::$log->debug("test: " . __METHOD__);

		$body = '{
			"Username": "nishen",
			"Password": "nishen",
			"Name": "Nishen Naidoo",
			"Email": "nish.naidoo@gmail.com",
			"Created": "2015-07-25 15:00:00"
		}';

		// create the user
		$res = self::$client->post("user?XDEBUG_SESSION_START=booker", [
			'body' => $body
		]);
		$user1 = strval($res->getBody());
		self::$log->debug($user1);
		$this->assertEquals(200, $res->getStatusCode(), "incorrect status");
		$this->assertContains('"Id":', $user1, "'Id' attribute not found");
		$this->assertContains('"Username":', $user1, "'Username' attribute not found");

		$user = new User();
		$user->fromJSON($user1);
		self::$log->debug("User: " . print_r($user, TRUE));

		$id = $user->getId();

		// get the user and check the data
		$res = self::$client->get("user/{$id}");
		$user2 = strval($res->getBody());
		self::$log->debug($user2);
		$this->assertEquals(200, $res->getStatusCode(), "incorrect status");
		$this->assertContains('"Id":' . $id, $user2, "'Id:{$id}' attribute not found");
		$this->assertContains('"Username":', $user2, "'Username' attribute not found");


		// delete the user
		$res = self::$client->delete("user/{$id}");
		$this->assertEquals(200, $res->getStatusCode(), "incorrect status");

		$res = self::$client->get("user/{$id}");
		$this->assertEquals(404, $res->getStatusCode(), "user seems to have been found.");
	}
}