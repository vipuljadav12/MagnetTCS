<?php
//echo base64_decode("V3JvbmcgUXVlcnkgOiBJTlNFUlQgSU5UTyBwc19nZW5lcmFsIFNFVCB2YWx1ZTIgPSAiIixkY2lkID0gIjUyIix2YWx1ZXQgPSAiPEZPTlQgRkFDRT0iVmVyZGFuYSIgU0laRT0iMiIgU0laRVA9IjEwIiA+PEI+IixjYXQgPSAic3R5bGVzIixuYW1lID0gIlZlcmRhbmEgMTAgQm9sZCIsaWQgPSAiMCIsdmFsdWUgPSAiIjxicj5Zb3UgaGF2ZSBhbiBlcnJvciBpbiB5b3VyIFNRTCBzeW50YXg7IGNoZWNrIHRoZSBtYW51YWwgdGhhdCBjb3JyZXNwb25kcyB0byB5b3VyIE1hcmlhREIgc2VydmVyIHZlcnNpb24gZm9yIHRoZSByaWdodCBzeW50YXggdG8gdXNlIG5lYXIgJ1ZlcmRhbmEiIFNJWkU9IjIiIFNJWkVQPSIxMCIgPjxCPiIsY2F0ID0gInN0eWxlcyIsbmFtZSA9ICJWZXJkYW5hIDEwIEJvbGQiLGlkLi4uJyBhdCBsaW5lIDE=");exit;
set_time_limit(0);
ini_set('memory_limit', '-1');
include('functions.php');
include_once('dbClass.php');
$type = $_GET['type'];//students';
$url = 'https://tuscaloosacs.powerschool.com';
$clientID = 'c1b2222b-9a3a-4c7d-af92-1c26099c3844';
$clientSecret = 'a7e0fdd4-d89c-4536-bf19-b2d57c62af4b';
$accessTokenKey = "YzFiMjIyMmItOWEzYS00YzdkLWFmOTItMWMyNjA5OWMzODQ0OmE3ZTBmZGQ0LWQ4OWMtNDUzNi1iZjE5LWIyZDU3YzYyYWY0Yg==";

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
    }
    else {
    echo "Invalid Token";
    }
getAccessToken($url, $clientID, $clientSecret);
$accessTokenArray = json_decode($accessToken);



?>