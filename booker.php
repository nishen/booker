<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/05/2015
 * Time: 11:41 PM
 */


$content = file_get_contents("login-page.html");
print getToken($content);


function getToken($content)
{
    $res = preg_match('/name=\"token\" +value=\"(.*)\"/', $content, $matches);
    if ($res > 0)
        return $matches[1];

    return "";
}