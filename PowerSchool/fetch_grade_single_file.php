<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
include('functions.php');
include_once('dbClass.php');
$type = 'student_storedgrades';
$url = 'https://tuscaloosacs.powerschool.com';

$clientID = '105a3a3d-78ea-4b7e-b511-868a94370f72';
$clientSecret = '8dae6cac-370b-41e7-84d1-982cc9d90602';

$objDB = new MySQLCN; 


$accessToken = getAccessToken($url, $clientID, $clientSecret);
$accessTokenArray = json_decode($accessToken);

if (!empty($accessTokenArray)) {
    $accessTokenKey = $accessTokenArray->access_token;
    $accessTokenType = $accessTokenArray->token_type;
    $accessTokenExpiresIn = $accessTokenArray->expires_in;

    echo 'course_number^credit_type^grade_level^studentid^course_name^academicYear^numericGrade^teacher_name^gpa_points^gradescale_name^grade^sectionid^schoolname^storecode<br>';

    
    if (isset($accessTokenKey) && !empty($accessTokenKey)) {

        $rs = array(11,32203,31041,32448,31500,31505,32591,32691,30093,30099,29543,30295,31342,12,13,31,32,35,20,59,60,70,85,30444);
        $rs = array(1745);

        for($i=0; $i < count($rs); $i++)
        {
            $SQL = "select grade from tmp where submission_id = '".$rs[$i]."'";
            $rs1 = $objDB->select($SQL);
            $student_id = $rs1[0]['grade'];
            //echo $student_id;exit;
           $powerSchoolRecords = getPowerSchoolRecords($type, $accessTokenKey, $url, array("submission_id"=>$rs[$i], "student_id"=>$student_id));
        }
    }
} else {
    echo "Invalid Token";
}
