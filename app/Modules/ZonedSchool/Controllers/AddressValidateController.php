<?php

namespace App\Modules\ZonedSchool\Controllers;

use Illuminate\Http\Request;
use App\Modules\ZonedSchool\Models\ZonedSchool;
use App\Modules\ZonedSchool\Models\ZonedAddressMaster;
use App\Modules\ZonedSchool\Export\ZoneAddressExport;
use App\Modules\ZonedSchool\Export\ZonedSchoolImport;
use App\Modules\ZonedSchool\Models\NoZonedSchool;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\AddressOverride\Models\AddressOverride;
use Validator;
use Session;
use DB;
use Auth;
// use Illuminate\Support\Facades\DB;

class AddressValidateController extends Controller
{

    private $end_points = [
        'schools' => "https://services1.arcgis.com/DADyRNMb7tdzKmmq/ArcGIS/rest/services/Tuscaloosa_City_School_Zones_2019_2020/FeatureServer/2/query?"
            ."&geometryType=esriGeometryPoint"
            ."&spatialRel=esriSpatialRelIntersects"
            ."&outFields=elem%2Cmiddle%2Chigh%2CES_choice%2CMS_choice%2CHS_choice"
            ."&returnTrueCurves=false"
            ."&returnIdsOnly=false"
            ."&returnCountOnly=false"
            ."&returnZ=false"
            ."&returnM=false"
            ."&returnDistinctValues=false"
            ."&returnExtentsOnly=false"
            ."&returnGeometry=false"
            ."&f=json"
            ."&geometry=",
        'possible_addresses' => "https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?"
            ."Street=&ZIP=&category=&outFields=&maxLocations=&outSR=&searchExtent=&location=&distance=&magicKey=&f=pjson"
            ."&SingleLine=",
        'address_point'=>
            "https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?"
            ."Street=&ZIP=&category=&outFields=&maxLocations=&outSR=&searchExtent=&location=&distance=&magicKey=&f=pjson"
            ."&SingleLine="
    ];

    public function __construct(){
        // session()->put('district_id', 1);  //26-6-20
    }

    public function prepareAddress( $address ){
        //HSV City System only used Unit, it changes Apt and Suite over to Unit.
        //We need to do the same. PREG_REPLACE Replaces either words with Unit.
        $address = trim( $address );
        $address = preg_replace( '/(\bSuite\b)|(\bLot\b)|(\bApt\b)/i' , '' , $address );
        $address = preg_replace( "/(\.)|(,)|(')|(#)/" , '' , $address );
        $address = preg_replace( '/(\bDrive\b)/i' , 'DR' , $address );
        $address = preg_replace( '/(\bCr\b)/i' , 'CIR' , $address );
        //$address = preg_replace( '/(\bmc)/i' , 'Mc ' , $address );
        $address = preg_replace( '/(\bBlvd\b)/i' , 'BLV' , $address );
        $address = preg_replace( '/(\bAvenue\b)/i' , 'AVE' , $address );
        $addressArray = explode( ' ' , $address );

        //Does the index:1 contain an number street. Example: 8th Street.
        if( isset( $addressArray[1] ) && preg_match( '/\d+/' , $addressArray[1] , $matches ) !== false ) {
            //Index:1 contains an number. Need to replace.
            //Add in switch statement to handle converting 1st - 17th to First - Seventeenth
            switch( strtoupper( $addressArray [1] ) ) {
                case '1ST':
                    $addressArray[1] = 'FIRST';
                    break;
                case '2ND':
                    $addressArray[1] = 'SECOND';
                    break;
                case '3RD':
                    $addressArray[1] = 'THIRD';
                    break;
                case '4TH':
                    $addressArray[1] = 'FOURTH';
                    break;
                case '5TH':
                    $addressArray[1] = 'FIFTH';
                    break;
                case '6TH':
                    $addressArray[1] = 'SIXTH';
                    break;
                case '7TH':
                    $addressArray[1] = 'SEVENTH';
                    break;
                case '8TH':
                    $addressArray[1] = 'EIGHTH';
                    break;
                case '9TH':
                    $addressArray[1] = 'NINTH';
                    break;
                case '10TH':
                    $addressArray[1] = 'TENTH';
                    break;
                case '11TH':
                    $addressArray[1] = 'ELEVENTH';
                    break;
                case '12TH':
                    $addressArray[1] = 'TWELFTH';
                    break;
                case '13TH':
                    $addressArray[1] = 'THIRTEENTH';
                    break;
                case '14TH':
                    $addressArray[1] = 'FOURTEENTH';
                    break;
                case '15TH':
                    $addressArray[1] = 'FIFTEENTH';
                    break;
                case '17TH':
                    $addressArray[1] = 'SEVENTEENTH';
                    break;
                default:
                    break;
            }
        }
        //echo implode( ' ' , $addressArray );exit;
        return implode( ' ' , $addressArray );
    }

