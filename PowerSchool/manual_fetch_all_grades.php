<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
include('functions.php');
include_once('dbClass.php');
$type = 'student_storedgrades';
$url = 'https://mcpss.powerschool.com';
$clientID = 'c8c7d902-81dc-41fb-8f92-5b26bcf18b19';
$clientSecret = '82a62983-c22c-419a-b7b9-4b68b92c2883';

$objDB = new MySQLCN; 


$accessToken = getAccessToken($url, $clientID, $clientSecret);
$accessTokenArray = json_decode($accessToken);

if (!empty($accessTokenArray)) {
    $accessTokenKey = $accessTokenArray->access_token;
    $accessTokenType = $accessTokenArray->token_type;
    $accessTokenExpiresIn = $accessTokenArray->expires_in;
    
    if (isset($accessTokenKey) && !empty($accessTokenKey)) {
        $SQL = "SELECT id, student_id FROM submissions WHERE student_id != '' AND grade_exists = 'N'";//" LIMIT 1";
        $rsMain = $objDB->select($SQL);

        if(count($rsMain) > 0)
        {
            for($i=0; $i < count($rsMain); $i++)
            {

            $SQL = "SELECT dcid FROM student WHERE stateID = '".$rsMain[$i]['student_id']."'";
            $rsS = $objDB->select($SQL);
            $powerSchoolRecords = getPowerSchoolRecords($type, $accessTokenKey, $url, array("submission_id"=>$rsMain[$i]['id'], "student_id"=>$rsS[0]['dcid']));

            $SQL = "UPDATE submissions SET  grade_exists = 'Y' WHERE id = '".$rsMain[$i]['id']."'";
            $rs = $objDB->sql_query($SQL);
            }

        }
        //}
    }
} else {
    echo "Invalid Token";
}
