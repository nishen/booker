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

/*
$user = new Model\User;
$user->setUsername('nishen');
$user->setPassword('nishen');
$user->setName('Nishen Naidoo');
$user->setEmail('nish.naidoo@gmail.com');
$user->setCreated(new DateTime());
$user->save();
*/

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;


$app = new App();
$app->get('/user/{id}', function (Request $request, Response $response, $args)
{
	$u = new \Model\UserQuery();
	$user = $u->findPk($args['id']);
	$response->write($user->toJSON());
});

$app->run();
