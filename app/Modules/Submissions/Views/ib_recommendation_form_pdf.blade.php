<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon" />
<title>Tuscaloosa City Schools</title>

<style>
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
       .w-50 {width:50%;}
    .f-12{font-size:13px;}

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
@media print {
    .table {border: solid #000 !important; border-width: 1px 0 0 1px !important;}
    .table th, .table td {border: solid #000 !important; border-width: 0 1px 1px 0 !important;}
}
</style>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center"><img src="{{getDistrictLogo()}}" width="200"></td>
    </tr>
    <tr>
      <td height="10"></td>
    </tr>
    @php
      $subject = explode('.', $answer_data->config_value)[0];  
    @endphp
    <tr>
      <td><table border="0" width="100%" cellpadding="3" cellspacing="0">
          <tbody>
            <tr>
              <td class="w-50 f-12">{{$submission->confirmation_no ?? ''}}</td>
              <td class="f-12">Student: {{$submission->first_name. ' ' . $submission->last_name}}</td>
            </tr>
            <tr>
              <td class="f-12">School: {{$submission->current_school ?? ''}}</td>
              <td class="f-12">Title: {{config('variables.recommendation_subject')[$subject] ?? ''}}</td>
            </tr>
            <tr>
              <td class="f-12">Teacher: {{$answer_data->teacher_name ?? ''}}</td>
              <td class="f-12">Email: {{$answer_data->teacher_email ?? ''}}</td>
            </tr>
          </tbody>
        </table></td>
    </tr>
    <tr>
      <td height="10"></td>
    </tr>
    <tr>
      <td><hr></td>
    </tr>
    <tr>
      <td height="10"></td>
    </tr>
    @if(isset($content->answer))
      @php $title = '' @endphp
      @foreach($content->answer as $key=>$header)
         @if(!isset($header->points) && !isset($header->options) )
            @php $title = $header->name @endphp
          @endif
      @endforeach
      <tr><td style="font-size:20px;" class="center"><strong>{{$title}}</strong></td></tr>
      <tr><td height="10"></td></tr>
      <tr><td class="f-12">The above student is applying to the Tuscaloosa City Schools International Baccalaureate Program. This rigorous academic curriculum challenges students to learn, analyze, and reach considered conclusions about people, language and literature, and the scientific forces of the environment. Students must have established strong scholastic backgrounds across all core subject areas. The successful IB student possesses a high degree of motivation, self-discipline, and a genuine love of learning. Each form should can only be submitted once. Please complete at submit this form withing 14 days of receipt.</td></tr> 
      @php $psoptions = [] @endphp
      @foreach($content->answer as $key=>$header)
          @if(isset($header->options) && strtoupper($header->name) != "OVERALL")
              @php $psoptions = $header->options @endphp
              <tr>
                  <td height="10"></td>
              </tr>

              @if($loop->index == 1)
                <tr>
                  <td class="f-12">Please place an X in the number column that best corresponds to your choice based on the provided scale.</td>
                </tr>
                <tr>
                  <td height="10"></td>
                </tr>
              @endif

              <tr>
                <td>
                  <table class="table" style="width:98.8%;" border="1" cellpadding="5" cellspacing="0">
                    <thead>
                      <tr>
                        <td colspan="2" style="width:calc(100% - 60px); text-align: center;" class="f-12">{{$header->name ?? ''}}</td>

                          @foreach($psoptions as $pk => $point)
                            <td style="width:15px;line-height:20px;text-align:center;" class="f-12">{{$point}}</td>
                        @endforeach
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($header->answers as $ak => $answer)
                      <tr>
                          <td style="width:15px !important;" class="f-12">{{$loop->iteration ?? ''}}.</td>
                          <td class="f-12">{{$ak ?? ''}}</td>

                          @foreach($psoptions as $pk => $point)
                            <td class="f-12" align="center">
                              @if($answer == $point)
                              X
                              @endif
                            </td>
                          @endforeach
                      </tr>
                      @endforeach
                    </tbody>
                  </table></td>
              </tr>
              <tr>
                <td height="10"></td>
              </tr>
            @elseif(strtoupper($header->name) == "OVERALL")
              <tr>
                  <td>
                      <table class="table" style="width:98.8%;" border="1" cellpadding="5" cellspacing="0">
                        <thead>
                          @foreach($header->answers as $hk=>$hv)
                            <tr>
                              <td class="f-12">{{$header->name ?? ''}}</td>
                              <td>{{$hv}}</td>
                            </tr>
                          @endforeach
                        </thead>
                      </table>
                  </td>
              </tr>
            @endif
      @endforeach
    @endif

    
    <tr>
      <td height="10"></td>
    </tr>
    <tr><td class="f-12">If you would like to add any further comments about the student, please do so in the space provided below. You may want to comment briefly on the suitability of the student’s participation in the IB Program’s advanced studies. We would appreciate your honesty in expressing any reservations you may have, and in helping put test scores and/or the student’s academic record in context. ALL RECOMMENDATIONS ARE CONFIDENTIAL.</td>
</tr>
<tr>
      <td height="10"></td>
    </tr>
    @if(isset($answer_data->comment) && $answer_data->comment != '')
      <tr>
        <td >Additional Comments:</td>
      </tr>
      <tr>
        <td>{{$answer_data->comment ?? ''}}</td>
      </tr>
    @endif
   
  </tbody>
</table>
</body>
</html>