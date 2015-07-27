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
$rh = new RequestHandler($log);

$app->get('/user', function (Request $req, Response $res) use ($rh)
{
	$rh->getUsers($res);
});

$app->post('/user', function (Request $req, Response $res) use ($rh)
{
	$rh->addUser($req, $res);
});

$app->get('/user/{id}', function (Request $req, Response $res, $args) use ($rh)
{
	$rh->getUser($res, $args);
});

$app->put('/user/{id}', function (Request $req, Response $res, $args) use ($rh)
{
	$rh->modUser($req, $res, $args);
});

$app->delete('/user/{id}', function (Request $req, Response $res, $args) use ($rh)
{
	$rh->delUser($res, $args);
});

$app->run();
