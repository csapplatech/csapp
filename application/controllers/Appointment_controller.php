<?php

Class appointment_controller extends CI_Controller{
   
    public function index()
	{
    $app_Times=array();
       
    $User_model= new User_model;
    
    $User_model->loadPropertiesFromPrimaryKey($_SESSION['UserID']);
    
    $Advising_schedule= new Advising_schedule_model;
    
    $Advising_appointment= new Advising_appointment_model;
    
    $quarter = Academic_quarter_model::getLatestAcademicQuarter();
        $quarter=$quarter->getAcademicQuarterID();
    
    
    if($User_model->isAdvisor()){           //If it is an advisor
        
        
        
        if( $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID($User_model->getUserID(), $quarter)){ //if there are appointments registered to this info
           $All_apps= ($Advising_schedule->getAllAdvisingAppointments());     //retrieve all advising appointments that correspond to this advisor
            $All_Advisees=($User_model->getAdvisees());
             
            $startTime=0;
            
             
           foreach ($All_apps as $key) //grabs each object inside array
                {
               $startTime = $key->getStartTime();
               array_push($app_Times, $startTime);
               }
            
             
             
             
             
            $prefs = array(
                'all_advisees'              =>$All_Advisees,
                'all_apps'                  =>$All_apps,
                'user'                      =>$User_model,
                'app_Times'                 =>$app_Times,
                'show_other_days'           => TRUE,
                'show_next_prev'            => TRUE,
                'next_prev_url'             => 'http://localhost/index.php/appointment_controller/index'
                );
         
            
             $Appointment_array=array('app_Times'=>($app_Times),'user'=>$User_model);
            
            $this->load->library('calendar',$prefs);
            
            $this->load->view("appointment_view", $Appointment_array);      //will load a blank calendar to be edited
        }
        
        else{                                                          //if there were no appointments found
            $app_Times=null;                                        //null app_Times array
            $Appointment_array=array('app_Times'=>($app_Times),'user'=>$User_model);
            $Advising_schedule->setAdvisorUserID($User_model->getUserID());   //use this to create a new advising shedule
            $Advising_schedule->setAcademicQuarterID(1);                      //use this to create a new advising schedule
            $Advising_schedule->create();                                     //CREATE the new advising schedule
            $All_Advisees=($User_model->getAdvisees());
            
            $All_apps= ($Advising_schedule->getAllAdvisingAppointments()); //for the sake o defining all_apps. It will be null
            
             $prefs = array(
                'all_advisees'              =>$All_Advisees,
                'all_apps'                  =>$All_apps,
                'user'                      =>$User_model,
                'app_Times'                 =>$app_Times,
                'show_other_days'           => TRUE,
                'show_next_prev'            => TRUE,
                'next_prev_url'             => 'http://localhost/index.php/appointment_controller/index'
                );
            $this->load->library('calendar',$prefs);
            $this->load->view("appointment_view", $Appointment_array);             

        }
    }
    else if($User_model->isStudent()){      //if it is a student 
		
		$getAdvisor=$User_model->getAdvisor();
		
	//print_r ($getAdvisor);
         if( $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID(($getAdvisor->getUserID()), $quarter))
         {  
             
             $All_apps= ($Advising_schedule->getAllAdvisingAppointments());
           // print_r ($Appointment_array);
             
            $startTime=0;
            $endTime=0;
             
           foreach ($All_apps as $key) //grabs each object inside array
            {
               $startTime = $key->getStartTime();
               
               array_push($app_Times, $startTime);
               
               
                
            }

                    $prefs = array(
                'all_apps'                  =>$All_apps,
                'user'                      =>$User_model,
                'app_Times'                 =>$app_Times,
                'show_other_days'           => TRUE,
                'show_next_prev'            => TRUE,
                'next_prev_url'             => 'http://localhost/index.php/appointment_controller/index'
                );
         
             //$app_Times=null;
            $Appointment_array=array('app_Times'=>($app_Times),'user'=>$User_model);
            
            $this->load->library('calendar',$prefs);
            
            $this->load->view("appointment_view", $Appointment_array);          //load student calendar view(which as of right now is the same as the advisor calendar view)
         }
         
         else
         {
             redirect('Mainpage/student');
         }
    }
   
    
    }

    
    
    
    
    
public function fill()
    {
     $User_model= new User_model;              //All this reiteration is temporary until integrated with the website. in which I will use the $_SESSION data
     $User_model->loadPropertiesFromPrimaryKey($_SESSION['UserID']); 
     
     $Advising_schedule= new Advising_schedule_model();
     $Advising_appointment= new Advising_appointment_model;
     
      $quarter = Academic_quarter_model::getLatestAcademicQuarter();
        $quarter=$quarter->getAcademicQuarterID();
	
	if($User_model->isStudent())
	{
		$getAdvisor=$User_model->getAdvisor();
		$getAdvisor=$getAdvisor->getUserID();
                
                $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID(($getAdvisor), $quarter); //load the scedule that corresponds to the students advisor and the acedemic quarter
                $all_Appointments=$Advising_schedule->getAllAdvisingAppointments();
            if((!empty($_POST['student_selection']))) //if a student scheduled an appointment
            {
                  
                foreach($all_Appointments as $selected) // Loop to store and display values of individual checked checkbox.
                {
                   
                    if(($selected->getScheduledStudentUserID() == $_SESSION['UserID'])&&($selected->isScheduled())) //doesn't let the student take another students slot
                     {
                      
                        $_POST['student_selection']=0;
                     }
                     
                }
                $aptTime = explode ("-", $_POST['student_selection']); //separate the start and end times
                
                foreach($all_Appointments as $selected) // Loop to store and display values of individual checked checkbox.
                {
                  
                    if((($selected->getStartTime()==$aptTime[0]) && !($selected->isScheduled())))
                     { 
                        $Advising_appointment->loadPropertiesFromPrimaryKey($selected->getAdvisingAppointmentID());//load the specific appointment from the ID
                        $Advising_appointment->setStudentUserID($_SESSION['UserID']);//set the scheduled student user ID
                        $Advising_appointment->setAdvisingAppointmentState(1);
                        $Advising_appointment->update(); //update the advising appointment with above information it is now marked as shceduled
                     }
                     
                }
            }
            
            elseif(!empty($_POST['My_Schedule']))
            {
                
                redirect('Appointment_controller/Student_Cancel');
            }
        }
	else if($User_model->isAdvisor())
	{
            $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID(($User_model->getUserID()), $quarter); //load the schedule that corresponds to this advisor and this academic quarter
            $all_Appointments=$Advising_schedule->getAllAdvisingAppointments(); 
           
                if(!empty($_POST['appointments']))// this will handle the cells that the advisor marked as available
                {
                    foreach($_POST['appointments'] as $selected) // Loop to store and display values of individual checked checkbox.
                    {
                        $aptTime = explode ("-", $selected); //separate the start and end times
                        
                        $Advising_appointment->setAdvisingScheduleID($Advising_schedule->getAdvisingScheduleID());
                        $Advising_appointment->setStartTime($aptTime[0]);//push start time to the database
                        $Advising_appointment->setEndTime($aptTime[1]);//push the end time to the database
                        $Advising_appointment->create(); //create the advising appointment with above information
                    }
                }
                
                if(!empty($_POST['Open']))//if this array is not empty then the advisor has removed an unscheduled, available office hour
                {
                    foreach($_POST['Open'] as $open)
                    {
                        $aptTime = explode ("-", $open); //separate the start and end times
                        foreach($all_Appointments as $selected)
                        {
                            if($selected->getStartTime()==$aptTime[0])
                            {
                                $selected->loadPropertiesFromPrimaryKey($selected->getAdvisingAppointmentID());
                                $selected->delete();
                            }
                        }
                    }
                
                }
                
                if(!empty($_POST['student_scheduled']))//if this array is not empty, then the advisor has canceled an appointment
                {
                    foreach($_POST['student_scheduled'] as $slot)
                    {
                        $aptTime = explode ("-", $slot); //separate the start and end times
                        foreach($all_Appointments as $selected)
                        {
                            if($selected->getStartTime()==$aptTime[0])
                            {
                                $selected->setAdvisingAppointmentID($selected->getAdvisingAppointmentID());
                                $selected->setAdvisingAppointmentState(4);
                                $selected->update();
                                //EMAIL THE STUDENT THAT THE APPOINTMENT WAS CANCELED 
                            }
                        }
                    }
                }
        }
    
   redirect('appointment_controller');
    }

    
    
public function Student_Cancel()
    {
    $User_model= new User_model;              
    $User_model->loadPropertiesFromPrimaryKey($_SESSION['UserID']); 
    
     $quarter = Academic_quarter_model::getLatestAcademicQuarter();
        $quarter=$quarter->getAcademicQuarterID();

    $Advising_schedule= new Advising_schedule_model();
    $Advising_appointment= new Advising_appointment_model;
    $advisor=$User_model->getAdvisor();
    $advisor=$advisor->getUserID();
    $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID(($advisor), $quarter);
    
    $app_array=$Advising_schedule->getAllAdvisingAppointments();
    
    foreach ($app_array as $key)
    {
        if($key->getScheduledStudentUserID()==$_SESSION['UserID']&& $key->isScheduled())
        {
          
            $Advising_appointment->loadPropertiesFromPrimaryKey($key->getAdvisingAppointmentID());
            $Advising_appointment->setAdvisingAppointmentState(3);
            $Advising_appointment->update();
            break;
        }
    }
    
    
    //SEND Optional Email
    
    redirect('appointment_controller');
    }    
}                
          
                
               
        

