<?php
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/06/2015
 * Time: 6:56 PM
 */

namespace nishen;

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TooManyRedirectsException;

class Booker
{
    private $username;
    private $password;

    private $client;
    private $jar;


    function __construct($username, $password)
    {
        $this->client = new Client([
            'base_uri' => 'https://secure.activecarrot.com/customer/mobile/',
            'timeout' => 125.0,
            'cookies' => true,
            'verify' => false,
            'proxy' => 'tcp://localhost:8888',
            'headers' => [
                'Origin' => 'secure.activecarrot.com',
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4',
                'Accept' => ':text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'no-cache'
            ]
        ]);

        $this->username = $username;
        $this->password = $password;
    }

    public function get()
    {
        $res = $this->client->get('login?site=382', [['cookies' => $this->jar]]);
        echo $res->getBody();
    }

    public function login()
    {
        $res = null;
        try
        {
            $res = $this->client->post('login', [
                'form_params' => [
                    'username' => $this->username,
                    'password' => $this->password,
                    'submit' => 'submit-value'
                ],
                'headers' => [
                    'Referer' => 'https://secure.activecarrot.com/customer/mobile/login?site=382',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'allow_redirects' => [ 'max' => 1]
            ]);

            print_r($res->getHeaders());
            echo $res->getBody();
        }
        catch (TooManyRedirectsException $e)
        {
            echo "additional redirect found: " . $e->getMessage();
        }
    }
}