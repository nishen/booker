<?php namespace Nishen;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 22/07/2015
 * Time: 6:45 PM
 */


use Analog\Logger;
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