    public function getSuggestion(Request $request)
    {
        //return "NoMatch";
        $val = $request->address;
        $zip_code = $request->zip;

        $addressParts = explode( ' ', trim( $val ) );
        $countParts = count($addressParts);

        $value = trim( preg_replace('/\s+/', ' ', strtoupper( $val ) ) );

        $results = null;
        for( $useParts = $countParts; $useParts > 0; $useParts-- ){
            $searchAddress = implode( ' ', array_slice( $addressParts, 0, $useParts ) );

            $suggestions = $this->getAddressCandidates( $searchAddress , $zip_code, 5 );

            if($suggestions) {
                if(count($suggestions) == 1)
                {
                    return $suggestions[0];
                }
                else
                {
                    foreach( $suggestions as $suggestion ) {
                        if ( trim( preg_replace('/\s+/', ' ', $suggestion) ) == $value ) {
                            return $request->address;
                        }
                    }
                    $str = "<select onchange='selectAddress(this.value)' class='form-control' id='addoptions'>";
                    $str .= "<option value=''>Select any address</option>";
                    foreach($suggestions as $value)
                    {
                        $str .= "<option value='".$value."'>".$value."</option>";
                    }
                    $str .= "</select>";
                    return $str;
                }
            }
        }
        return "NoMatch";

        /*
        $address = str_ireplace('Huntsville, AL', '', $val);
        $address = str_ireplace('Northport, AL', '', $address);
        $address = str_ireplace('Cottondale, AL', '', $address);
        $address_basic = trim($address);
        $abbrevs = [
            '/ GDNS\b/i' => ' Gardens',
            '/ HTS\b/i' => ' heights',
            '/ SQ\b/i' => ' square',
            '/ VLG\b/i' => ' village',
            '/ GRV\b/i' => ' grove',
            '/ RDG\b/i' => ' ridge',
            '/ HWY\b/i' => ' hw',
            '/ HLS\b/i' => ' hills',
            '/ TER\b/i' => ' terrace'
        ];

        foreach($abbrevs as $k => $v)
        {
            $address_basic = preg_replace($k, $v, $address_basic);
        }

        $address_basic = strtoupper($address_basic);
        
        $pattern = '/ apt [a-z0-9 ]+/i';
        $pattern2 = '/ lot [a-z0-9 ]+/i';
        $address_basic = preg_replace($pattern, '', $address_basic);
        $address_basic = preg_replace($pattern2, '', $address_basic);
        $address_basic = str_replace(' ', '+', $address_basic);
        $address_basic = preg_replace('/\s+/', ' ', $address_basic);
        $address_basic = preg_replace( '/^(\d+)[a-zA-Z]/', '$1', $address_basic );

        $fake_address = $address_basic.' | '.$zip_code;

        if ($address_basic)
        {
            $url =  $this->end_points['possible_addresses']."?Street=&category=&outFields=*&maxLocations=5&outSR=&searchExtent=&location=&distance=&magicKey=&f=json&SingleLine=".$address_basic;

            $content = file_get_contents($url);
            $json = json_decode($content, true);
            if( count( $json['candidates'] ) == 0 ){

                $address_basic = preg_replace('/\+\w+$/', '', $address_basic);

                $url = $this->end_points['possible_addresses']."?Street=&category=&outFields=*&maxLocations=5&outSR=&searchExtent=&location=&distance=&magicKey=&f=json&SingleLine="
                            .$address_basic;
            echo "<pre>";
            print_r($json);exit;

                $content = file_get_contents($url);
                $json2 = json_decode($content, true);
                $json = $json2;
           }
            if( count( $json['candidates'] ) == 1 ){

                return $json['candidates'][0]['address'];
            } 
            else if( count( $json['candidates'] ) > 1)
            {
                $data = $json['candidates'];
                $address = [];
                $final_address = "";
                foreach($data as $key=>$value)
                {
                    if($value['score'] == 100)
                    {
                        $final_address = $value['address'];
                    }
                    if(!in_array($value['address'], $address))
                    {
                        $address[] = $value['address'];
                    }
                }
                $str = "<select onchange='selectAddress(this.value)' class='form-control' id='addoptions'>";
                $str .= "<option value=''>Select any address</option>";
                foreach($address as $value)
                {
                    $str .= "<option value='".$value."'>".$value."</option>";
                }
                $str .= "</select>";
                return $str;
            }       
        }
        return "NoMatch";*/

    }

