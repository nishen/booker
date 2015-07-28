<?php namespace Nishen;
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/06/2015
 * Time: 6:56 PM
 */

require_once __DIR__ . '/../config.php';

use DateTime;
use DateTimeZone;
use Exception;
use GuzzleHttp\Client;

class Booker
{
	private static $log;

	private $username;

	private $password;

	private $client;

	private $courtMap;

	function __construct($username, $password)
	{
		global $log;

		self::$log = $log;

		$this->username = $username;
		$this->password = $password;

		$this->client = new Client([
			'base_uri' => 'https://secure.activecarrot.com/customer/mobile/',
			'timeout' => 120.0,
			'cookies' => true,
			'verify' => false,
			//'proxy' => 'tcp://localhost:8888',
			'headers' => [
				'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
				'Accept-Encoding' => 'gzip, deflate',
				'Accept-Language' => 'en-AU,en;q=0.8,en-GB;q=0.6,en-US;q=0.4',
				'Cache-Control' => 'no-cache',
				'Connection' => 'keep-alive',
				'Host' => 'secure.activecarrot.com',
				'Pragma' => 'no-cache',
				'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4'
			]
		]);

		$this->courtMap = [
			4591 => 1,
			4592 => 2,
			4593 => 3,
			4594 => 4,
			4595 => 5,
			4596 => 6
		];
	}

	public function getLoginPage()
	{
		$res = $this->client->get('login?site=382');

		return strval($res->getBody());
	}

	public function login()
	{
		$result = null;

		$res = $this->client->post('login', [
			'form_params' => [
				'username' => $this->username,
				'password' => $this->password,
				'submit' => 'submit-value'
			],
			'headers' => [
				'Referer' => 'https://secure.activecarrot.com/customer/mobile/login?site=382',
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Origin' => 'https://secure.activecarrot.com'
			],
			'allow_redirects' => false
		]);

		switch ($res->getStatusCode())
		{
			case 200:
				$result = null;
				break;

			case 301:
			case 302:
			case 303:
			case 307:
			case 308:
				$result = $res->getHeader("Location")[0];
				break;
		}

		return $result;
	}

	public function logout()
	{
		$res = $this->client->get('login/logout');

		return strval($res->getBody());
	}

	public function dashboard()
	{
		$result = $this->client->get('dashboard', [
			'headers' => [
				'Accept-Encoding' => 'gzip, deflate, sdch',
				'Referer' => 'https://secure.activecarrot.com/customer/mobile/login?site=382'
			],
			'allow_redirects' => false
		]);

		return strval($result->getBody());
	}

	public function getFacilityAvailability($time = null, $facility = '753')
	{
		$result = null;

		$date = $time == null ? new DateTime() : new DateTime($time);
		if ($time == null)
		{
			$date->modify('+2 days');
			if ($date->format('N') > 5)
			{
				$date->setTime(10, 0, 0);
			}
			else
			{
				$date->setTime(17, 0, 0);
			}
		}
		else
		{
			$tmp = new DateTime();
			$tmp->modify('+3 days');
			$tmp->setTime(0, 0, 0);

			if ($date > $tmp)
			{
				throw new Exception("date+time exceeds booking window: {$date->format('Y-m-d H:i:s')}");
			}
		}

		$endpoint = 'facility/browse/' . $facility . '/' . $date->format('Y-m-d');

		$res = $this->client->get($endpoint, [
			'headers' => [
				'Accept' => 'text/html, */*; q=0.01',
				'Accept-Encoding' => 'gzip, deflate, sdch',
				'Referer' => 'https://secure.activecarrot.com/customer/mobile/dashboard',
				'X-Requested-With' => 'XMLHttpRequest'
			],
			'allow_redirects' => false
		]);

		return strval($res->getBody());
	}

	public function extractAvailabilityData($doc)
	{
		$result = preg_match_all('|<a href="/customer/mobile/facility/book_dialog/(\d+)/(\d+)".*>(.{7})</a>|', $doc, $data, PREG_SET_ORDER);

		return $result > 0 ? $data : null;
	}

	public function findSlots($data, $time, $slots = 4)
	{
		$courts = [];
		foreach ($data as $item)
		{
			$courts[$item[2]]['timeu'][] = $item[1];
			$courts[$item[2]]['times'][] = $item[3];
		}

		//$startTime = '2015-07-03 05:00pm';
		$date = new DateTime($time, new DateTimeZone("Australia/NSW"));
		self::$log->debug("using date: {$date->format('c')}");

		$timeSlotsRequired = [];
		for ($x = 0; $x < $slots; $x++)
		{
			$timeSlotsRequired[] = $date->format("U");
			self::$log->debug("timeSlotDate: {$date->format('U')}");
			$date->modify('+30 MINUTES');
		}

		$result = [];
		foreach ($courts as $id => $court)
		{
			$intersect = array_intersect($court['timeu'], $timeSlotsRequired);
			if (count($intersect) == count($timeSlotsRequired))
			{
				reset($intersect);
				$result[] = [
					'date' => $date->format('Y-m-d'),
					'court' => $id,
					'timeu' => $court['timeu'][key($intersect)],
					'times' => $court['times'][key($intersect)],
					'slots' => $slots
				];
				self::$log->debug("results: " . print_r($result, true));
			}
		}

		if (count($result) == 0)
		{
			self::$log->debug("no slot found: " . $time);
			$result = null;
		}

		return $result;
	}

