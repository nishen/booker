<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/06/2015
 * Time: 7:00 PM
 */

require_once __DIR__ . '/../app/config.php';

/*
Activities:
  register
  login
  logout
  token auth
  schedule booking
*/

use Nishen\RequestHandler;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

$app = new App();
$h = new RequestHandler($log);

$app->get('/', function (Request $req, Response $res)
{
	$res->withStatus(200)
		->write("<h1>API ENDPOINT</h1>")
		->write("<ul>")
		->write("<li>user</li>")
		->write("<li>preference</li>")
		->write("<li>booking</li>")
		->write("<li>resource</li>")
		->write("</ul>");
});

$app->get('/user', function (Request $req, Response $res) use ($h)
{
	return $h->getUsers($res);
});

$app->post('/user', function (Request $req, Response $res) use ($h)
{
	return $h->addUser($req, $res);
});

$app->get('/user/{id}', function (Request $req, Response $res, $args) use ($h)
{
	return $h->getUser($res, $args);
});

$app->put('/user/{id}', function (Request $req, Response $res, $args) use ($h)
{
	return $h->modUser($req, $res, $args);
});

$app->delete('/user/{id}', function (Request $req, Response $res, $args) use ($h)
{
	return $h->delUser($res, $args);
});

$app->run();
