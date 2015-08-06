<?php namespace Nishen;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 22/07/2015
 * Time: 6:45 PM
 */


use Analog\Logger;
use DateTime;
use DateTimeZone;
use Exception;
use Model\Booking;
use Model\BookingQuery;
use Model\User;
use Model\UserQuery;
use Nishen\RequestHelper as H;
use Propel\Runtime\Exception\PropelException;
use Slim\Http\Request;
use Slim\Http\Response;

class RequestHandler
{
	private $log;

	public function __construct(Logger $log)
	{
		$this->log = $log;
	}

	public function processBookings(Response $res)
	{
		return H::json($res, null, 418);
	}

	public function makeBooking(Response $res, $args)
	{
		$this->log->debug(print_r($args, true));

		// check facility
		$facility = $args['facility'];
		if (!in_array($facility, [753]))
		{
			$err = [
				"code" => 400,
				"message" => "facility does not exist: {facility}",
			];

			return H::json($res, json_encode($err), 404);
		}

		// check time
		$time = $args['time'];
		$reqTime = new DateTime($time, new DateTimeZone("Australia/NSW"));
		if ($time != $reqTime->format('Y-m-d h:ia'))
		{
			$err = [
				"code" => 400,
				"message" => "incorrect date format: {$time}/" . $reqTime->format('Y-m-d h:ia'),
			];

			return H::json($res, json_encode($err), 400);
		}

		// check slots (2 to 4)
		$numSlots = $args['numSlots'];
		if ($numSlots < 2 || $numSlots > 4)
		{
			$err = [
				"code" => 400,
				"message" => "only support 2 to 4 slots: {$numSlots}",
			];

			return H::json($res, json_encode($err), 400);
		}

		try
		{
			$booker = new Booker('nishen.naidoo@mq.edu.au', 'nishen');
			$booker->bookResource($facility, $time, $numSlots);
		}
		catch (Exception $e)
		{
			$err = [
				"code" => $e->getCode(),
				"message" => $e->getMessage(),
			];

			return H::json($res, json_encode($err), $e->getCode());
		}

		return H::json($res, json_encode($args));
	}

	public function getUsers(Response $res)
	{
		$users = UserQuery::create()->find();

		return H::json($res, $users->toJSON());
	}

	public function addUser(Request $req, Response $res)
	{
		$body = file_get_contents($req->getBody()->getMetadata('uri'));

		$user = new User();
		$user->fromJSON($body);
		try
		{
			$user->save();
		}
		catch (PropelException $e)
		{
			return H::json($res, null, 422);
		}

		return H::json($res, $user->toJSON(), 201)
			->withAddedHeader('Location', $req->getUri() . "/" . $user->getId());
	}

	public function getUser(Response $res, $args)
	{
		$user = UserQuery::create()->findPk($args['id']);
		$this->log->debug("user value:" . print_r($user, true));
		if ($user == null)
			return H::json($res, null, 404);

		return H::json($res, $user->toJSON());
	}

	public function modUser(Request $req, Response $res, $args)
	{
		$body = file_get_contents($req->getBody()->getMetadata('uri'));

		$user = UserQuery::create()->findPk($args['id']);
		if ($user == null)
			return H::json($res, null, 404);

		try
		{
			$user->fromJSON($body);
			$user->setId($args['id']);
			$user->save();
		}
		catch (PropelException $e)
		{
			return H::json($res, null, 409);
		}

		return H::json($res, $user->toJSON(), 200);
	}

	public function delUser(Response $res, $args)
	{
		$user = UserQuery::create()->findPk($args['id']);
		if ($user == null)
			return H::json($res, null, 404);

		try
		{
			$user->delete();
		}
		catch (PropelException $e)
		{
			return H::json($res, null, 404);
		}

		return H::json($res, $user->toJSON(), 200);
	}

	public function getBookings(Response $res)
	{
		$bookings = BookingQuery::create()->find();

		return H::json($res, $bookings->toJSON());
	}

	public function addBooking(Request $req, Response $res)
	{
		$body = file_get_contents($req->getBody()->getMetadata('uri'));

		$booking = new Booking();
		$booking->fromJSON($body);
		try
		{
			$booking->save();
		}
		catch (PropelException $e)
		{
			return H::json($res, null, 422);
		}

		return H::json($res, $booking->toJSON(), 201)
			->withAddedHeader('Location', $req->getUri() . "/" . $booking->getId());
	}

	public function getBooking(Response $res, $args)
	{
		$booking = BookingQuery::create()->findPk($args['id']);
		$this->log->debug("user value:" . print_r($booking, true));
		if ($booking == null)
			return H::json($res, null, 404);

		return H::json($res, $booking->toJSON());
	}

	public function modBooking(Request $req, Response $res, $args)
	{
		$body = file_get_contents($req->getBody()->getMetadata('uri'));

		$booking = BookingQuery::create()->findPk($args['id']);
		if ($booking == null)
			return H::json($res, null, 404);

		try
		{
			$booking->fromJSON($body);
			$booking->setId($args['id']);
			$booking->save();
		}
		catch (PropelException $e)
		{
			return H::json($res, null, 409);
		}

		return H::json($res, $booking->toJSON(), 200);
	}

	public function delBooking(Response $res, $args)
	{
		$booking = BookingQuery::create()->findPk($args['id']);
		if ($booking == null)
			return H::json($res, null, 404);

		try
		{
			$booking->delete();
		}
		catch (PropelException $e)
		{
			return H::json($res, null, 404);
		}

		return H::json($res, $booking->toJSON(), 200);
	}
}