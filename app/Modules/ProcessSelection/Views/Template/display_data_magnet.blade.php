<div class="">
<div class="">
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped mb-0 w-100" id="datatable">
                <thead>
                    <tr>
                        <th class="align-middle text-center">Sub ID</th>
                        <th class="align-middle text-center">Submission Status</th>
                        <th class="align-middle hiderace text-center">Race</th>
                        <th class="align-middle text-center">Student Status</th>
                        <th class="align-middle text-center">First Name</th>
                        <th class="align-middle text-center">Last Name</th>
                        <th class="align-middle text-center">Next Grade</th>
                        <th class="align-middle text-center">Current School</th>
                        <th class="align-middle hidezone text-center">Zoned School</th>
                        <th class="align-middle text-center">First Choice</th>
                        <th class="align-middle text-center">Second Choice</th>
                        <th class="align-middle text-center">Sibling ID</th>
                        <th class="align-middle text-center">Lottery Number</th>
                        <th class="align-middle text-center committee_score-col">Profile Score</th>
                        <th class="align-middle text-center committee_score-col">Final Status</th>
                        <th class="align-middle text-center committee_score-col">% Status</th>
                    </tr>
                    
                </thead>
                <tbody>
                    @if(!empty($magnet_offer_data))
                        @foreach($magnet_offer_data as $key=>$value)
                            <tr>
                                <td class="">{{$value['id']}}</td>
                                <td class="text-center">{{$value['submission_status']}}</td>
                                <td class="hiderace">{{$value['race']}}</td>
                                <td class="">
                                    @if($value['student_id'] != '')
                                        Current
                                    @else
                                        New
                                    @endif
                                </td>
                                <td class="">{{$value['first_name']}}</td>
                                <td class="">{{$value['last_name']}}</td>
                                
                                <td class="text-center">{{$value['next_grade']}}</td>
                                <td class="">{{$value['current_school']}}</td>
                                <td class="hidezone">{{$value['zoned_school']}}</td>
                                @if($value['choice'] == "first")
                                    <td class="">{{getProgramName($value['program_id'])}}</td>
                                    <td class="text-center"></td>
                                @else
                                    <td class="text-center"></td>
                                    <td class="">{{getProgramName($value['program_id'])}}</td>
                                @endif
                                <td class="">
                                    @php $sibling_id = $value[$value['choice'].'_sibling'] @endphp
                                    @if($sibling_id  != '')
                                        <div class="alert1 alert-success p-10 text-center">{{$sibling_id}}</div>
                                    @else
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    @endif
                                </td>

                                <td class="">{{$value['lottery_number']}}</td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            {!! $value['student_profile'] !!}
                                        </div>
                                </td>
                                <td class="text-center">{{ $value['offer_status']}}</td>
                                <td class="text-center">{!! $value['percent_status'] ?? '' !!}	</td>
                            </tr>
                        @endforeach
                    @endif

                    @if(isset($first_magnet_processing['waitlisted_arr']))

                        @foreach($first_magnet_processing['waitlisted_arr'] as $key=>$value)
                            <tr>
                                <td class="">{{$value['id']}}</td>
                                <td class="text-center">{{$value['submission_status']}}</td>
                                <td class="hiderace">{{$value['race']}}</td>
                                <td class="">
                                    @if($value['student_id'] != '')
                                        Current
                                    @else
                                        New
                                    @endif
                                </td>
                                <td class="">{{$value['first_name']}}</td>
                                <td class="">{{$value['last_name']}}</td>
                                
                                <td class="text-center">{{$value['next_grade']}}</td>
                                <td class="">{{$value['current_school']}}</td>
                                <td class="hidezone">{{$value['zoned_school']}}</td>
                                <td class="">{{getProgramName($value['program_id'])}}</td>
                                <td class="text-center"></td>
                                <td class="">
                                        @php $sibling_id = $value['first_sibling'] @endphp
                                    @if($sibling_id  != '')
                                        <div class="alert1 alert-success p-10 text-center">{{$sibling_id}}</div>
                                    @else
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    @endif
                                </td>
                                <td class="">{{$value['lottery_number']}}</td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            {!! $value['student_profile'] !!}
                                        </div>
                                </td>
                                <td class="text-center">{{ $value['offer_status']}}</td>
                                <td class="text-center">{!! $value['percent_status'] ?? '' !!}	</td>
                            </tr>
                        @endforeach
                    @endif

                    @if(isset($second_magnet_processing['waitlisted_arr']))
                        @foreach($second_magnet_processing['waitlisted_arr'] as $key=>$value)
                            <tr>
                                <td class="">{{$value['id']}}</td>
                                <td class="text-center">{{$value['submission_status']}}</td>
                                <td class="hiderace">{{$value['race']}}</td>
                                <td class="">
                                    @if($value['student_id'] != '')
                                        Current
                                    @else
                                        New
                                    @endif
                                </td>
                                <td class="">{{$value['first_name']}}</td>
                                <td class="">{{$value['last_name']}}</td>
                                
                                <td class="text-center">{{$value['next_grade']}}</td>
                                <td class="">{{$value['current_school']}}</td>
                                <td class="hidezone">{{$value['zoned_school']}}</td>
                                <td class="text-center"></td>
                                <td class="">{{getProgramName($value['program_id'])}}</td>
                                

                                <td class="">
                                        @php $sibling_id = $value['second_sibling'] @endphp
                                    @if($sibling_id  != '')
                                        <div class="alert1 alert-success p-10 text-center">{{$sibling_id}}</div>
                                    @else
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    @endif
                                </td>
                                <td class="">{{$value['lottery_number']}}</td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            {!! $value['student_profile'] !!}
                                        </div>
                                </td>
                                <td class="text-center">{{ $value['offer_status']}}</td>
                                <td class="text-center">{!! $value['percent_status'] ?? '' !!}	</td>
                            </tr>
                        @endforeach 
                    @endif


                    @foreach($first_magnet_processing['no_availability_arr'] as $key=>$value)
                        <tr>
                            <td class="">{{$value['id']}}</td>
                            <td class="text-center">{{$value['submission_status']}}</td>
                            <td class="hiderace">{{$value['race']}}</td>
                            <td class="">
                                @if($value['student_id'] != '')
                                    Current
                                @else
                                    New
                                @endif
                            </td>
                            <td class="">{{$value['first_name']}}</td>
                            <td class="">{{$value['last_name']}}</td>
                            
                            <td class="text-center">{{$value['next_grade']}}</td>
                            <td class="">{{$value['current_school']}}</td>
                            <td class="hidezone">{{$value['zoned_school']}}</td>
                            <td class="">{{getProgramName($value['program_id'])}}</td>
                            <td class="text-center"></td>
                            <td class="">
                                    @php $sibling_id = $value['first_sibling'] @endphp
                                @if($sibling_id  != '')
                                    <div class="alert1 alert-success p-10 text-center">{{$sibling_id}}</div>
                                @else
                                    <div class="alert1 alert-warning p-10 text-center">NO</div>
                                @endif
                            </td>
                            <td class="">{{$value['lottery_number']}}</td>
                            
                            <td class="text-center committee_score-col">
                                    <div class="alert1 alert-success">
                                        {!! $value['student_profile'] !!}
                                    </div>
                            </td>
                            <td class="text-center">Waitlisted<br>[No Availability]</td>
                            <td class="text-center">{!! $value['percent_status'] ?? '' !!}	</td>
                        </tr>
                    @endforeach

                    @foreach($second_magnet_processing['no_availability_arr'] as $key=>$value)
                        <tr>
                            <td class="">{{$value['id']}}</td>
                            <td class="text-center">{{$value['submission_status']}}</td>
                            <td class="hiderace">{{$value['race']}}</td>
                            <td class="">
                                @if($value['student_id'] != '')
                                    Current
                                @else
                                    New
                                @endif
                            </td>
                            <td class="">{{$value['first_name']}}</td>
                            <td class="">{{$value['last_name']}}</td>
                            
                            <td class="text-center">{{$value['next_grade']}}</td>
                            <td class="">{{$value['current_school']}}</td>
                            <td class="hidezone">{{$value['zoned_school']}}</td>
                            <td class="text-center"></td>
                            <td class="">{{getProgramName($value['program_id'])}}</td>
                            
                            <td class="">
                                    @php $sibling_id = $value['second_sibling'] @endphp
                                @if($sibling_id  != '')
                                    <div class="alert1 alert-success p-10 text-center">{{$sibling_id}}</div>
                                @else
                                    <div class="alert1 alert-warning p-10 text-center">NO</div>
                                @endif
                            </td>
                            <td class="">{{$value['lottery_number']}}</td>
                            
                            <td class="text-center committee_score-col">
                                    <div class="alert1 alert-success">
                                        {!! $value['student_profile'] !!}
                                    </div>
                            </td>
                            <td class="text-center">Waitlisted<br>[No Availability]</td>
                            <td class="text-center">{!! $value['percent_status'] ?? '' !!}	</td>

                        </tr>
                    @endforeach                     

                    @if(isset($first_magnet_processing['in_eligible']))
                        @foreach($first_magnet_processing['in_eligible'] as $key=>$value)
                            <tr>
                                <td class="">{{$value['id']}}</td>
                                <td class="text-center">{{$value['submission_status']}}</td>
                                <td class="hiderace">{{$value['race']}}</td>
                                <td class="">
                                    @if($value['student_id'] != '')
                                        Current
                                    @else
                                        New
                                    @endif
                                </td>
                                <td class="">{{$value['first_name']}}</td>
                                <td class="">{{$value['last_name']}}</td>
                                
                                <td class="text-center">{{$value['next_grade']}}</td>
                                <td class="">{{$value['current_school']}}</td>
                                <td class="hidezone">{{$value['zoned_school']}}</td>
                                @if($value['choice'] == "first")
                                    <td class="">{{getProgramName($value['program_id'])}}</td>
                                    <td class="text-center"></td>
                                @else
                                    <td class="text-center"></td>
                                    <td class="">{{getProgramName($value['program_id'])}}</td>
                                @endif
                                <td class="">
                                    @php $sibling_id = $value[$value['choice'].'_sibling'] @endphp
                                    @if($sibling_id  != '')
                                        <div class="alert1 alert-success p-10 text-center">{{$sibling_id}}</div>
                                    @else
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    @endif
                                </td>
                                <td class="">{{$value['lottery_number']}}</td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            {!! $value['student_profile'] !!}
                                        </div>
                                </td>
                                <td class="text-center text-danger">Denied Due to Ineligibility</td>
                                <td class="text-center">{!! $value['percent_status'] ?? '' !!}	</td>
                            </tr>
                        @endforeach
                    @endif

                    @if(isset($second_magnet_processing['in_eligible']))
                        @foreach($second_magnet_processing['in_eligible'] as $key=>$value)
                            <tr>
                                <td class="">{{$value['id']}}</td>
                                <td class="text-center">{{$value['submission_status']}}</td>
                                <td class="hiderace">{{$value['race']}}</td>
                                <td class="">
                                    @if($value['student_id'] != '')
                                        Current
                                    @else
                                        New
                                    @endif
                                </td>
                                <td class="">{{$value['first_name']}}</td>
                                <td class="">{{$value['last_name']}}</td>
                                        
                                <td class="text-center">{{$value['next_grade']}}</td>
                                <td class="">{{$value['current_school']}}</td>
                                <td class="hidezone">{{$value['zoned_school']}}</td>
                                @if($value['choice'] == "first")
                                    <td class="">{{getProgramName($value['program_id'])}}</td>
                                    <td class="text-center"></td>
                                @else
                                    <td class="text-center"></td>
                                    <td class="">{{getProgramName($value['program_id'])}}</td>
                                @endif
                                <td class="">
                                    @php $sibling_id = $value[$value['choice'].'_sibling'] @endphp
                                    @if($sibling_id  != '')
                                        <div class="alert1 alert-success p-10 text-center">{{$sibling_id}}</div>
                                    @else
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    @endif
                                </td>
                                <td class="">{{$value['lottery_number']}}</td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            {!! $value['student_profile'] !!}
                                        </div>
                                </td>
                                <td class="text-center text-danger">Denied Due to Ineligibility</td>
                                <td class="text-center">{!! $value['percent_status'] ?? '' !!}	</td>
                            </tr>
                        @endforeach
                    @endif                                                            
                </tbody>
            </table>
            
        </div>
    </div>
</div>
</div>
</div>
