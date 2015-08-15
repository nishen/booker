<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/06/2015
 * Time: 7:00 PM
 */

require_once __DIR__ . '/../../../app/config.php';

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

/*
 * Default response
 */

/** @noinspection PhpUnusedParameterInspection */
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


/***********************************************************
 * Book API
 ***********************************************************/

/** @noinspection PhpUnusedParameterInspection */
$app->get('/book', function (Request $req, Response $res) use ($h)
{
	return $h->processBookings($res);
});

/** @noinspection PhpUnusedParameterInspection */
$app->get('/book/{facility}/{time}/{numSlots}', function (Request $req = null, Response $res, $args) use ($h)
{
	return $h->makeBooking($res, $args);
});


/***********************************************************
 * User API
 ***********************************************************/

/** @noinspection PhpUnusedParameterInspection */
$app->get('/user', function (Request $req, Response $res) use ($h)
{
	return $h->getUsers($res);
});

$app->post('/user', function (Request $req, Response $res) use ($h)
{
	return $h->addUser($req, $res);
});

/** @noinspection PhpUnusedParameterInspection */
$app->get('/user/{id}', function (Request $req, Response $res, $args) use ($h)
{
	return $h->getUser($res, $args);
});

$app->put('/user/{id}', function (Request $req, Response $res, $args) use ($h)
{
	return $h->modUser($req, $res, $args);
});

/** @noinspection PhpUnusedParameterInspection */
$app->delete('/user/{id}', function (Request $req, Response $res, $args) use ($h)
{
	return $h->delUser($res, $args);
});


/***********************************************************
 * Booking API
 ***********************************************************/

/** @noinspection PhpUnusedParameterInspection */
$app->get('/booking', function (Request $req, Response $res) use ($h)
{
	return $h->getBookings($res);
});

$app->post('/booking', function (Request $req, Response $res) use ($h)
{
	return $h->addBooking($req, $res);
});

/** @noinspection PhpUnusedParameterInspection */
$app->get('/booking/{id}', function (Request $req, Response $res, $args) use ($h)
{
	return $h->getBooking($res, $args);
});

$app->put('/booking/{id}', function (Request $req, Response $res, $args) use ($h)
{
	return $h->modBooking($req, $res, $args);
});

/** @noinspection PhpUnusedParameterInspection */
$app->delete('/booking/{id}', function (Request $req, Response $res, $args) use ($h)
{
	return $h->delBooking($res, $args);
});

$app->run();
