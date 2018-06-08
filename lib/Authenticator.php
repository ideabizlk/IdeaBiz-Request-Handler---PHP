<?php

/**
 * Created by PhpStorm.
 * User: Malinda
 * Date: 3/23/2015
 * Time: 10:45 PM
 */
include_once dirname(__FILE__) . '/Exceptions.php';
include_once dirname(__FILE__) . '/ResponseStatus.php';
include_once dirname(__FILE__) . '/RequestMethod.php';

class Authenticator
{
    var $data;
    var $config;
    var $basic;

    function renewToken()
    {

        if($this->getAccessToken()==null){
            $this->createNewtoken();
            return;
        }
        $url = $this->config->auth_url . "token?grant_type=refresh_token&refresh_token=" . $this->config->auth_refreshToken . "&scope=" . $this->config->auth_scope;

        $r = getHTTP($url, null, RequestMethod::POST, null,
            array("Content-Type: application/x-www-form-urlencoded",
                "Accept: application/json", "Authorization: Basic " . $this->basic), null, true);

        if ($r['status'] != ResponseStatus::OK)
            throw new  ConnectionException($r['msg']);

        if ($r['statusCode'] == 400 && strpos($r['body'], "invalid_grant") !== false) {
            $this->createNewtoken();
            return;
        } else if ($r['statusCode'] != 200)
            throw new  AuthenticationException("Wrong Access Token");

        $body = json_decode($r['body']);

        $this->data->expire = $body->expires_in;
        $this->data->accessToken = $body->access_token;
        $this->config->auth_refreshToken = $body->refresh_token;


        file_put_contents(dirname(__FILE__) . "/data.json", json_encode($this->data, JSON_PRETTY_PRINT));
        file_put_contents(dirname(__FILE__) . "/../config.json", json_encode($this->config, JSON_PRETTY_PRINT));

    }

    function __construct()
    {
        include dirname(__FILE__) ."/curl.php";

        $this->data = json_decode(file_get_contents(dirname(__FILE__) . "/data.json"));
        $this->config = json_decode(file_get_contents(dirname(__FILE__) . "/../config.json"));

        $this->basic = base64_encode($this->config->auth_consumerKey . ":" . $this->config->auth_consumerSecret);

    }

    function getAccessToken()
    {
        if(!isset($this->data) || !isset($this->data->accessToken)){
            return null;
        }
        return $this->data->accessToken;
    }

    function createNewtoken()
    {
        if (isset($this->config->auth_username) == false || $this->config->auth_username == null || strlen($this->config->auth_username) <= 1 || $this->config->auth_pw == false || $this->config->auth_pw == null || strlen($this->config->auth_pw) <= 1) {
            throw new  AuthenticationException("Wrong Access Token. Please recreate one");
        }
        $url = $this->config->auth_url . "token?grant_type=password&username=" . urlencode($this->config->auth_username) . "&password=" . urlencode($this->config->auth_pw) . "&scope=" . $this->config->auth_scope;

        $r = getHTTP($url, null, RequestMethod::POST, null,
            array("Content-Type: application/x-www-form-urlencoded",
                "Accept: application/json", "Authorization: Basic " . $this->basic), null, true);

        if ($r['statusCode'] != 200)
            throw new  AuthenticationException("Failed to create access token");
        $body = json_decode($r['body']);

        //echo $r['body'];
        if($this->data == null )
            $this->data = new stdClass();

        $this->data->expire = $body->expires_in;
        $this->data->accessToken = $body->access_token;
        $this->config->auth_refreshToken = $body->refresh_token;


        file_put_contents(dirname(__FILE__) . "/data.json", json_encode($this->data, JSON_PRETTY_PRINT));
        file_put_contents(dirname(__FILE__) . "/../config.json", json_encode($this->config, JSON_PRETTY_PRINT));

    }

}