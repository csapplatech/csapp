<?php

Class appointment_controller extends CI_Controller{
   
    public function index()
	{
     $app_Times=array();
		
     $prefs = array(
        'app_Times'                 =>$app_Times,
        'show_other_days'           => TRUE,
        'show_next_prev'            => TRUE,
        'next_prev_url'             => 'http://localhost/index.php/appointment/index'
	);
         
        

    

    $User_model= new User_model;
    
    $User_model->loadPropertiesFromPrimaryKey($_SESSION['UserID']); 
    $getAdvisor=$User_model->getAdvisor();
    $getAdvisor=$getAdvisor->getUserID();
    
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
        'app_Times'                 =>$app_Times,
        'show_other_days'           => TRUE,
        'show_next_prev'            => TRUE,
        'next_prev_url'             => 'http://localhost/index.php/appointment/index'
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
            $this->load->view("appointment", $Appointment_array);             
            
        }
    }
    else if($User_model->isStudent()){      //if it is a student 
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
        'app_Times'                 =>$app_Times,
        'show_other_days'           => TRUE,
        'show_next_prev'            => TRUE,
        'next_prev_url'             => 'http://localhost/index.php/appointment/index'
	);
         
             //$app_Times=null;
            $Appointment_array=array('app_Times'=>($app_Times),'user'=>$User_model);
            
            $this->load->library('calendar',$prefs);
            
            $this->load->view("appointment_view", $Appointment_array);          //load student calendar view(which as of right now is the same as the advisor calendar view)
         }
         else{
           //tell the student that the advisor has yet to create a schedule.
         }
    }
    else{
        //You don't need to make a calendar
    }
    
    
    
    
   
  
         
           
           
           
           
}
public function fill(){
     $User_model= new User_model;              //All this reiteration is temporary until integrated with the website. in which I will use the $_SESSION data
    
    $User_model->loadPropertiesFromPrimaryKey($_SESSION['UserID']); 
    $getAdvisor=$User_model->getAdvisor();
    $getAdvisor=$getAdvisor->getUserID();
    
    $Advising_schedule= new Advising_schedule_model();
    $Advising_appointment= new Advising_appointment_model;
    
    $Advising_schedule->loadPropertiesFromAdvisorIDAndAcademicQuarterID(($getAdvisor), 1);
    
    
    
    
    if(!empty($_POST['appointments']))
    {
        // Loop to store and display values of individual checked checkbox.
        foreach($_POST['appointments'] as $selected)
        {
            //date("h-i-s-M-d-Y",$timestamp);
            $aptTime = explode ("-", $selected);
            //echo "<p>".date("h-i-s-M-d-Y",$aptTime[0])."_________".date("h-i-s-M-d-Y",$aptTime[1])."_______".$selected."</p>";
            //echo $selected;
            $Advising_appointment->setAdvisingScheduleID($Advising_schedule->getAdvisingScheduleID());
            $Advising_appointment->setStartTime($aptTime[0]);
            $Advising_appointment->setEndTime($aptTime[1]);
            $Advising_appointment->create();
            
            //echo $selected."</br>";
        }
    }
           
   
    
    redirect('appointment_controller');
 
}
}

