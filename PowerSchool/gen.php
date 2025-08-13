<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
include('functions.php');
include_once('dbClass.php');
$type = 'gen';
$url = 'https://tuscaloosacs.powerschool.com';

$clientID = "5fbcc22b-48fc-4d79-a943-f44a78c7a443";
$clientSecret = "81a7f449-3b6f-43de-aa5b-1b4eca3fb03f";

$objDB = new MySQLCN; 


$accessToken = getAccessToken($url, $clientID, $clientSecret);
$accessTokenArray = json_decode($accessToken);

if (!empty($accessTokenArray)) {
    $accessTokenKey = $accessTokenArray->access_token;
    $accessTokenType = $accessTokenArray->token_type;
    $accessTokenExpiresIn = $accessTokenArray->expires_in;
    
    if (isset($accessTokenKey) && !empty($accessTokenKey)) {
       
            $powerSchoolRecords = getPowerSchoolRecords($type, $accessTokenKey, $url, array());

        }
        //}
    
} else {
    echo "Invalid Token";
}
