<?php namespace nishen;
/**
 * Created by PhpStorm.
 * User: nishen
 * Date: 27/06/2015
 * Time: 6:56 PM
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DateTime;
use GuzzleHttp\Client;

class Booker
{
    private $username;

    private $password;

    private $client;

    function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->client = new Client([
            'base_uri' => 'https://secure.activecarrot.com/customer/mobile/',
            'timeout' => 120.0,
            'cookies' => true,
            'verify' => false,
            'proxy' => 'tcp://localhost:8888',
            'headers' => [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'en-AU,en;q=0.8,en-GB;q=0.6,en-US;q=0.4',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'Pragma' => 'no-cache',
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4'
            ]
        ]);
    }

    public function viewLogin()
    {
        $res = $this->client->get('login?site=382');
        echo $res->getBody();
    }

    public function login()
    {
        $res = $this->client->post('login', [
            'form_params' => [
                'username' => $this->username,
                'password' => $this->password,
                'submit' => 'submit-value'
            ],
            'headers' => [
                'Referer' => 'https://secure.activecarrot.com/customer/mobile/login?site=382',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Origin' => 'https://secure.activecarrot.com'
            ],
            'allow_redirects' => false
        ]);

        print_r($res->getHeaders());

        echo $res->getBody();

        switch ($res->getStatusCode())
        {
            case 301:
            case 302:
            case 303:
            case 307:
            case 308:
                return $res->getHeader("Location");
                break;
        }

        return null;
    }

    public function dashboard()
    {
        $res = $this->client->post('dashboard', [
            'headers' => [
                'Accept-Encoding' => 'gzip, deflate, sdch',
                'Referer' => 'https://secure.activecarrot.com/customer/mobile/login?site=382'
            ],
            'allow_redirects' => false
        ]);

        echo $res->getBody();
    }

    public function getFacilityAvailability($resource = '753', $date = null)
    {
        if ($date == null)
        {
            $date = new DateTime();
            $date->modify('+2 days');
            echo $date->format('Y-m-d');
        }

        $endpoint = 'facility/browse/' . $resource . '/' . $date->format('Y-m-d');

        $res = $this->client->post($endpoint, [
            'headers' => [
                'Accept' => 'text/html, */*; q=0.01',
                'Accept-Encoding' => 'gzip, deflate, sdch',
                'Referer' => 'https://secure.activecarrot.com/customer/mobile/login?site=382',
                'X-Requested-With' => 'XMLHttpRequest'
            ],
            'allow_redirects' => false
        ]);

        preg_match_all('|<a href="/customer/mobile.facility/book_dialog/(\d+)/(\d+)" data-rel="dialog">(.{7})</a>|', $res->getBody(), $out, PREG_SET_ORDER);

        return $out;
    }
}