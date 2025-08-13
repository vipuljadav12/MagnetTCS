<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="" rel="stylesheet">
    <style type="text/css">
     
    @font-face {
        font-family:'Open Sans';
        src:url('fonts/OpenSans-Regular.ttf') format('truetype');
        font-weight:normal;
        font-style:normal;
    }
    @font-face {
        font-family:'Open Sans SemiBold';
        src:url('storage/fonts/OpenSans-SemiBold.ttf') format('truetype');
        font-weight:normal;
        font-style:normal;
    }
    @font-face {
        font-family:'Open Sans bold';
        src:url('fonts/OpenSans-Bold.ttf') format('truetype');
        font-weight:normal;
        font-style:normal;
    }
   
       footer {
                position: fixed; 
                bottom: -30px; 
                left: 0px; 
                right: 0px;
                height: 50px; 
                /** Extra personal styles **/
                /*border-top: 1px solid #000;*/
                text-align: center;
                line-height: 35px;
            }

    body {padding:10px; margin:0; font-family: 'Open Sans', sans-serif; font-size:15px;}
    .container {max-width:720px; margin:0 auto; padding-top: 80px;}
    img {max-width:100%;}
    .w-50 {width:50%;}
    .w-80 {width:80%;}
    .w-100 {width:100%;}
    .logo-box {width:120px; margin-bottom:20px;}
    .logo-box.text-right {margin-left:auto;}
    .text-center {text-align:center;}
    .table {width:100%; border:1px solid #ccc; border-collapse:collapse;}
    .table tr {padding:0; margin:0; border-bottom:1px solid #ccc;}
    .table tr th {padding:10px 5px;margin:0; border-top:1px solid #ccc; border-right:1px solid #ccc; font-size: 13px;}
    .table tr td {padding:10px 5px;margin:0; border-top:1px solid #ccc; border-right:1px solid #ccc;}
    .small-text{font-size:11px; line-height: 14px;}
    .section {margin-bottom:30px;}
    .section-title {padding:10px; text-align:center;background:#666;color:#fff;font-size:14px;text-transform:uppercase;}
    .section-1 {}  
    .text-right {text-align:right;}
    .f-12{font-size:13px;}
    header {
                position: fixed;
                top: -20px;
                left: 0px;
                right: 0px;
                height: 50px;
                max-width: 700px;

                /** Extra personal styles **/
                
                
            }



    </style>
    
  </head>
  <body>
    @php $config_subjects = Config::get('variables.subjects') @endphp
    <header>
        <div class="header">
          <table class="w-100">
              <tbody>
                  <tr>
                      <td class="w-100">
                         @php $logo = (isset($application_data) ? getDistrictLogo($application_data->display_logo) : getDistrictLogo()) @endphp
                        <div class="logo-box" style="padding-left: 10px;"><img src="{{str_replace('https://', 'http://', $logo)}}" title="" alt="" style="max-width: 100px !important;"></div></td>
                      <td class="w-50 text-right"><strong>Student Applicant Data Sheet</strong></td>
                  </tr>
              </tbody>
          </table>
      </div>
    </header>
    <footer>
          <table class="w-100">
            <tbody>
                <tr>
                    <td class="w-80 small-text"><i>Note: Information captured in this sheet is accurate as of the form generation date and is subject to change following that date.</i></td>
                    <td class=""><div class="logo-box text-right"><img src="{{str_replace('https:', 'http:', url('/resources/assets/admin/images/login.png'))}}" title="" alt="" style="max-width: 130px !important;"></div></td>
                </tr>
            </tbody>
        </table>
        </footer>
    @foreach($student_data as $value)
        <div class="container page">
          <div class="wrapper">
              <div class="section section-1">
                  <div class="section-title"><strong>Student Information</strong></div>
                  <table class="table">
                      <tbody>
                          <tr>
                              <td class="w-50 f-12"><strong>Student Name:</strong> {{$value['name']}}</td>
                              <td class="f-12"><strong>Form Generated:</strong> {{getDateTimeFormat($value['created_at'])}}</td>
                          </tr>
                          <tr>
                              <td class="f-12"><strong>Submission ID:</strong> {{$value['id']}}</td>
                              <td class="f-12"><strong>Submission Date:</strong> {{getDateTimeFormat($value['created_at'])}} </td>
                          </tr>
                          <tr>
                              <td class="f-12"><strong>Student ID:</strong>  {{($value['student_id'] != "" ? $value['student_id'] : "")}}</td>
                              <td class="f-12"><strong>Date of Birth:</strong> {{getDateFormat($value['birth_date'])}}</td>
                          </tr>
                          <tr>
                              <td class="f-12"><strong>Current School:</strong>  {{$value['current_school']}}</td>
                              <td class="f-12"><strong>Student Status:</strong> {{($value['student_id'] != "" ? "Current" : "Non-Current")}}</td>
                          </tr>
                          <tr>
                              <td class="f-12"><strong>First Choice:</strong>  {{$value['first_choice']}} - Grade {{$value['grade']}}</td>
                              <td class="f-12"><strong>Second Choice:</strong> {{($value['second_choice'] != "" ? $value['second_choice'] . " - Grade ".$value['grade'] : "NA")}}</td>
                          </tr>
                          @if(isset($value['awarded_school']) && $value['awarded_school'] != "")
                          <tr>
                              <td class="f-12" colspan="2"><strong>Awarded Program:</strong>  {{$value['awarded_school']}}</td>
                          </tr>
                          @endif
                      </tbody>
                  </table>
              </div>

              
              {{-- <div class="section section-2">
                <table class="table text-center"  style="border: none !important">
                    <tr  style="border: none !important">
                        <td class="w-50 f-12" style="border: none !important"></td>

                        <td class="w-50 f-12" style="padding: 20px; border: 1px solid black; text-align: left"><strong>Grade Average:</strong> {{$value['avgGrade']}}</td>
                        
                    </tr>
                </table>
              </div> --}}
              @php
                $subject_count = count($subjects) ?? 0;
              @endphp
              @if(isset($terms))
                @foreach ($terms as $tyear => $tvalue)
                  <div class="section section-2">
                    <div class="section-title"><strong>Academic Grades ({{ $tyear }})</strong></div>

                    <table class="table text-center">
                      <tbody>
                        <tr>
                          @forelse($subjects as $svalue)
                            @php
                              $sub = $config_subjects[$svalue] ?? $svalue;
                            @endphp
                            <td class="f-12" colspan="{{ count($tvalue) }}"><strong>{{$sub}}</strong></td>
                          @empty
                          @endforelse
                        </tr>
                        
                        <tr>
                          @forelse($subjects as $svalue)
                            @foreach($tvalue as $value1)
                              <td class="f-12"><strong>{{ $value1 }}</strong></td>
                            @endforeach
                          @empty
                          @endforelse
                        </tr>

                        <tr>
                          @forelse($subjects as $svalue)
                            @foreach($tvalue as $tvalue1)
                              @php
                                  $marks = $value['score'][$tyear][$svalue][$tvalue1] ?? '';
                              @endphp
                              <td class="f-12">{{ $marks }}</td>
                            @endforeach
                          @empty
                          @endforelse
                        </tr>
                    </table>
                  </div>
                @endforeach
              @endif
          </div>
        </div>   
    @endforeach
  </body>

  <style>
    .page {
       page-break-after: always;
    }
    .page:last-child {
       page-break-after: unset;
    }
  </style>

</html>