	public function bookingDialog($facility, $resource, $time)
	{
		$date = new DateTime('@' . $time, new DateTimeZone("Australia/NSW"));

		// https://secure.activecarrot.com/customer/mobile/facility/book_dialog/1437375600/4591
		$endpoint = "facility/book_dialog/{$time}/{$resource}";
		self::$log->debug("endpoint: {$endpoint}");

		$referer = $this->client->getConfig('base_uri') . "facility/browse/{$facility}/{$date->format('Y-m-d')}";
		self::$log->debug("referer: {$referer}");

		$result = $this->client->get($endpoint, [
			'headers' => [
				'Accept' => 'text/html, */*; q=0.01',
				'Accept-Encoding' => 'gzip, deflate, sdch',
				'Referer' => $referer,
				'X-Requested-With' => 'XMLHttpRequest'
			],
			'allow_redirects' => false
		]);

		return strval($result->getBody());
	}

	public function book($facility, $resource, $time, $slots)
	{
		$date = new DateTime('@' . $time, new DateTimeZone("Australia/NSW"));

		if ($slots < 2) $slots = 2;
		if ($slots > 4) $slots = 4;

		// endpoint: https://secure.activecarrot.com/customer/mobile/facility/book_dialog/1437375600/4591
		$endpoint = "facility/book_ajax";
		self::$log->debug("endpoint: {$endpoint}");

		// referer: https://secure.activecarrot.com/customer/mobile/facility/browse/753/2015-07-20
		$referer = $this->client->getConfig('base_uri') . "facility/browse/{$facility}/{$date->format('Y-m-d')}";
		self::$log->debug("referer: {$referer}");

		$endpoint = 'facility/book_ajax';

		$res = $this->client->post($endpoint, [
			'form_params' => [
				'site_facility_id' => $resource,
				'booking_time' => $time,
				'event_duration' => ($slots * 30)
			],
			'headers' => [
				'Accept' => 'application/json, text/javascript, */*; q=0.01',
				'Accept-Encoding' => 'gzip, deflate',
				'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
				'Origin' => 'https://secure.activecarrot.com',
				'Referer' => $referer,
				'X-Requested-With' => 'XMLHttpRequest'
			]
		]);

		self::$log->debug("headers: {$referer}");

		return strval($res->getBody());
	}

	public function getConfirmation($facility, $time)
	{
		$date = new DateTime('@' . $time, new DateTimeZone("Australia/NSW"));

		// https://secure.activecarrot.com/customer/mobile/facility/book_dialog/1437375600/4591
		$endpoint = "facility/confirm";
		self::$log->debug("endpoint: {$endpoint}");

		$referer = $this->client->getConfig('base_uri') . "facility/browse/{$facility}/{$date->format('Y-m-d')}";
		self::$log->debug("referer: {$referer}");

		$result = $this->client->get($endpoint, [
			'headers' => [
				'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
				'Accept-Encoding' => 'gzip, deflate, sdch',
				'Referer' => $referer
			],
			'allow_redirects' => false
		]);

		return strval($result->getBody());
	}

	public function confirm($resource)
	{
		$endpoint = "facility/confirm";
		self::$log->debug("endpoint: {$endpoint}");

		$referer = "facility/confirm";
		self::$log->debug("referer: {$referer}");

		$res = $this->client->post($endpoint, [
			'form_params' => [
				'site_facility_id' => $resource,
				'submit' => 'Confirm+Booking'
			],
			'headers' => [
				'Accept' => 'application/json, text/javascript, */*; q=0.01',
				'Accept-Encoding' => 'gzip, deflate',
				'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
				'Origin' => 'https://secure.activecarrot.com',
				'Referer' => $referer,
				'X-Requested-With' => 'XMLHttpRequest'
			]
		]);

		self::$log->debug("headers: {$referer}");

		return strval($res->getBody());
	}

	public function selectSlot($slots, $order)
	{
		foreach ($order as $o)
		{
			foreach ($slots as $s)
			{
				if ($o == $this->courtMap[$s['court']])
				{
					return $s;
				}
			}
		}

		return null;
	}
}