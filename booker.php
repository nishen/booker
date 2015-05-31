<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/05/2015
 * Time: 11:41 PM
 */

require_once(__DIR__ . '/httpful.phar');

use \Httpful\Httpful;
use \Httpful\Http;
use \Httpful\Mime;
use \Httpful\Proxy;
use \Httpful\Response;
use \Httpful\Request;

$siteUrl = 'https://secure.activecarrot.com/login?site=382';


$res = Request::get($siteUrl)->send();
$token = getToken($res->body);

if ($token == "")
{
    echo "failed to obtain token...";
    exit(1);
}

echo "$token\n";

#$content = file_get_contents("login-page.html");
#print getToken($content);

function getToken($content)
{
    $res = preg_match('/name=\"atoken\" +value=\"(.*)\"/', $content, $matches);
    if ($res > 0)
        return $matches[1];

    return "";
}
