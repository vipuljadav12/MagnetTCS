<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="" rel="stylesheet">
    <style type="text/css">

        /*page size start*/
        body {
            font-size: 12px;
        }
        td, th {
          padding: 5px !important;
          padding-left: 6px !important;
        }
        table {
          margin-bottom: 5px !important;
          margin-top: 0px !important;
        }
        .tbl_top_mrgn {
            margin-top: 15px !important;
        }
        /*page size end*/

        @font-face {
            font-family: 'Open Sans';
            src: url('fonts/OpenSans-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
       footer {
            bottom: 55px; 
            left: 0px; 
            right: 0px;
        }
        .header {
            top: 0px;
        }
        .footer {
            bottom: 0px;
        }
        body {padding: 10px; margin: 0; font-family: 'Open Sans', sans-serif;}
        .container {max-width: 700px; margin: 0 auto;}
        img {max-width:100%;}
        .w-50 {width:50%;}
        .w-100 {width:100%;}
        .logo-box {width:120px; margin-bottom: 20px;}
        .logo-box.text-right {margin-left: auto;}
        .text-center {text-align:center;}
        .table {width:100%; border: 1px solid black; border-collapse: collapse;}
        .table tr {padding: 0; margin: 0; border-bottom: 1px solid black;}
        .table tr th, .table tr td {padding: 10px 5px;margin: 0; border-top: 1px solid black; border-right: 1px solid black;}
        .section {margin-bottom: 0px !important;}
        .section-title {margin-top: 5px; margin-bottom: -10px !important; font-size: 12px; margin-left: -6px;}
        .section-title1 {margin-bottom: 0px !important; font-weight: 600; font-family: 'Open Sans', sans-serif;}
        .text-right {text-align: right;}
        .text-center {text-align: center;}
        .font-12{font-size: :12px;}

        .page_break {
           page-break-after: always;
        }
        .page_break:last-child {
           page-break-after: unset;
        }
    </style>
</head>
<body>
    @php
        if(isset($sp_datasheet) && !empty($sp_datasheet))
        {
            $loop_data = $sp_datasheet;
            $extra_container_class = 'page_break';
        } else {
            $loop_data[] = $data;
            $extra_container_class = '';
        }
    @endphp
    @foreach($loop_data as $data)
        <div class="{{$extra_container_class}}">
            <div class="header"></div>
            <div class="container {{-- page --}}" style="height: 100%;">
                <div class="header1">
                    <div align="center" style="margin-bottom: 10px; text-align: center;"> 
                       <table align="center" style="text-align: center;">
              <tbody>
                  <tr>
                      <td align="center" style="text-align: center">
                         @php $logo = (isset($application_data) ? getDistrictLogo($application_data->display_logo) : getDistrictLogo()) @endphp
                        <img src="{{str_replace('https://', 'http://', $logo)}}" title="" alt="" style="max-width: 300px !important;"></td>
                      
                  </tr>
              </tbody>
          </table>
                    
                    </div>
                    <div style="margin-bottom: 5px; margin-top: 50px;">
                        {{date('m/d/Y')}}
                    </div>
                   
                    <table class="w-100">
                        <tbody>
                            <tr>
                                <th class="w-100 text-center" colspan="3" style="font-size: 15px;">Student Profile Page</th>
                            </tr>
                            <tr>
                                <th class="w-100 text-center" colspan="3" style="font-size: 15px;">{{$data['submission']->current_grade.' to '.$data['submission']->next_grade}}</th>
                            </tr>
                            {{-- <tr><th colspan="3">&nbsp;</th></tr> --}}
                            <tr>
                                <td align="left">Student Name: {{$data['submission']->first_name.' '.$data['submission']->last_name}}</td>
                                <td align="center">
                                    @if($data['submission']->student_id != '')
                                        Student ID: {{$data['submission']->student_id}}
                                    @endif
                                </td>
                                <td align="right">Submission ID: {{$data['submission']->id}}</td>
                            </tr>
                            {{-- <tr><th colspan="3">&nbsp;</th></tr> --}}
                        </tbody>
                    </table>
                </div>
                <div class="wrapper">
                    <!-- Recommendation Data -->
                   
                    @if ($data['profile']['recommendation']['status'] == 'Y')
                        <div class="section section-1 page">
                            <div class="section-title">
                                <table class="" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td><b>Learner Profile Screening Device (LPSD) Criteria</b></td>
                                            <td align="right">{{$data['profile']['recommendation']['scored'].'/'.$data['profile']['recommendation']['total']}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if(count($data['profile']['recommendation']['data']) > 0)
                                <table class="table" style="white-space: nowrap;">
                                    <tr>
                                        @forelse($data['profile']['recommendation']['data'] as $question => $score)
                                            @php
                                                $field_txt = '';
                                                foreach ($data['profile']['recommendation']['lpsd_fields'] as $field => $find_key) {
                                                    if (strpos($question, $find_key) !== false) {
                                                        $field_txt = $field;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <td>{{$field_txt}}</td>
                                        @empty
                                        @endforelse
                                    </tr>
                                    <tr>
                                        @forelse($data['profile']['recommendation']['data'] as $question => $score)
                                            <td>{{$score}}</td>
                                        @empty
                                        @endforelse
                                    </tr>
                                </table>
                                {{-- <br>  --}}
                                <table class="table tbl_top_mrgn" style="white-space: nowrap;">
                                    <tr>
                                        <td style="width: 200px;">Add Top 4 Scores</td>
                                        @foreach($data['profile']['recommendation']['lpsd_points'] as $key => $value)    
                                            <td @if($loop->last) style="width: 100px;" @endif>{{$key}}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>LPSD Points</td>
                                        @foreach($data['profile']['recommendation']['lpsd_points'] as $key => $value)    
                                            <td>{{$value}}</td>
                                        @endforeach
                                    </tr>
                                </table>
                            @endif
                        </div>
                    @endif
                    <!-- Test Score Data -->
                    @if ($data['profile']['test_score']['status'] == 'Y')
                        <div class="section section-2 page">
                            <div class="section-title">
                                <table class="" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td><b>Universal Screener Criteria</b></td>
                                            <td align="right">{{$data['profile']['test_score']['scored'].'/'.$data['profile']['test_score']['total']}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @php
                                $ts_data_count = count($data['profile']['test_score']['data']);
                                $ts_table_count = round($ts_data_count/2);
                                $ts_itr = $ts_table_count;
                            @endphp
                            @if($ts_data_count > 0)
                                @for($i=0; $i<$ts_table_count; $i++)
                                    @php
                                        $ts_rmn_sub_count = count($data['profile']['test_score']['test_scores']);
                                        if ($i > 0) {
                                            $ts_tbl_class = 'tbl_top_mrgn';
                                        } else {
                                            $ts_tbl_class = '';
                                        }
                                    @endphp
                                    <table class="table {{$ts_tbl_class}}" @if($ts_rmn_sub_count <= 1) style="width: 50%;" @endif>
                                        <tr>
                                            @php
                                                $avg_ts_total = round(($data['profile']['test_score']['total']/(count($data['profile']['test_score']['test_scores']))), 2);
                                                $flag = 2;
                                            @endphp
                                            @foreach($data['profile']['test_score']['test_scores'] as $subject => $score)
                                                @php
                                                    if ($flag <= 0)
                                                        break;
                                                    $flag--;
                                                    $sub_short_name = ($data['profile']['test_score']['data'][$subject]['short_name'] ?? '');
                                                    $subject_name = ($sub_short_name != '') ? $sub_short_name : $subject;
                                                    $pts_scored = ($data['profile']['test_score']['data'][$subject]['score'] ?? '0');
                                                @endphp
                                                <td style="width: 50%;">{{$subject_name.' ('.$score.'%): '.$pts_scored.'/'.$avg_ts_total}}</td>
                                            @endforeach
                                        </tr>
                                        @if($data['profile']['test_score']['is_txt'])
                                            <tr>
                                                @php
                                                    $flag = 2;
                                                @endphp
                                                @foreach($data['profile']['test_score']['data'] as $key => $ts_values)
                                                    @php
                                                        if ($flag <= 0)
                                                            break;
                                                        $flag--;
                                                    @endphp
                                                    <td class="font-12-1">
                                                        @foreach($ts_values['txt'] as $txt_values)
                                                        @if($loop->index !== 0)
                                                        <br>
                                                        @endif
                                                            {{$txt_values}}
                                                        @endforeach
                                                    </td>
                                                    @php
                                                        // remove printed subjects
                                                        unset($data['profile']['test_score']['test_scores'][$key]); 
                                                        unset($data['profile']['test_score']['data'][$key]); 
                                                    @endphp
                                                @endforeach
                                            </tr>
                                        @endif
                                    </table>
                                    @if(($ts_itr > 1) && ($ts_table_count > 1)) {{-- <br> --}} @endif
                                    @php $ts_itr--; @endphp
                                @endfor
                            @endif
                        </div>
                    @endif
                    <!-- Grade Data -->
                    @if ($data['profile']['grade']['status'] == 'Y')
                        @php
                            $ag_data = $data['student_profile']['eligibility']['academic_grades_data'] ?? '';
                            $ag_data = ($ag_data!='null') ? json_decode($ag_data, 1) : [];
                             //dd($ag_data);
                        @endphp
                        @if (!empty($ag_data) && isset($data['profile']['grade']))
                            <div class="section section-3 page">
                                <div class="section-title">
                                    <table class="" style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td><b>Student Performance Criteria</b></td>
                                                <td align="right">{{$data['profile']['grade']['scored'].'/'.$data['profile']['grade']['total']}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                @php
                                    $year_grades = $data['profile']['grade']['year_grades'] ?? [];
                                    $subjects_ary_conf = config('variables.ag_eligibility_subjects');
                                    // dd(3);
                                @endphp
                                @if(isset($year_grades) && !empty($year_grades))
                                    @if(isset($ag_data['part_1']['ts_scores']) || isset($ag_data['part_2']['ts_scores']))
                                        <table class="table">
                                            <tr>
                                                @for($i=1; $i<3; $i++)
                                                    @php
                                                        // $rng_method = array_key_first(($ag_data['part_'.$i]['rangeselection'] ?? []));
                                                        $ts_fields = $ag_data['part_'.$i]['ts_scores'] ?? [];
                                                        $ts_fields_count = count($ts_fields);
                                                        $clspn = $ts_fields_count * (count($year_grades));
                                                        // dd($ag_data['part_1']['ts_scores']);
                                                    @endphp
                                                    @if(($clspn > 0) && !empty($ts_fields))
                                                        <td colspan="{{$clspn}}">
                                                            @if($ts_fields_count > 0)
                                                                @foreach($ag_data['part_'.$i]['ts_scores'] as $ts_field)
                                                                    {{$subjects_ary_conf[$ts_field]}}
                                                                    @if(next($ag_data['part_'.$i]['ts_scores']) !== false) and @endif
                                                                @endforeach
                                                                : {{ $data['profile']['grade']['data']['part_'.$i]['scored'].'/'.($data['profile']['grade']['total']/2)}}
                                                            @endif
                                                        </td>
                                                    @endif
                                                @endfor
                                            </tr>
                                            <tr>
                                                @if(isset($year_grades) && !empty($year_grades))
                                                    @for($i=1; $i<3; $i++)
                                                        @if(isset($ag_data['part_'.$i]['ts_scores']))
                                                            @foreach($year_grades as $ts_year_grade)
                                                                @foreach($ag_data['part_'.$i]['ts_scores'] as $ts_field)
                                                                <td>
                                                                    {{$ts_year_grade}}<br>
                                                                    {{$subjects_ary_conf[$ts_field]}}
                                                                </td>
                                                                @endforeach
                                                            @endforeach
                                                        @endif
                                                    @endfor
                                                @endif
                                            </tr>
                                            <tr style="font-size: 15px !important;">
                                                {{-- @if(isset($year_grades) && !empty($year_grades)) --}}
                                                    @for($i=1; $i<3; $i++)
                                                        @php
                                                            $range_method = $data['profile']['grade']['data']['part_'.$i]['range_method'];
                                                            // dd(2);
                                                            // dump('dump=',$i);
                                                        @endphp
                                                        @if(isset($ag_data['part_'.$i]['ts_scores']))
                                                            @if($range_method == 'abc')
                                                                @if(isset($year_grades) && !empty($year_grades))
                                                                    @foreach($year_grades as $ts_year_grade)
                                                                        @foreach($ag_data['part_'.$i]['ts_scores'] as $ts_field)
                                                                            @php
                                                                            // dd($data['profile']['grade']);
                                                                                $score = $data['profile']['grade']['data']['part_'.$i]['score_ary'][$ts_year_grade][$ts_field]['score'] ?? '-';
                                                                                $points = $data['profile']['grade']['data']['part_'.$i]['score_ary'][$ts_year_grade][$ts_field]['points'] ?? 0;
                                                                            @endphp
                                                                            <td class="font-12">{{ '('.$score.')'.' '.$points}}</td>
                                                                        @endforeach
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                            @if($range_method == '3s2s')
                                                                @if(isset($year_grades) && !empty($year_grades))
                                                                    {{-- @for($j=1; $j<3; $j++) --}}
                                                                        @php
                                                                            $data_3s2s = $data['profile']['grade']['data']['part_'.$i]['3s2s'];
                                                                        @endphp
                                                                        @if(isset($ag_data['part_'.$i]['ts_scores']))
                                                                            @foreach($year_grades as $ts_year_grade)
                                                                                @foreach($ag_data['part_'.$i]['ts_scores'] as $ts_field)
                                                                                    @if(isset($data_3s2s) && !empty($data_3s2s))
                                                                                        @php
                                                                                            $tr_data_ary = ($data_3s2s[$ts_year_grade][$ts_field] ?? []);
                                                                                        @endphp
                                                                                        <td class="font-12">
                                                                                            @foreach($tr_data_ary as $identifier=>$tr_val)
                                                                                                {{$identifier}} :
                                                                                                {{$tr_val}}
                                                                                                @if(!$loop->last) <br> @endif
                                                                                            @endforeach
                                                                                        </td>
                                                                                    @endif
                                                                                @endforeach
                                                                            @endforeach
                                                                        @endif
                                                                    {{-- @endfor --}}
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endfor
                                                {{-- @endif --}}
                                            </tr>
                                        </table>
                                        <table class="table tbl_top_mrgn">
                                            <tr>
                                                @if(isset($year_grades) && !empty($year_grades))
                                                    @for($i=1; $i<3; $i++)
                                                        @php
                                                            // Check for subject selection
                                                            if (!isset($ag_data['part_'.$i]['ts_scores']) || empty($ag_data['part_'.$i]['ts_scores'])) {
                                                                continue;
                                                            }
                                                            if (isset($ag_data['part_'.$i]['rangeselection']['abc'])) {
                                                                $range_selection = $ag_data['part_'.$i]['rangeselection']['abc'];
                                                            } elseif (isset($ag_data['part_'.$i]['rangeselection']['3s2s'])) {
                                                                $range_selection = $ag_data['part_'.$i]['rangeselection']['3s2s'];
                                                            } else {
                                                                $range_selection = [];
                                                            }
                                                        @endphp
                                                        @if(!empty($range_selection))
                                                            <td width="50%">
                                                                @if(isset($ag_data['part_'.$i]['ts_scores']))
                                                                    @foreach($ag_data['part_'.$i]['ts_scores'] as $ts_field)
                                                                        {{$subjects_ary_conf[$ts_field]}}
                                                                        @if(next($ag_data['part_'.$i]['ts_scores']) !== false) and @endif
                                                                    @endforeach
                                                                    <br>
                                                                @endif
                                                                @foreach($range_selection as $rngkey=>$rngval)
                                                                    @php
                                                                        $points = ($rngval != '') ? $rngval.' Points' : '-';
                                                                    @endphp
                                                                    {{$rngkey.' = '. $points}} <br>
                                                                @endforeach
                                                            </td>
                                                        @endif
                                                    @endfor
                                                @endif
                                            </tr>
                                        </table>
                                    @endif
                                @endif
                            </div>
                        @endif
                    @endif
                    <!-- Conduct Discpline Creiteria -->
                    @if(isset($data['profile']['conduct_discpline_criteria']['data']))
                        @php
                            $incident_count = ($data['profile']['conduct_discpline_criteria']['data']['incident_count'] ?? 0);
                            $incidents = ($data['profile']['conduct_discpline_criteria']['data']['incidents'] ?? []);
                        @endphp
                        <div class="section section-4 page">
                            <div class="section-title">
                                <table class="" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td><b>Conduct Criteria</b></td>
                                        </tr>
                                        <tr><td>Note: Three(3) Most Recent Office Discpline Referrals (ODRs) (Fields will be blank if not applicable):</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <table class="table">
                                @for($i=0; $i<3; $i++)
                                    @php $itr = $i+1; @endphp
                                    <tr>
                                        <td>{{$itr}}</td>
                                        <td>
                                            @if(isset($incidents[$i]['date']) && ($incidents[$i]['date'] != '')) 
                                                {{ ($incidents[$i]['date']) }} <br>
                                            @endif
                                            @if(isset($incidents[$i]['incident']) && ($incidents[$i]['incident'] != '')) 
                                                Incident: {{($incidents[$i]['incident'])}} <br>
                                            @endif
                                            @if(isset($incidents[$i]['infraction']) && ($incidents[$i]['infraction'] != '')) 
                                                Infraction: {{($incidents[$i]['infraction'])}} <br><br>
                                            @endif
                                            @if(isset($incidents[$i]['action']) && ($incidents[$i]['action'] != '')) 
                                                Action: {{($incidents[$i]['action'])}} <br>
                                            @endif
                                        </td>
                                    </tr>
                                @endfor
                            </table>
                        </div>
                    @endif
                    <!-- Final Data -->
                    <table style="width: 100%; padding: 1px !important; margin-left: -6px;">
                        <tr>
                            <td style="font-weight: bold; font-size: 13px !important;">
                                <span>Total Student Profile Score {{$data['profile']['student_score']}} / {{$data['profile']['total']}}</span><br>
                                <span>Student Profile Percentage Score {{$data['profile']['final_percent']}}%</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="wrapper">
                    <div class="section section-5 page" style="bottom: 0px; ">
                        <table style="width: 100%;">
                            <tr><td align="center">
                                <div><b>Tuscaloosa City Schools</b></div>
                                <div><b>1210 21st Avenue | Tuscaloosa, AL 36104 | (205) 759-3700</b></div>
                                <div><span style="color: #10335d;"><b>AN EQUAL OPPORTUNITY EMPLOYER</b></span></div></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>