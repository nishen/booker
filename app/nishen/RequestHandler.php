<?php namespace Nishen;

/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 22/07/2015
 * Time: 6:45 PM
 */


use Analog\Logger;
use Model\User;
use Model\UserQuery;
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
		$u = new UserQuery();
		$users = $u->find();

		return RequestHelper::json($res, $users->toJSON());
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
			return RequestHelper::json($res, null, 422);
		}

		return RequestHelper::json($res, $user->toJSON(), 201)
							->withAddedHeader('Location', $req->getUri() . "/" . $user->getId());
	}

	public function getUser(Response $res, $args)
	{
		$u = new UserQuery();
		$user = $u->findPk($args['id']);
		$this->log->debug("user value:" . print_r($user, true));
		if ($user == null)
			return RequestHelper::json($res, null, 404);

		return RequestHelper::json($res, $user->toJSON());
	}

	public function modUser(Request $req, Response $res, $args)
	{
		$body = file_get_contents($req->getBody()->getMetadata('uri'));

		$u = new UserQuery();
		$user = $u->findPk($args['id']);
		if ($user == null)
			return RequestHelper::json($res, null, 404);

		try
		{
			$user->fromJSON($body);
			$user->setId($args['id']);
			$user->save();
		}
		catch (PropelException $e)
		{
			return RequestHelper::json($res, null, 409);
		}

		return RequestHelper::json($res, $user->toJSON(), 200);
	}

	public function delUser(Response $res, $args)
	{
		$u = new UserQuery();
		$user = $u->findPk($args['id']);
		if ($user == null)
			return RequestHelper::json($res, null, 404);

		$user->delete();

		return RequestHelper::json($res, $user->toJSON(), 200);
	}
}