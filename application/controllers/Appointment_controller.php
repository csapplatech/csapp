<?php

Class appointment_controller extends CI_Controller{
   
    public function index()
	{
     $app_Times=array();
		
     $prefs = array(
        'app_Times'                 =>$app_Times,
        'show_other_days'           => TRUE,
        'show_next_prev'            => TRUE,
        'next_prev_url'             => 'http://localhost/index.php/appointment_controller/index'
	);
         
        

    

    $User_model= new User_model;
    
    $User_model->loadPropertiesFromPrimaryKey($_SESSION['UserID']);
    
    $Advising_schedule= new Advising_schedule_model();
    
    
    
    $Advising_appointment= new Advising_appointment_model;
    
    
    if($User_model->isAdvisor()){           //If it is an advisor
        if( $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID($User_model->getUserID(), 1)){ //if there are appointments registered to this info
           $Appointment_array= ($Advising_schedule->getAllAdvisingAppointments());     //retrieve all advising appointments that correspond to this advisor
            
             
            $startTime=0;
            $endTime=0;
             
           foreach ($Appointment_array as $key) //grabs each object inside array
                {
               $startTime = $key->getStartTime();
              
               
               array_push($app_Times, $startTime);
              
               
                
            }
             
            $prefs = array(
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
            
             $prefs = array(
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
		$getAdvisor=$getAdvisor->getUserID();
	
         if( $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID(($getAdvisor), 1)){  
             
             $Appointment_array= ($Advising_schedule->getAllAdvisingAppointments());
           // print_r ($Appointment_array);
             
            $startTime=0;
            $endTime=0;
             
           foreach ($Appointment_array as $key) //grabs each object inside array
                {
               $startTime = $key->getStartTime();
               
               array_push($app_Times, $startTime);
               
               
                
            }
            
            $prefs = array(
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
         else{
           redirect('Mainpage/student');
         }
    }
    else{
        //You don't need to make a calendar
    }
    
    
    
    
   
  
         
           
           
           
           
}
public function fill(){
     $User_model= new User_model;              //All this reiteration is temporary until integrated with the website. in which I will use the $_SESSION data
     $User_model->loadPropertiesFromPrimaryKey($_SESSION['UserID']); 
     
     $Advising_schedule= new Advising_schedule_model();
     $Advising_appointment= new Advising_appointment_model;
	
	if($User_model->isStudent())
	{
		$getAdvisor=$User_model->getAdvisor();
		$getAdvisor=$getAdvisor->getUserID();
                
                $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID(($getAdvisor), 1); //load the scedule that corresponds to the students advisor and the acedemic quarter
                $all_Appointments=$Advising_schedule->getAllAdvisingAppointments();
            if(($_POST['student_selection']))
            {
                
                $aptTime = explode ("-", $_POST['student_selection']); //separate the start and end times
                
                foreach($all_Appointments as $selected) // Loop to store and display values of individual checked checkbox.
                {
                    if(($selected->getStartTime()==$aptTime[0])&& $selected->isOpen())
                     {
                        $Advising_appointment->loadPropertiesFromPrimaryKey($selected->getAdvisingAppointmentID());//load the specific appointment from the ID
                        $Advising_appointment->setStartTime($aptTime[0]);//push start time to the database
                        $Advising_appointment->setEndTime($aptTime[1]);//push the end time to the database
                        $Advising_appointment->setStudentUserID($_SESSION['UserID']);//set the scheduled student user ID
                        $Advising_appointment->setAdvisingAppointmentState(1);
                        $Advising_appointment->update(); //update the advising appointment with above information it is now marked as shceduled
                     }
                    
                    

            
        }
    }
        }
	else if($User_model->isAdvisor())
	{
		$getAdvisor = $User_model->getUserID();
                
                $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID(($getAdvisor), 1); //load the schedule that corresponds to this advisor and this academic quarter
	
                 if(!empty($_POST['appointments']))
            {
                foreach($_POST['appointments'] as $selected) // Loop to store and display values of individual checked checkbox.
                {
                    $aptTime = explode ("-", $selected); //separate the start and end times
                    
                    $Advising_appointment->setAdvisingScheduleID($Advising_schedule->getAdvisingScheduleID());
                    $Advising_appointment->setStudentUserID($_SESSION['UserID']);//set the scheduled student user ID to the advisors ID
                    $Advising_appointment->setAdvisingAppointmentState(5);
                    $Advising_appointment->setStartTime($aptTime[0]);//push start time to the database
                    $Advising_appointment->setEndTime($aptTime[1]);//push the end time to the database
                    $Advising_appointment->create(); //create the advising appointment with above information

            //echo $selected."</br>";
        }
    }
        }
    
    
    
    
    
    
   
           
   
    
    redirect('appointment_controller');
 
}
}

