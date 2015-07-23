<?php namespace Nishen;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 22/07/2015
 * Time: 6:45 PM
 */


use Analog\Logger;

use Model\User;
use Model\Booking;
use Model\Preference;
use Model\Resource;
use Model\UserQuery;
use Model\BookingQuery;
use Model\PreferenceQuery;
use Model\ResourceQuery;

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
		$u = new UserQuery();
		$users = $u->find();

		$res->write($users->toJSON());
	}

	public function addUser(Request $req, Response $res)
	{
		$body = file_get_contents($req->getBody()->getMetadata('uri'));

		$user = new User();
		$user->fromJSON($body);
		$user->save();

		$res->write($user->toJSON());
	}

	public function getUser(Request $req, Response $res, $args)
	{
		$u = new UserQuery();
		$user = $u->findPk($args['id']);
		if ($user == NULL)
		{
			$res->withStatus(404, "resource not found")
				->write('{"error": {"code": 404, "mesg": "resource not found"}}');
		}
		else
		{
			$res->write($user->toJSON());
		}
	}

	public function modUser(Request $req, Response $res, $args)
	{
		$body = file_get_contents($req->getBody()->getMetadata('uri'));

		$u = new UserQuery();
		$user = $u->findPk($args['id']);
		if ($user == NULL)
		{
			$res->withStatus(404, "resource not found")
				->write('{"error": {"code": 404, "mesg": "resource not found"}}');
		}
		else
		{
			$user->fromJSON($body);
			$user->setId($args['id']);
			$user->save();
			$res->write($user->toJSON());
		}
	}

	public function delUser(Request $req, Response $res, $args)
	{
		$body = file_get_contents($req->getBody()->getMetadata('uri'));

		$u = new UserQuery();
		$user = $u->findPk($args['id']);
		if ($user == NULL)
		{
			$res->withStatus(404, "resource not found")
				->write('{"error": {"code": 404, "mesg": "resource not found"}}');
		}
		else
		{
			$user->delete();
		}
	}
}