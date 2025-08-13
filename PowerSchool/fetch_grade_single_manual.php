<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
include('functions.php');
include_once('dbClass.php');
$type = 'student_storedgrades';
$url = 'https://tuscaloosacs.powerschool.com';

//$clientID = '105a3a3d-78ea-4b7e-b511-868a94370f72';//'105a3a3d-78ea-4b7e-b511-868a94370f72';
//$clientSecret = '8dae6cac-370b-41e7-84d1-982cc9d90602';//'8dae6cac-370b-41e7-84d1-982cc9d90602';
$clientID = "5fbcc22b-48fc-4d79-a943-f44a78c7a443";
$clientSecret = "81a7f449-3b6f-43de-aa5b-1b4eca3fb03f";

$objDB = new MySQLCN; 


$accessToken = getAccessToken($url, $clientID, $clientSecret);
$accessTokenArray = json_decode($accessToken);
/*
echo base64_decode("V3JvbmcgUXVlcnkgOiBJTlNFUlQgSU5UTyBzdWJtaXNzaW9uX2dyYWRlIFNFVCBzdWJtaXNzaW9uX2lkID0gIjEwMDQiLHN0YXRlSUQgPSAiMTQ2MDAiLGFjYWRlbWljWWVhciA9ICIyMDIxLTIyIixhY2FkZW1pY1Rlcm0gPSAiUTEgR3JhZGUiLEdyYWRlTmFtZSA9ICJRMSBHcmFkZSIsY291cnNlVHlwZUlEID0gIjMiLGNvdXJzZVR5cGUgPSAiRW5nbGlzaCIsY291cnNlTmFtZSA9ICJFbmdsaXNoIExhbmd1YWdlIEFydHMsIEdyYWRlIDYiLGNvdXJzZUZ1bGxOYW1lID0gIkVuZ2xpc2ggTGFuZ3VhZ2UgQXJ0cywgR3JhZGUgNiIsYWN0dWFsX251bWVyaWNfZ3JhZGUgPSAiNzMiLG51bWVyaWNHcmFkZSA9ICI3MyIsZ3JhZGUgPSAiQyIsdGVhY2hlcl9uYW1lID0gIkJyeWFudCwgTW9uZWVrIFIiLHRlYWNoZXJfZW1haWwgPSAibWJyeWFudEB0dXNjLmsxMi5hbC51cyI8YnI+VW5rbm93biBjb2x1bW4gJ2dyYWRlJyBpbiAnZmllbGQgbGlzdCc=");exit;*/

if (!empty($accessTokenArray)) {
    $accessTokenKey = $accessTokenArray->access_token;
    $accessTokenType = $accessTokenArray->token_type;
    $accessTokenExpiresIn = $accessTokenArray->expires_in;
    
    if (isset($accessTokenKey) && !empty($accessTokenKey)) {
        $SQL = "SELECT id, student_id FROM submissions WHERE id = '".$_GET['id']."'";
            $rs = $objDB->select($SQL);
           // print_r($rs);exit;
            for($i=0; $i < count($rs); $i++)
            {
                $SQL = "SELECT dcid, stateID FROM student WHERE stateID = '".$rs[$i]['student_id']."'";
                $rsS = $objDB->select($SQL);

                //print_r($rsS);
                if(count($rsS) > 0)
                {
                    $powerSchoolRecords = getPowerSchoolRecords($type, $accessTokenKey, $url, array("submission_id"=>$rs[$i]['id'], "student_id"=>$rsS[0]['dcid']));
                }
            }
            
            
    }
} else {
    echo "Invalid Token";
}