        /**
     * Retrieve response from API
     *
     * @param $end_point
     * @return array|mixed
     */
    public function getResponse( $end_point ){

        $curl = curl_init($end_point);

        curl_setopt( $curl , CURLOPT_URL , $end_point );
        curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
        curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true );
        curl_setopt( $curl , CURLOPT_HEADER , false );
        curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0) Gecko/20100101 Firefox/13.0.1');    // mPDF 5.7.4
        $data = curl_exec($curl);

        curl_close($curl);

        if (!$data) {
            return [];
        }
        $decoded_data = json_decode($data);

        if (json_last_error() != JSON_ERROR_NONE) {

            writeln('JSON error: ' . json_last_error());
            return [];
        }

        return $decoded_data;
    }

    public function getAddressCandidates($address, $zip = null, $maxAddresses = null)
    {
        
         // Get possible addresses from API
        $response = $this->getResponse( $this->end_points['possible_addresses'] . urlencode( $this->prepareAddress( $address." ".$zip ) ) );
       
        if( !isset($response->candidates) ){
            return false;
        }

        $possible_addresses = [];
        $scoredList = [];

        //Build list of addresses with scores
        foreach( $response->candidates as $candidate ){
            $scoredList[] = [
                'score' => $candidate->score,
                'addressBound' => $candidate->address
            ];
        }
        //Sort scored list by score descending
        usort($scoredList, function($a, $b) {
            if ($a['score'] == $b['score']) {
                return 0;
            }
            return ($a['score'] > $b['score']) ? -1 : 1;
        });

        //Remove duplicate addresses
        $final_match = false;
        foreach( $scoredList as $index => $scoredAddress ){
            /*if($scoredAddress['score'] == 100 && !$final_match)
            {
                $final_match = true;
                if( !in_array( $scoredAddress['addressBound'], $possible_addresses ) ) {
                    $possible_addresses[] = $scoredAddress['addressBound'];
                }
            }

            if(!$final_match)
            {*/
                if( !in_array( $scoredAddress['addressBound'], $possible_addresses ) ) {
                    $possible_addresses[] = $scoredAddress['addressBound'];
                } else {
                    unset( $scoredList[$index] );
                }
            //}
        }

       
        $returnAddresses = [];
        foreach( $scoredList as $address){
            $returnAddresses[] = $address['addressBound'];
        }

        return $returnAddresses;
    }


    public function getSuggestionCurrent($form_id)
    {
        if(Session::has("form_data"))
        {
            $dataArray =  Session::get("form_data")[0];
        }
        $formdata = $dataArray['formdata'];

        $address_id = fetch_student_field_id($form_id, "address");
        $zip_id = fetch_student_field_id($form_id, "zip");
        $city_id = fetch_student_field_id($form_id, "city");
        $next_grade_id = fetch_student_field_id($form_id, "next_grade");
        $zoned_field_id = fetch_student_field_id($form_id, "zoned_school");
        $get_mcp_id = fetch_student_field_id($form_id, "mcp_employee");
        $state_id = fetch_student_field_id($form_id, "student_id");

        $rs = AddressOverride::where('state_id', $formdata[$state_id])->first();
        if(!empty($rs) && $rs->zoned_school != '')
        {
            $zoned_field_id = fetch_student_field_id($form_id, "zoned_school");
            //$zoned_school = "Other";
            $formdata[$zoned_field_id] = $rs->zoned_school;
            Session::forget("form_data");
            $dataArray['formdata'] = $formdata;
            Session::push("form_data", $dataArray);
            return $rs->zoned_school;
        }

        $val = $address = Session::get("form_data")[0]["formdata"][$address_id];

        $zip_code = Session::get("form_data")[0]["formdata"][$zip_id];
        $next_grade = Session::get("form_data")[0]["formdata"][$next_grade_id];
        $city = Session::get("form_data")[0]["formdata"][$city_id];

        $addressParts = explode( ' ', trim( $val ) );
        $countParts = count($addressParts);

        $value = trim( preg_replace('/\s+/', ' ', strtoupper( $val ) ) );

        $results = null;
        $final_address = "";
        for( $useParts = $countParts; $useParts > 0; $useParts-- ){
            $searchAddress = implode( ' ', array_slice( $addressParts, 0, $useParts ) );

            $suggestions = $this->getAddressCandidates( $searchAddress , $zip_code, 5 );
            if($suggestions) {
                if(count($suggestions) == 1)
                {
                    $final_address = $suggestions[0];
                }
                else
                {
                    foreach( $suggestions as $suggestion ) {
                        $tmp = str_replace("&", "AND", $suggestion);

                        if ( trim( preg_replace('/\s+/', ' ', $suggestion) ) == $value || trim( preg_replace('/\s+/', ' ', $tmp) ) == $value ) {
                            $final_address = $address;
                        }
                    }
                    if($final_address == "")
                        $final_address = $suggestions[0];

                }
                if($final_address == "")
                {
                    $insert = array();
                    $insert['street_address'] = $address;
                    $insert['city'] = $city;
                    $insert['zip'] = $zip_code;
                    $nz = NoZonedSchool::create($insert);
                    Session::forget("step_session");
                    return "NoMatch";
                }
                $zoned_school = $this->getZonedSchool($final_address, $next_grade);

                if($zoned_school != '')
                {
                    $formdata[$zoned_field_id] = $zoned_school;
                    Session::forget("form_data");
                    $dataArray['formdata'] = $formdata;
                    Session::push("form_data", $dataArray);
                    return $zoned_school;
                }
                else
                {
                    if (isset($formdata[$get_mcp_id]) && $formdata[$get_mcp_id] == "Yes") {
                        $zoned_school = "Other";
                        $formdata[$zoned_field_id] = $zoned_school;
                        Session::forget("form_data");
                        $dataArray['formdata'] = $formdata;
                        Session::push("form_data", $dataArray);
                        return $zoned_school;
                    }
                    else
                    {
                        $insert = array();
                        $insert['street_address'] = $address;
                        $insert['city'] = $city;
                        $insert['zip'] = $zip_code;
                        $nz = NoZonedSchool::create($insert);
                        Session::forget("step_session");
                        //$nz = NoZonedSchool::create($insert);
                    }
                }
                return "NoMatch";
                
            }
            else
            {
                return "NoMatch";
            }
        }
        return "NoMatch";
    }
    
    public function getZonedSchool($address1, $nextGrade)
	{
        $zip = 35401;
        $address = strtoupper( $address1 );

        //$address = explode( ' APT ', $address)[0];
        //$address = explode( ' UNIT ', $address)[0];
        //$address = explode( ' LOT ', $address)[0];
        $address = trim( $address );
        $address = preg_replace( '/(\bAVE\b$)/i', 'AV' , $address );

        //$address = $address .', 35401';
        $end_point = $this->end_points['address_point'] . urlencode( $address1 );
        $response = $this->getResponse( $end_point );
            //dd($response);
       
        if( count( $response->candidates ) == 0 ){
            $address = $this->prepareAddress( $address );

            $end_point = $this->end_points['address_point'] . urlencode( $address );
            $response = $this->getResponse( $end_point );
        }
        
        if( count( $response->candidates ) == 0 ){
            return "";
        }

        $matching_index = 0;
        $multiple_matches = false;
        if( count( $response->candidates ) > 1 ){
            //echo "<pre>";
            //print_r($response);
            //$address = preg_replace( '/(\bSuite\b)|(\bLot\b)|(\bApt\b)/i' , '' , $address );
            $address = trim($address,",");
            $dataArray =  Session::get("form_data")[0];

            $form_id = Session::get("form_id");
        
            $formdata = $dataArray['formdata'];
            

            $zip_id = fetch_student_field_id($form_id, "zip");
            $city_id = fetch_student_field_id($form_id, "city");
            $state = fetch_student_field_id($form_id, "state");  
            $tmp_zip = explode("-",$formdata[$zip_id]);
            $zip = $tmp_zip[0];
            $city = $formdata[$city_id];
            $state = $formdata[$state];
            //$address .= ", ".$city.", Alabama, ".$zip;


            $matching_index = -1;
            $basic_addresses = [];
            foreach( $response->candidates as $index => $feature ){
                //echo "<br>".$address . " - ".$feature->address."<BR>";
                if( strtoupper( $address ) == strtoupper( $feature->address )){
                    $multiple_matches = ( $matching_index > -1 );
                    if( $matching_index < 0 ){
                        $matching_index = $index;
                    }
                    $basic_addresses[] = $feature->address;
                }
            }
            
        }
            //exit;
        if( $multiple_matches ){
            if( count( array_unique( $basic_addresses ) ) > 1 ){
                return "";
            }
        }

        if( $matching_index == -1 ){
            return "";
        }

        $geometry = '{"x" : '. $response->candidates[$matching_index]->location->x
            .', "y" : '.$response->candidates[$matching_index]->location->y
            .', "spatialReference" : {"wkid" : '.$response->spatialReference->wkid.'}}';

        $end_point = $this->end_points['schools']. urlencode( $geometry );

        $response = $this->getResponse( $end_point );

        if( empty( $response->features ) ){
            return "";
        }


        if(
            isset( $response->features[$matching_index]->attributes->elem )
            && isset( $response->features[$matching_index]->attributes->middle )
            && isset( $response->features[$matching_index]->attributes->high )
            && (
                strtoupper( $response->features[$matching_index]->attributes->elem ) == 'N/A'
                || strtoupper( $response->features[$matching_index]->attributes->middle ) == 'N/A'
                || strtoupper( $response->features[$matching_index]->attributes->high ) == 'N/A'
            )
        ){
            return "";
        }


		$elem = [1, 2, 3, 4, 5];
		$mid = [6,7,8];
		$high = [9,10,11,12];
		$nextSchool = "";

		if (in_array($nextGrade, $elem) || $nextGrade == "PreK" || $nextGrade == "K")
		{
			$nextSchool = $response->features[$matching_index]->attributes->elem;
		} else if (in_array($nextGrade, $mid)) {
			$nextSchool = $response->features[$matching_index]->attributes->middle;
		} else if (in_array($nextGrade, $high)) {
			$nextSchool = $response->features[$matching_index]->attributes->high;
		}
        if(str_replace("/", "", strtolower($nextSchool)) == "na")
        {
            Session::forget("application_id");
            Session::forget("step_session");
            Session::forget("form_data");
            Session::forget("page_id");
            Session::forget("mcpss_zone_api");
            Session::forget("zone_api");
            Session::forget('gifted_student');
            return "";

        }
		return $nextSchool;
	}

    public function getZonedSchoolAdmin(Request $request)
    {
        $address = $request->address;
        $next_grade = $request->next_grade;
        $zip = $request->zip;
        $address = $address. ", ".$zip;
        $zoned_school = $this->getZonedSchool($address, $next_grade);
        if($zoned_school != "")
            echo $zoned_school;
        else
            echo "";

    }


    public function checkAddress()
    {
        return view('ZonedSchool::check_address');
    }
}
