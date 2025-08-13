<div class="left side-menu" datasidebar-bg="theme05">
    <div class="">
        <div class="border-bottom d-flex align-items-center" style="height: 70px; background: <?php echo e(Session::get("theme_color")); ?>;">
                    <div class="logo-box p-10 text-center"> <a href="<?php echo e(url('/admin/dashboard')); ?>" title="" class="logo-dark h-100"><img class="img-fluid h-100" src="<?php echo e(getDistrictLogo()); ?>" title="" alt=""></a> <a href="<?php echo e(url('/admin/dashboard')); ?>" title="" class="logo-dark-small h-100"><img class="img-fluid h-100" src="<?php echo e(getDistrictLogo()); ?>" title="" alt=""></a> <a href="<?php echo e(url('/admin/dashboard')); ?>" title="" class="logo-light h-100"><img class="img-fluid h-100" src="<?php echo e(getDistrictLogo()); ?>" title="" alt=""></a> <a href="<?php echo e(url('/admin/dashboard')); ?>" title="" class="logo-light-small h-100"><img class="img-fluid h-100" src="<?php echo e(getDistrictLogo()); ?>" title="" alt=""></a> </div>
                    <div class="side-menu-btn text-primary"><i class="fa-2x fas fa-bars"></i></div>
                </div>
    </div>
    <div class="slimscroll-menu" id="remove-scroll"> 
        <!--- Sidemenu -->
        <div id="sidebar-menu"> 
            <!-- Left Menu Start -->
            <ul class="metismenu" id="side-menu">
                <li class=""><a title="Dashboard" href="<?php echo e(url('/admin/dashboard')); ?>"><i class="far fa-chart-bar"></i><span>Dashboard</span></a></li>
                <?php if((checkPermission(Auth::user()->role_id,'Submissions') == 1)): ?>
                    <li class="">
                       <a title="Submission Workspace" href="javascript:void(0);" class=""><i class="far fa-address-card"></i><span>Submission Workspace</span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li class=""><a title="Submissions" href="<?php echo e(url('/admin/Submissions')); ?>" class="active"><span>Submissions</span></a></li>
                            <li class=""><a title="Custom Communication" href="<?php echo e(url('/admin/CustomCommunication')); ?>"><span>Custom Communications</span></a></li>
                            <li class=""><a title="Generate Application Data" href="<?php echo e(url('/admin/GenerateApplicationData')); ?>"><span>Generate Application Data Sheets</span></a></li>
                            <li class=""><a title="Print Writing Prompts/Recommendation" href="<?php echo e(url('/admin/GenerateApplicationData/generated/form')); ?>"><span>Generate Submission Forms</span></a></li>
                            <li class=""><a title="Admin Review" href="<?php echo e(url('/admin/Reports/admin_review')); ?>"><span>Admin Review</span></a></li> 
                            <li class=""><a title="Parent Submitted Records" href="<?php echo e(url('/admin/Reports/missing/15/gradecdiupload')); ?>"><span>Parent Submitted Records</span></a></li>
                            <li class=""><a title="Export Submissions" href="<?php echo e(url('/admin/Reports/export/submissions')); ?>"><span>Export Submissions</span></a></li>                          
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if((checkPermission(Auth::user()->role_id,'Enrollment') == 1)): ?>
                    <li class=""><a title="Create New Enrollment Period" href="<?php echo e(url('/admin/Enrollment')); ?>"><i class="far fa-calendar-alt"></i><span>New Enrollment Period</span></a></li>
                <?php endif; ?>
                <?php if((checkPermission(Auth::user()->role_id,'Application') == 1)): ?>
                    <li class=""><a title="Setup Application" href="<?php echo e(url('/admin/Application')); ?>"><i class="far fa-file-alt"></i><span>Setup Application</span></a></li>
                <?php endif; ?>
                <?php if((checkPermission(Auth::user()->role_id,'SetEligibility') == 1)): ?>
                    <li class="">
                        <a title="Set Eligibility Values" href="<?php echo e(url('/admin/SetEligibility')); ?>"><i class="fa fa-tasks"></i><span>Set Eligibility Values</span></a>
                    </li>
                <?php endif; ?>
                <?php if((checkPermission(Auth::user()->role_id,'Availability') == 1)): ?>
                    <li>
                        <a title="Process Selection" href="javascript:void(0);" class=""><i class="fa fa-user-check"></i><span>Process Selection</span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li class=""><a title="Set Availability" href="<?php echo e(url('/admin/Availability')); ?>" class="active"><span>Set Availability</span></a></li>
                            <li class="d-none"><a title="Program Max Percent Swing" href="<?php echo e(url('/admin/Process/Selection/settings')); ?>" class="active"><span>Program Max Percent Swing</span></a></li>
                             <li class=""><a title="Preliminary Processing" href="<?php echo e(url('/admin/Preliminary/Processing')); ?>"><span>Preliminary Processing</span></a></li> 
                             <li><a title="Process Selection" href="<?php echo e(url('/admin/Process/Selection/')); ?>"><span>Run Selection</span></a></li> 
                             <li><a title="Edit Communication" href="<?php echo e(url('/admin/EditCommunication')); ?>"><span>Edit Communication</span></a></li> 
                             <li class=""><a title="Edit Screen Text" href="<?php echo e(url('/admin/DistrictConfiguration/edit_text')); ?>"><span>Edit Screen Text</span></a></li> 
                             <li><a title="Edit Final Confirmation Email" href="<?php echo e(url('/admin/DistrictConfiguration/edit_email')); ?>"><span>Edit Final Confirmation Email</span></a></li> 
                            
                        </ul>
                    </li>
                <?php endif; ?>
                    <li class="">
                        <a title="Process Selection" href="javascript:void(0);" class=""><i class="fa fa-user-cog"></i><span>Process Waitlist</span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li class=""><a title="Run Selection" href="<?php echo e(url('/admin/Waitlist')); ?>" class="active"><span>Run Selection</span></a></li>
                             <li class=""><a title="Edit Communication" href="<?php echo e(url('/admin/Waitlist/EditCommunication')); ?>"><span>Edit Communication</span></a></li> 
                             <li class=""><a title="Edit Screen Text" href="<?php echo e(url('/admin/DistrictConfiguration/edit_waitlist_text')); ?>"><span>Edit Screen Text</span></a></li> 
                             <li class=""><a title="Edit Final Confirmation Email" href="<?php echo e(url('/admin/DistrictConfiguration/edit_waitlist_email')); ?>"><span>Edit Final Confirmation Email</span></a></li> 
                             <!--<li class=""><a title="Population Changes Report" href="<?php echo e(url('/admin/Waitlist/Population/Version/0')); ?>"><span>Population Changes</span></a></li> 
                             <li class=""><a title="Submission Results" href="<?php echo e(url('/admin/Waitlist/Submission/Result/Version/0')); ?>"><span>Submission Results</span></a></li> 
                             <li class=""><a title="Seats Status" href="<?php echo e(url('/admin/Waitlist/Submission/SeatsStatus/Version/0')); ?>"><span>Seats Status</span></a></li> -->
                        </ul>
                    </li>

                     <li class="">
                        <a title="Process Selection" href="javascript:void(0);" class=""><i class="fa fa-user-clock"></i><span>Process Late Submission</span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                             <li class=""><a title="Preliminary Processing" href="<?php echo e(url('/admin/LateSubmission/Preliminary/Processing')); ?>"><span>Preliminary Processing</span></a></li> 
                            <li class=""><a title="Run Selection" href="<?php echo e(url('/admin/LateSubmission')); ?>" class="active"><span>Run Selection</span></a></li>
                             <li class=""><a title="Edit Communication" href="<?php echo e(url('/admin/LateSubmission/EditCommunication')); ?>"><span>Edit Communication</span></a></li> 
                             <li class=""><a title="Edit Screen Text" href="<?php echo e(url('/admin/DistrictConfiguration/edit_late_submission_text')); ?>"><span>Edit Screen Text</span></a></li> 
                             <li class=""><a title="Edit Final Confirmation Email" href="<?php echo e(url('/admin/DistrictConfiguration/edit_late_submission_email')); ?>"><span>Edit Final Confirmation Email</span></a></li></ul>
                    </li>
                <!--<li class=""><a title="Front End" href="form.html"><i class="far fa-list-alt"></i><span>Forms</span></a></li>
                <li class=""><a title="Program" href="program.html"><i class="far fa-star"></i><span>Programs</span></a></li>
                <li class=""><a title="School" href="school.html"><i class="fas fa-school"></i><span>Schools</span></a></li>                            
                <li class=""><a title="Files" href="files.html"><i class="far fa-folder-open"></i><span>Files</span></a></li>
                            
                <li class=""><a title="Translation" href="translation.html"><i class="fas fa-language"></i><span>Translations</span></a></li>
                <li class=""><a title="Emails / Letters" href="javascript:void(0);"><i class="far fa-envelope"></i><span>Emails / Letters</span> <span class="menu-arrow"></span></a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li><a title="Applications" href="application-emails-letters.html"><span>Applications</span></a></li>
                        <li><a title="Program Processing" href="program-processing-emails-letters.html"><span>Program Processing</span></a></li>
                    </ul>
                </li>
               
                <li class=""><a title="Date" href="javascript:void(0);"><i class="far fa-calendar-alt"></i><span>Dates</span> <span class="menu-arrow"></span></a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li><a title="Application Dates" href="application-dates.html"><span>Application Dates</span></a></li>
                        <li><a title="Program Processing Dates" href="program-processing-dates.html"><span>Program Processing Dates</span></a></li>
                    </ul>
                </li>
                <li class=""><a title="Override" href="override.html"><i class="far fa-check-circle"></i><span>Overrides</span></a></li>
                <li class=""><a title="Report" href="report.html"><i class="fas fa-chart-pie"></i><span>Reports</span></a></li>
                -->
               
                <!--<li class=""><a title="Configuration" href="javascript:void(0);"><i class="fas fa-cog"></i><span>Configurations</span> <span class="menu-arrow"></span></a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li><a title="Header & Footer" href="header-footer-configuration.html"><span>Header &amp; Footer</span></a></li>
                        <li><a title="Eligibility" href="eligibility.html"><span>Eligibility</span></a></li>
                        <li><a title="Program Processing" href="program-processing-configuration.html"><span>Program Processing</span></a></li>
                    </ul>
                </li>-->

                <?php if((checkPermission(Auth::user()->role_id,'GenerateApplicationData') == 1)): ?>
                   <!-- <li class=""><a title="Generate Application Data" href="<?php echo e(url('/admin/GenerateApplicationData')); ?>"><i class="fas fa-receipt"></i><span>Generate Application Data</span></a></li>-->
                <?php endif; ?>
                <?php if((checkPermission(Auth::user()->role_id,'CustomCommunication') == 1)): ?>
                    <!--<li class=""><a title="Custom Communication" href="<?php echo e(url('/admin/CustomCommunication')); ?>"><i class="fas fa-envelope"></i><span>Custom Communication</span></a></li>-->
                <?php endif; ?>

                <?php if((checkPermission(Auth::user()->role_id,'Reports/missing/grade') == 1 || checkPermission(Auth::user()->role_id,'Reports/missing/cdi') == 1)): ?>
                    <li class=""><a title="Submissions" href="<?php echo e(url('/admin/Reports/missing')); ?>"><i class="far fa-file-alt"></i><span>Reports</span></a></li>
                <?php endif; ?>
                <?php if(checkPermission(Auth::user()->role_id,'Configuration') == 1 || Auth::user()->role_id == 2): ?>
                    <li class=""><a title="Configuration" href="javascript:void(0);"><i class="fas fa-cog"></i><span>Administration</span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <!--<li><a title="Header & Footer" href="header-footer-configuration.html"><span>Header &amp; Footer</span></a></li>
                            <li><a title="Eligibility" href="eligibility.html"><span>Eligibility</span></a></li>
                            <li><a title="Program Processing" href="program-processing-configuration.html"><span>Program Processing</span></a></li>-->
                            
                            <li class=""><a title="Process Log Report" href="<?php echo e(url('/admin/Reports/process/logs')); ?>"><span>Process Log Report</span></a></li>
                            <li class="d-none"><a title="Real Process Log Report" href="<?php echo e(url('/admin/Reports/process/real/logs')); ?>"><span>Seats Status Report</span></a></li>
                             <li class=""><a title="Front Page Links" href="<?php echo e(url('/admin/Files')); ?>"><span>Front Page Links</span></a></li>
                             <li class=""><a title="Welcome Texts" href="<?php echo e(url('/admin/Configuration')); ?>"><span>Texts</span></a></li>
                             <li class=""><a title="Audit Trails" href="<?php echo e(url('/admin/AuditTrailData')); ?>"><span>Audit Trail</span></a></li>
                             <li class=""><a title="District Configuration" href="<?php echo e(url('/admin/DistrictConfiguration')); ?>"><span>District Configuration</span></a></li>
                             <!--<li class=""><a title="Address Override" href="<?php echo e(url('/admin/ZonedSchool/overrideAddress')); ?>"><span>Address Override</span></a></li>-->
                              <?php if(Auth::user()->role_id == 1): ?>
                                <li class=""><a title="User" href="<?php echo e(url('admin/Users')); ?>"><span>Users</span></a></li>
                                <?php endif; ?>
                            <li class=""><a title="Import Gifted Students" href="<?php echo e(url('/admin/import/gifted_students')); ?>"><span>Import Gifted Students</span></a></li>
                            <li class=""><a title="Import Student Data for Program" href="<?php echo e(url('/admin/import/agt_nch')); ?>"><span>Import Student Data for Program</span></a></li>

                        </ul>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->role_id == 1): ?>
                    <li class="master"> <a title="" href="javascript:void(0);"><i class="far fa-gem"></i><span>Master</span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <?php if((checkPermission(Auth::user()->role_id,'District') == 1)): ?>
                                <?php if(Session::get("super_admin") == "Y"): ?>
                                <li><a title="District Master" href="<?php echo e(url('/admin/District')); ?>"><span>District Master</span></a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <li><a title="Program Master" href="<?php echo e(url('/admin/Program')); ?>"><span>Program Master</span></a></li>
                            <li><a title="Eligibility Master" href="<?php echo e(url('/admin/Eligibility')); ?>"><span>Eligibility Master</span></a></li>
                            <li><a title="School Master" href="<?php echo e(url('/admin/School')); ?>"><span>School Master</span></a></li>
                            <li><a title="Priority Master" href="<?php echo e(url('/admin/Priority')); ?>"><span>Priority Master</span></a></li>
                           <li><a title="Process Selection" href="javascript:void(0)"><span>Run Admin Selection</span> <span class="menu-arrow"></span></a>
                                    <ul class="nav-second-level" aria-expanded="false">
                                        <li class="pl-20"><a title="Regular Submissions" href="<?php echo e(url('/admin/Process/Selection/Reports')); ?>"><span>Regular</span></a></li>
                                        <li class="pl-20"><a title="Waitlist Submissions" href="<?php echo e(url('/admin/Waitlist/Admin/Selection')); ?>"><span>Waitlist</span></a></li>
                                        <li class="pl-20"><a title="Late Submission Master" href="<?php echo e(url('/admin/LateSubmission/Admin/Selection')); ?>"><span>Last Submission</span></a></li>

                                    </ul>
                            </li>
                            
                            <li class=""><a title="Form Master" href="<?php echo e(url('/admin/Form')); ?>"><span>Submissions Form Master</span></a></li>
                            <li><a title="Report Master" href="<?php echo e(url('/admin/Reports/Waitlist')); ?>"><span>Selection Report Master</span></a></li>
                            <li><a title="Priority Master" href="<?php echo e(url('/admin/Role')); ?>"><span>User Role Master</span></a></li>
                            <li class="d-none"><a title="Zone Address" href="<?php echo e(url('/admin/ZonedSchool')); ?>"><span>Upload School Address</span></a></li>
                            <li class=""><a title="Zone Address" href="<?php echo e(url('/admin/ConfigureExportSubmission')); ?>"><span>Export Submissions Configuration</span></a></li>
                            <li class=""><a title="Eligibility Validator" href="<?php echo e(url('/admin/EligibilityChecker')); ?>"><span>Eligibility Validator</span></a></li>



                            <!--
                            <li><a title="Form Master" href="form-master.html"><span>Form Master</span></a></li>-->
                            <!--<li><a title="Program Master" href="program-master.html"><span>Program Master</span></a></li>
                            <li><a title="User Master" href="user-master.html"><span>User Master</span></a></li>
                           -->
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left --> 
</div>