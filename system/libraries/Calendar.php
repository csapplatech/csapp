<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Calendar Class
 *
 * This class enables the creation of calendars
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/calendar.html
 */
class CI_Calendar {

	/**
	 * Calendar layout template
	 *
	 * @var mixed
	 */
        
         public $Scheduled_Info=array();
    
        public $Unscheduled_Students=array();
        
        public $Scheduled_Students=array();
    
        public $all_advisees='';
        
        public $all_apps='';
    
	public $template = '';
        
        public $user='';

	/**
	 * Replacements array for template
	 *
	 * @var array
	 */
	public $replacements = array();

	/**
	 * Day of the week to start the calendar on
	 *
	 * @var string
	 */
	public $start_day = 'sunday';

	/**
	 * How to display months
	 *
	 * @var string
	 */
	public $month_type = 'long';

	/**
	 * How to display names of days
	 *
	 * @var string
	 */
	public $day_type = 'abr';

	/**
	 * Whether to show next/prev month links
	 *
	 * @var bool
	 */
	public $show_next_prev = FALSE;

	/**
	 * Url base to use for next/prev month links
	 *
	 * @var bool
	 */
	public $next_prev_url = '';

	/**
	 * Show days of other months
	 *
	 * @var bool
	 */
	public $show_other_days = FALSE;
        
        public  $app_Times=array();
        
        public $interval = 30;

	// --------------------------------------------------------------------

	/**
	 * CI Singleton
	 *
	 * @var object
	 */
	protected $CI;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * Loads the calendar language file and sets the default time reference.
	 *
	 * @uses	CI_Lang::$is_loaded
	 *
	 * @param	array	$config	Calendar options
	 * @return	void
	 */
	public function __construct($config = array())
	{
		$this->CI =& get_instance();

		if ( ! in_array('calendar_lang.php', $this->CI->lang->is_loaded, TRUE))
		{
			$this->CI->lang->load('calendar');
		}

		empty($config) OR $this->initialize($config);

		log_message('info', 'Calendar Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the user preferences
	 *
	 * Accepts an associative array as input, containing display preferences
	 *
	 * @param	array	config preferences
	 * @return	CI_Calendar
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

		// Set the next_prev_url to the controller if required but not defined
		if ($this->show_next_prev === TRUE && empty($this->next_prev_url))
		{
			$this->next_prev_url = $this->CI->config->site_url($this->CI->router->class.'/'.$this->CI->router->method);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the calendar
	 *
	 * @param	int	the year
	 * @param	int	the month
	 * @param	array	the data to be shown in the calendar cells
	 * @return	string
	 */
        
	public function generate($year = '', $month = '', $data = array(),$interval =20)
        {       
            
            
                if((intval($interval)== 10)||(intval($interval)== 15)||(intval($interval)== 20)||(intval($interval)== 30)) //if input is wrong
                {
                    $interval = $interval;
                }
                else
                {
                    $interval = 20;
                }
                $starthour = '7:00 am'; //start time in string format
                $endhour = '5:00 pm';  //end time time in string format
                $sstarthour = 7; //start time in int format 8:00 am
                $eendhour = 5; //starttime in int format 5:00 pm
                $totalhours = 10; //total number of hours between $sstarttime and $eendtime
                $increment = $interval; //appointment time limits (10, 15, 20, 30)
                $incrementratio = 60/$increment;
                $tempk = 0;
                //////////////////////                      
                $time = 0; 
                $startTime = 0;
                $endTime = 0;
                $formatted_increment = 0;
                //////////////////////
                $thour = 0; //hour var for timestamp
                $tminutes = 0; //hour var for timestamp
                $tseconds = 0; //hour var for timestamp
                $tmonth = 0; //hour var for timestamp
                $tday = 0; //hour var for timestamp
                $tyear = 0; //hour var for timestamp
                $tcurrmonth = 0;
                $tprevmonth = 0;
                $tnextmonth =0;
                //////////////
                $myday =array (); //captures the days of the month being produced
                $whichWeek =0; //determines which week is being processed for $myday (usually 1 week - 5 weeks)
                $lastWeek = 5; //determines the last week of the month
                $timestamp1 = 0;
                $timestamp2 = 0;
                $tablenum = 0;
                $eo = "class='tableodd'";
                $switch = 0;
                $actualdate =0; //goes in the title attribute for each appointment;
                $sidebar = ''; //box of data on side of calendar
               
		$local_time = time();

		// Set and validate the supplied month/year
		if (empty($year))
		{
			$year = date('Y', $local_time);
		}
		elseif (strlen($year) === 1)
		{
			$year = '200'.$year;
		}
		elseif (strlen($year) === 2)
		{
			$year = '20'.$year;
		}

		if (empty($month))
		{
			$month = date('m', $local_time);
		}
		elseif (strlen($month) === 1)
		{
			$month = '0'.$month;
		}

		$adjusted_date = $this->adjust_date($month, $year);

		$month	= $adjusted_date['month'];
		$year	= $adjusted_date['year'];

		// Determine the total days in the month
		$total_days = $this->get_total_days($month, $year);

		// Set the starting day of the week
		$start_days	= array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
		$start_day	= isset($start_days[$this->start_day]) ? $start_days[$this->start_day] : 0;

		// Set the starting day number
		$local_date = mktime(12, 0, 0, $month, 1, $year);
		$date = getdate($local_date);
		$day  = $start_day + 1 - $date['wday'];

		while ($day > 1)
		{
			$day -= 7;
		}

		// Set the current month/year/day
		// We use this to determine the "today" date
		$cur_year	= date('Y', $local_time);
		$cur_month	= date('m', $local_time);
		$cur_day	= date('j', $local_time);

		$is_current_month = ($cur_year == $year && $cur_month == $month);

		// Generate the template data array
		$this->parse_template();

		// Begin building the calendar output
		$out = $this->replacements['table_open']."\n\n".$this->replacements['heading_row_start']."\n";

		// "previous" month link
		if ($this->show_next_prev === TRUE)
		{
			// Add a trailing slash to the URL if needed
			$this->next_prev_url = preg_replace('/(.+?)\/*$/', '\\1/', $this->next_prev_url);

			$adjusted_date = $this->adjust_date($month - 1, $year);
			$out .= str_replace('{previous_url}', $this->next_prev_url.$adjusted_date['year'].'/'.$adjusted_date['month'], $this->replacements['heading_previous_cell'])."\n";
		}

		// Heading containing the month/year
		$colspan = ($this->show_next_prev === TRUE) ? 5 : 7;

		$this->replacements['heading_title_cell'] = str_replace('{colspan}', $colspan,
								str_replace('{heading}', $this->get_month_name($month).'&nbsp;'.$year, $this->replacements['heading_title_cell']));

		$out .= $this->replacements['heading_title_cell']."";

		// "next" month link
		if ($this->show_next_prev === TRUE)
		{
			$adjusted_date = $this->adjust_date($month + 1, $year);
			$out .= str_replace('{next_url}', $this->next_prev_url.$adjusted_date['year'].'/'.$adjusted_date['month'], $this->replacements['heading_next_cell']);
		}

		$out .= "\n".$this->replacements['heading_row_end'].""
			// Write the cells containing the days of the week
			.$this->replacements['week_row_start']."";

		$day_names = $this->get_day_names();

		$out .= "\n".$this->replacements['week_row_end']."<div id='weekwrapper'>";//throws in wrapper around tables
                $tprevmonth = $this->adjust_date($month-1, $tyear);
                $tnextmonth = $this->adjust_date($month+1, $tyear);
                $tcurrmonth = $month[0]+$month[1]; //current month in number format
                $tprevmonth = $tprevmonth['month']; //previous month in number format
                $tnextmonth = $tnextmonth['month']; //next month in number format
                $tmonth = $tprevmonth;
		//echo $tprevmonth." ".$tcurrmonth." ".$tnextmonth;
                // Build the main body of the calendar
		while ($day <= $total_days) //once get to last day, exit
		{       
			$out .= "\n".$this->replacements['cal_row_start'];
                        $out .= "id= 'table$tablenum'><thead><tr><th id='col'></th><th id='col0'></th>";
                        
                        for ($i = 0; $i < 7; $i ++) //print out each day of the week as one row
                        {
                                $aa = $i +1;
                                $out .= "<th id='col$aa'>";
                                $out .= str_replace('{week_day}', $day_names[($start_day + $i) %7], $this->replacements['week_day_cell']);
                                $out .= "</th>";
                        }
                        $out .="</tr><tr><th id='col'></th><th id='col0'></th>"; //adds a column to the second row of each table 
			for ($i = 0; $i < 7; $i++) // adds a week
			{
				if ($day > 0 && $day <= $total_days) // if days of the month
				{
					$out .= ($is_current_month === TRUE && $day == $cur_day) ? $this->replacements['cal_cell_start_today'] : $this->replacements['cal_cell_start'];
                                        $kk = $i +1;
                                        $out .= "id='col$kk'>";
					if (isset($data[$day]))
					{
						// Cells with content
						$temp = ($is_current_month === TRUE && $day == $cur_day) ?
								$this->replacements['cal_cell_content_today'] : $this->replacements['cal_cell_content'];
						$out .= str_replace(array('{content}', '{day}'), array($data[$day], $day), $temp);
					}
					else
					{
						// Cells with no content
						$temp = ($is_current_month === TRUE && $day == $cur_day) ?
								$this->replacements['cal_cell_no_content_today'] : $this->replacements['cal_cell_no_content'];
						$out .= str_replace('{day}', $day, $temp);
                                                $myday []=str_replace('{day}', $day, $temp);
					}
					$out .= ($is_current_month === TRUE && $day == $cur_day) ? $this->replacements['cal_cell_end_today'] : $this->replacements['cal_cell_end'];
				}
				elseif ($this->show_other_days === TRUE) //show other days of other months
				{
					$out .= $this->replacements['cal_cell_start_other'];
                                        $hh = $i +1;
                                        $out .= "id='col$hh'>";
					if ($day <= 0)
					{
						// Day of previous month
						$prev_month = $this->adjust_date($month - 1, $year);
						$prev_month_days = $this->get_total_days($prev_month['month'], $prev_month['year']);
						$out .= str_replace('{day}', $prev_month_days + $day, $this->replacements['cal_cell_other']);
                                                $myday []= str_replace('{day}', $prev_month_days + $day, $this->replacements['cal_cell_other']);
					}
					else
					{
						// Day of next month
						$out .= str_replace('{day}', $day - $total_days, $this->replacements['cal_cell_other']);
                                                $myday []= str_replace('{day}', $day - $total_days, $this->replacements['cal_cell_other']);
					}
					$out .= $this->replacements['cal_cell_end_other'];
                                        
				}
				else
				{
					// Blank cells
					$out .= $this->replacements['cal_cell_start'].$this->replacements['cal_cell_blank'].$this->replacements['cal_cell_end'];
				}
				$day++; // increments day 7 times then repeats
                                
			}
                        $out .= "</tr></thead><tbody>";
                        $time = strtotime("$starthour"); //convert starting hour to appropriate format
                        $startTime = date("H:i", strtotime('-00 minutes', $time)); //initializes the start time appropriately
                        $hourTime = 0;
                        $shourTime = 0;
                        $ehourtime = 0;
                        $formatted_increment = $increment; //initializes the increment time
                        $whichWeek = sizeof($myday)/7;
                        
                        ////////////////////////////////
                        //date("h-i-s-M-d-Y",mktime($thour,$tminutes,$tseconds,$tmonth,$tday,$tyear)); //creates date from timestamp
                        //mktime($thour,$tminutes,$tseconds,$tmonth,$tday,$tyear) //makes timestamp
                        for ($k = 0; $k < ($totalhours*$incrementratio); $k++ ) //creates the proper number of rows of cells (appointments) based off of interval and start/end time.
                        {
                            $endTime = date("H:i", strtotime("+$formatted_increment minutes", $time)); //adds 30 minutes from the vairiable time
                            $regStartTime = date('h:i', strtotime($startTime)); //changes from militarty time to regular time
                            $regEndTime = date('h:i', strtotime($endTime)); //changes from military time to regular time
                            $shourTime = explode(':', $regStartTime); //splits start time into an array of size 2
                            $ehourTime = explode(':', $regEndTime); //splits end time into an array of size 2
                            $out .= "<tr>";//first column of every row
                            ($k%2 == 0)? $eo = "class='tableeven'" : $eo = "class='tableodd'";
                            if(($interval == 10)||($interval == 15)||($interval == 20)||($interval == 30)) //determines the "huge number" based on interval
                            {
                                if($interval == 10) //if interval = 10
                                {
                                   if($k%6 == 0) //if $k is at the beginning hour of each time interval output a new column 
                                       $out .= "<td rowspan='6' id='col'>$shourTime[0]</td>";
                                }
                                elseif($interval == 15) //if interval = 15
                                {
                                    if($k%4 == 0)//if $k is at the beginning hour of each time interval output a new column 
                                       $out .= "<td rowspan='4' id='col'>$shourTime[0]</td>";
                                }
                                elseif($interval == 20) //if interval = 25
                                {
                                    if($k%3 == 0)//if $k is at the beginning hour of each time interval output a new column 
                                       $out .= "<td rowspan='3' id='col'>$shourTime[0]</td>";
                                }
                                elseif($interval == 30) //if interval = 30
                                {
                                    if($k%2 == 0)//if $k is at the beginning hour of each time interval output a new column 
                                       $out .= "<td rowspan='2' id='col'>$shourTime[0]</td>";
                                }
                                else{}//if not interval, do nothing
                            }
                            if(($interval == 10)||($interval == 15)||($interval == 20)||($interval == 30)) //determines the "ending interval" based on interval
                            {
                                if($interval == 10) //if interval = 10
                                {
                                   if((($k+1)%6)== 0) //if $k is at the end hour of each time interval output a new column 
                                       $out .= "<td id='col0' $eo>:$shourTime[1] - <span style='font-weight:bold;color: #e4481b;'>$ehourTime[0]</span></td>"; //second column of every row has start and end time
                                   else 
                                       $out .= "<td id='col0' $eo>:$shourTime[1] - :$ehourTime[1] </td>"; //second column of every row has start and end time
                                }
                                elseif($interval == 15) //if interval = 15
                                {
                                   if((($k+1)%4)== 0) //if $k is at the end hour of each time interval output a new column 
                                       $out .= "<td id='col0' $eo>:$shourTime[1] - <span style='font-weight:bold;color: #e4481b;'>$ehourTime[0]</span></td>"; //second column of every row has start and end time
                                   else 
                                       $out .= "<td id='col0' $eo>:$shourTime[1] - :$ehourTime[1] </td>"; //second column of every row has start and end time
                                }
                                elseif($interval == 20) //if interval = 25
                                {
                                   if((($k+1)%3)== 0) //if $k is at the end hour of each time interval output a new column 
                                       $out .= "<td id='col0' $eo>:$shourTime[1] - <span style='font-weight:bold;color: #e4481b;'>$ehourTime[0]</span></td>"; //second column of every row has start and end time
                                   else 
                                       $out .= "<td id='col0' $eo>:$shourTime[1] - :$ehourTime[1] </td>"; //second column of every row has start and end time
                                }
                                elseif($interval == 30) //if interval = 30
                                {
                                   if((($k+1)%2)== 0) //if $k is at the end hour of each time interval output a new column 
                                       $out .= "<td id='col0' $eo>:$shourTime[1] - <span style='font-weight:bold;color: #e4481b;'>$ehourTime[0]</span></td>"; //second column of every row has start and end time
                                   else 
                                       $out .= "<td id='col0' $eo>:$shourTime[1] - :$ehourTime[1] </td>"; //second column of every row has start and end time
                                }
                                else{}//if not interval, do nothing
                            }
                            for($l = 0; $l < 7; $l++) //7 columns
                            {
                                //$switch = 0;
                                $tempk++;
                                $jj = $l+1;
                                $correctDay = (7*($whichWeek-1))+$l; //calculates the correct day of the stored month based on the correct week
                                $theday = intval(strip_tags($myday[$correctDay], '<strong style="color: #e4481b;;"></strong>')); //removes unwanted html from around int
                                $hourTime = explode(':', $startTime);
                                
                                $thour = $hourTime[0]; //hour var for timestamp
                                $tminutes = $hourTime[1]; //minutes var for timestamp
                                $tseconds = 00; //seconds var for timestamp
                                $tday =$theday; //day var for timestamp
 
                                /////////////SWITCHES TO MONTH BEFORE(DURING FIRST WEEK OR LAST) FOR EACH ROW/////////////
                                if(($whichWeek==1)&&($switch==0)) //when switch == 0 in the beginning of month switch month to previous month
                                {
                                    $tmonth = $tprevmonth;
                                }
                                else if (($whichWeek==$lastWeek)&&($switch==1))//last week of the month
                                {
                                    $tmonth = $tcurrmonth;
                                }
                                else{}
                                ////////////////SWTICHES TO NEXT MONTH WHEN THE FIRST OF THE MONTH(FIRST WEEK OR LAST) FOR EACH ROW///////////////////////////
                                /*
                                 * this chart helps determine the last week, based on the 1st (first week, not last)
                                 *                    [ Sun;Mon ;Tue ;Wed ;Thurs;Fri;Sat;]
                                 *    28days    1st:  [4wks,5wks;5wks;5wks;5wks;5wks;5wks]
                                 *    29days    1st:  [5wks;5wks;5wks;5wks;5wks;5wks;5wks]
                                 *    30days    1st:  [5wks;5wks;5wks;5wks;5wks;5wks;6wks;]
                                 *    31days    1st:  [5wks;5wks;5wks;5wks;5wks;6wks;6wks;]
                                 */
                                if(($theday == '01')&&($total_days == 28)) //switch to current month from previous month && set switch to 1
                                {
                                    //$switch++;
                                    //$tmonth = $tcurrmonth;
                                    if($whichWeek == 1) //1st week  
                                    {
                                        $tmonth = $tcurrmonth;
                                        $switch = 1;
                                        if($l==0)//if the 1st of the month appears on sunday, the last week is 4
                                        {
                                            $lastWeek = 4;
                                        }
                                        else //if 1st of the month appears on any other day of the week, the last week is 5
                                        {
                                            $lastWeek = 5;
                                        }
                                    }
                                    else if($whichWeek == $lastWeek) //last week
                                    {
                                        $tmonth = $tnextmonth;
                                        $switch = 0;
                                    }
                                    else{}
                                }
                                else if (($theday == '01')&&($total_days == 29))
                                {
                                    if($whichWeek == 1) //1st week  
                                    {
                                        $tmonth = $tcurrmonth;
                                        $switch = 1;
                                        $lastWeek = 5;
                                    }
                                    else if($whichWeek == $lastWeek) //last week
                                    {
                                        $tmonth = $tnextmonth;
                                        $switch = 0;
                                    }
                                    else{}
                                }
                                else if (($theday == '01')&&($total_days == 30)) //switch to next month from current month
                                {
                                    if($whichWeek == 1) //1st week  
                                    {
                                        $tmonth = $tcurrmonth;
                                        $switch = 1;
                                        if($l==6) //if the 1st appears on saturday, the last week is 6
                                        {
                                            $lastWeek = 6;
                                        }
                                        else //if 1st of the month appears on any other day of the week, the last week is 5
                                        {
                                            $lastWeek = 5;
                                        }
                                    }
                                    else if($whichWeek == $lastWeek) //last week
                                    {
                                        $tmonth = $tnextmonth;
                                        $switch = 0;
                                    }
                                    else {}
                                }
                                else if (($theday == '01')&&($total_days == 31))
                                {
                                    if($whichWeek == 1) //1st week  
                                    {
                                        $tmonth = $tcurrmonth;
                                        $switch = 1;
                                        if($l ==6) //if the 1st appears on saturday, the last week is 6
                                        {
                                            $lastWeek = 6;
                                        }
                                        else if($l == 5) //if the 1st appears on friday, the last week is 6
                                        {
                                            $lastWeek = 6;
                                        }
                                        else //if 1st of the month appears on any other day of the week, the last week is 5
                                        {
                                            $lastWeek = 5;
                                        }
                                    }
                                    else if($whichWeek == $lastWeek) //last week
                                    {
                                        $tmonth = $tnextmonth;
                                        $switch = 0;
                                    }
                                    else {}   
                                }
                                else {}
                                
                                $tyear = $year; //year var for timestamp
                                $timestamp1 = mktime($thour,$tminutes,$tseconds,$tmonth,$tday,$tyear);
                                $hourTime = explode(':', $endTime);
                                $thour = $hourTime[0]; //hour var for timestamp
                                $tminutes = $hourTime[1]; //minutes var for timestamp     
                                $timestamp2 = mktime($thour,$tminutes,$tseconds,$tmonth,$tday,$tyear);
                                
                                $actualdate = "(".date("h:i",$timestamp1)." - ".date("h:i",$timestamp2).") ".date("M d,Y",$timestamp2);
                                
                                $existing_Appointment=false; 
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
            if($this->user->isStudent())
            {
                foreach($this->all_apps as $key)
                   {
                        if($key->getStartTime()==$timestamp1)
                        {

                           if($key->isScheduled())  //if a student has already picked this time slot
                               {
                                if($key->getScheduledStudentUserID()==$_SESSION['UserID']){
                                    $out .= "<td id='clickable'><div class='My_Schedule'><input type='checkbox' disabled id='$tempk' class='row$k' name='won't_be_posted value='$timestamp1-$timestamp2' ><label title = '$actualdate' for='$tempk' id='$tempk-'</label></div></td> ";
                                    $existing_Appointment=true;
                                     break;
                                    }
                                $out .= "<td id='clickable'><div class='Scheduled'><input type='checkbox' disabled id='$tempk' class='row$k' name='won't_be_posted value='$timestamp1-$timestamp2' ><label title = '$actualdate' for='$tempk' id='$tempk-'</label></div></td> ";
                                $existing_Appointment=true;
                                break;

                               } 

                           else
                               {
                                $out .= "<td id='clickable'><div class='Open'><input type='checkbox' id='$tempk' class='row$k' name='student_selection' value='$timestamp1-$timestamp2' ><label title = '$actualdate' for='$tempk' id='$tempk-'</label></div></td> "; //creates a row of $l columns $k times
                                $existing_Appointment=true;
                                break;
                               }
                        }
                    }

                if($existing_Appointment==false)
                    {
                    $out .= "<td id='clickable'><div class='cboxwrapper'><input type='checkbox' disabled id='$tempk' class='row$k' name='appointments[]' value='$timestamp1-$timestamp2'><label title = '$actualdate' for='$tempk' id='$tempk-'></label></div></td>"; //creates a row of $l columns $k times
                    }
            }
        else if($this->user->isAdvisor())
            {
                    if($this->app_Times !=null)
                {  
                    foreach($this->all_apps as $key)
                        {
                            if($key->getStartTime()==$timestamp1)
                                {
                                    if($key->isScheduled())  //if a student has already picked this time slot
                                {
                                    $this->user->loadPropertiesFromPrimaryKey($key->getScheduledStudentUserID());
                                    $student_Name=$this->user->getName();
                                    $student_ID=$this->user->getUserID();
                                    array_push($this->Scheduled_Students, $student_ID);
                                    array_push($this->Scheduled_Info, $student_Name."-".$timestamp1."-".$timestamp2);
                                    $out .= "<td id='clickable'><div class='Scheduled'><input type='checkbox' id='$tempk' class='row$k' name='student_scheduled[]' value='$timestamp1-$timestamp2' ><label title = '".$student_Name."' for='$tempk' id='$tempk-'</label></div></td> ";
                                    $existing_Appointment=true;
                                    $this->user->loadPropertiesFromPrimaryKey($_SESSION['UserID']);
                                    break;

                               } 

                           else   //if not scheduled load the cell that the advisor marked as open
                               {
                                $out .= "<td id='clickable'><div class='Open'><input type='checkbox' id='$tempk' class='row$k' name='Open[]' value='$timestamp1-$timestamp2' ><label title = '$actualdate' for='$tempk' id='$tempk-'</label></div></td> "; //creates a row of $l columns $k times
                                $existing_Appointment=true;
                                break;
                               }
                                }

                        }

                }
             if($existing_Appointment==false)
                {
                 $out .= "<td id='clickable'><div class='cboxwrapper'><input type='checkbox' id='$tempk' class='row$k' name='appointments[]' value='$timestamp1-$timestamp2'><label title = '$actualdate' for='$tempk' id='$tempk-' onmouseover='selectAll(event, this)'></label></div></td>"; //creates a row of $l columns $k times
                }
            }

                   
                         
                         
                                 
                              
 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////                               
                                // $out .= "<td id='clickable'><div class='cboxwrapper'><input type='checkbox' id='$tempk' class='row$k' name='appointments[]' value='$timestamp1-$timestamp2'><label title = '$actualdate' for='$tempk' id='$tempk-' onmouseover='selectAll(event, this)'></label></div></td>"; //creates a row of $l columns $k times
                                if(($l == 6)&& ($whichWeek==1)) //switches back to 0 at the end of the week
                                {
                                    $switch = 0;
                                }
                                else if (($l == 6)&& ($whichWeek==$lastWeek))
                                {
                                    $switch = 1;
                                }
                                else{}
                            }
                            $out .= "</tr>";
                            $startTime = $endTime; //creates start time for next appointment 
                            $formatted_increment += $increment; //keeps track of the overall time based on the interval
                        }
			$out .= "\n".$this->replacements['cal_row_end']."\n"; //end of the week, but also end of individual week table
                        $tablenum++;
		}       
                
                
            if($this->user->isAdvisor())
            {  
               $X=0;
                foreach($this->all_advisees as $key) 
                {   
                    if(!empty($this->Scheduled_Students))//if at least one person is scheduled
                    {
                        foreach($this->Scheduled_Students as $SS)
                        { 
                            $this->user->loadPropertiesFromPrimaryKey($SS);  
                            if($key->getUserID() == $this->user->getUserID()) //if statement that removes scheduled students from array
                            {        
                                array_splice($this->all_advisees,$X, 1); //if $SS userID == $key UserID then remove $key from $all_advisees and smush the array back together
                            }
                        }
                    }
                    $X++;
                }
                foreach($this->all_advisees as $key) //stores unscheduled students in the array Unscheduled_Students
                {
                    array_push($this->Unscheduled_Students,$key);
                }
                $this->user->loadPropertiesFromPrimaryKey($_SESSION['UserID']);  
                
                //ONCE THE ADVISOR'S STUDENTS ARE SORTED IN THEIR PROPER ARRAYS, CREATE SIDE BAR
                $sidebar .="
                    <div id='tabs-container'>
                        <ul class='tabs-menu'>
                            <li class='current'><a href='#tab-1'>Scheduled</a></li>
                            <li><a href='#tab-2'>Unscheduled</a></li>
                            <li><a href='#tab-3'>Mapping Key</a></li>
                        </ul>
                        <div class='tab'>";
                        
                $sidebar .="<div id='tab-1' class='tab-content'>"; //beginning of first tab of data (Scheduled Students)
                          //$sidebar.="<p>".."</p>";
                        foreach($this->Scheduled_Info as $key)
                        {
                            $sidebar.="<p>".$key."</p>";
                        }
                $sidebar .="</div>"; //end of first tab of data
                $sidebar .="<div id='tab-2' class='tab-content'>"; //beginning of second tab of data (Unscheduled Students)
                        foreach($this->Unscheduled_Students as $key)
                        {
                            $sidebar.="<p>".$key->getName()."</p>";
                        }
                $sidebar .="</div>"; //end of first tab of data
                $sidebar .="<div id='tab-3' class='tab-content'>"; //beginning of third tab of data (Mapping Key)
                                    
                $sidebar .="</div>"; //end of first tab of data
                $sidebar .="            
                        </div>
                    </div>"; //end of sidebar
                
            }
            return $out .= "\n".$this->replacements['table_close'].$sidebar;
               
	}//end of generator

	// --------------------------------------------------------------------

	/**
	 * Get Month Name
	 *
	 * Generates a textual month name based on the numeric
	 * month provided.
	 *
	 * @param	int	the month
	 * @return	string
	 */
	public function get_month_name($month)
	{
		if ($this->month_type === 'short')
		{
			$month_names = array('01' => 'cal_jan', '02' => 'cal_feb', '03' => 'cal_mar', '04' => 'cal_apr', '05' => 'cal_may', '06' => 'cal_jun', '07' => 'cal_jul', '08' => 'cal_aug', '09' => 'cal_sep', '10' => 'cal_oct', '11' => 'cal_nov', '12' => 'cal_dec');
		}
		else
		{
			$month_names = array('01' => 'cal_january', '02' => 'cal_february', '03' => 'cal_march', '04' => 'cal_april', '05' => 'cal_mayl', '06' => 'cal_june', '07' => 'cal_july', '08' => 'cal_august', '09' => 'cal_september', '10' => 'cal_october', '11' => 'cal_november', '12' => 'cal_december');
		}

		return ($this->CI->lang->line($month_names[$month]) === FALSE)
			? ucfirst(substr($month_names[$month], 4))
			: $this->CI->lang->line($month_names[$month]);
	}

	// --------------------------------------------------------------------

	/**
	 * Get Day Names
	 *
	 * Returns an array of day names (Sunday, Monday, etc.) based
	 * on the type. Options: long, short, abr
	 *
	 * @param	string
	 * @return	array
	 */
	public function get_day_names($day_type = '')
	{
		if ($day_type !== '')
		{
			$this->day_type = $day_type;
		}

		if ($this->day_type === 'long')
		{
			$day_names = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
		}
		elseif ($this->day_type === 'short')
		{
			$day_names = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
		}
		else
		{
			$day_names = array('su', 'mo', 'tu', 'we', 'th', 'fr', 'sa');
		}

		$days = array();
		for ($i = 0, $c = count($day_names); $i < $c; $i++)
		{
			$days[] = ($this->CI->lang->line('cal_'.$day_names[$i]) === FALSE) ? ucfirst($day_names[$i]) : $this->CI->lang->line('cal_'.$day_names[$i]);
		}

		return $days;
	}

	// --------------------------------------------------------------------

	/**
	 * Adjust Date
	 *
	 * This function makes sure that we have a valid month/year.
	 * For example, if you submit 13 as the month, the year will
	 * increment and the month will become January.
	 *
	 * @param	int	the month
	 * @param	int	the year
	 * @return	array
	 */
	public function adjust_date($month, $year)
	{
		$date = array();

		$date['month']	= $month;
		$date['year']	= $year;

		while ($date['month'] > 12)
		{
			$date['month'] -= 12;
			$date['year']++;
		}

		while ($date['month'] <= 0)
		{
			$date['month'] += 12;
			$date['year']--;
		}

		if (strlen($date['month']) === 1)
		{
			$date['month'] = '0'.$date['month'];
		}

		return $date;
	}

	// --------------------------------------------------------------------

	/**
	 * Total days in a given month
	 *
	 * @param	int	the month
	 * @param	int	the year
	 * @return	int
	 */
	public function get_total_days($month, $year)
	{
		$this->CI->load->helper('date');
		return days_in_month($month, $year);
	}

	// --------------------------------------------------------------------

	/**
	 * Set Default Template Data
	 *
	 * This is used in the event that the user has not created their own template
	 *
	 * @return	array
	 */
       
	public function default_template()
	{
		if($this->user->isAdvisor()){
                return array(
			'table_open'				=> '<script>function setGetParameter(paramName, paramValue){var url = window.location.href;if (url.indexOf(paramName + "=") >= 0){var prefix = url.substring(0, url.indexOf(paramName));var suffix = url.substring(url.indexOf(paramName));suffix = suffix.substring(suffix.indexOf("=") + 1);suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";url = prefix + paramName + "=" + paramValue + suffix;}else{if (url.indexOf("?") < 0)url += "?" + paramName + "=" + paramValue;else url += "&" + paramName + "=" + paramValue;}window.location.href = url;}</script><div id="calwrap"><form action="appointment_controller/fill" method="post">',//<table border="0" cellpadding="4" cellspacing="0"></table>
			'heading_row_start'			=> '<table id="top" class="animated fadeInUp"><tr><td><input id= "startT" type="text" value=""></td><td colspan="2"></td><td colspan="3"> <select id="intervals" name="intervals" onchange="setGetParameter(\'interval\',document.getElementById(\'intervals\').options[document.getElementById(\'intervals\').selectedIndex].value)"><option>Intervals</option><option value="10">10 min.</option><option value="15">15 min.</option><option value="20">20 min.</option><option value="30">30 min.</option></select> </td><td></td><td id ="submitwrap" rowspan="2"><input id="submit" name="submit" type="submit" value="Add"></td></tr><tr>',//<th>
			'heading_previous_cell'		=> '<td id="pad"><input id="endT" type="text" value=""></td><td><a id="prevweek" href ="javascript:void(0);" onclick="nextOrPrev(this)"><div>&lt;</div></a></td><td><a id="previousnext" href="{previous_url}"><div>&lt;&lt;</div></a></td>',
			'heading_title_cell'		=> '<td>{heading}</td>',
			'heading_next_cell'			=> '<td><a id="nextprevious" href="{next_url}"><div>&gt;&gt;</div></a></td><td><a id="nextweek" href="javascript:void(0);" onclick="nextOrPrev(this)"><div>&gt;</div></a></td><td id="pad"></td></table>',
			'heading_row_end'			=> '', //</th>
			'week_row_start'			=> '', //<tr><div id="weekrow"></div>
			'week_day_cell'				=> '{week_day}',
			'week_row_end'				=> '', //</tr>
			'cal_row_start'				=> '<table class="scroll" ',
			'cal_cell_start'			=> '<th ',
			'cal_cell_start_today'		=> '<th ',
			'cal_cell_start_other'		=> '<th style="color: #f2f2f2;" ',
			'cal_cell_content'			=> '<a href="{content}">{day}<divid="cellcontent"></div></a>',
			'cal_cell_content_today'	=> '<a href="{content}"><strong>{day}<div id="cellcontenttoday"></div></strong></a>',
			'cal_cell_no_content'		=> '{day}',
			'cal_cell_no_content_today'	=> '<strong style="color: #e4481b;;">{day}</strong>',
			'cal_cell_blank'			=> '&nbsp;',
			'cal_cell_other'			=> '{day}',
			'cal_cell_end'				=> '</th>',
			'cal_cell_end_today'		=> '</th>',
			'cal_cell_end_other'		=> '</th>',
			'cal_row_end'				=> '</tbody></table>',
			'table_close'				=> '</div></form></div>'
                );}
                
                if($this->user->isStudent()){
                return array(
			'table_open'				=> '<script>function setGetParameter(paramName, paramValue){var url = window.location.href;if (url.indexOf(paramName + "=") >= 0){var prefix = url.substring(0, url.indexOf(paramName));var suffix = url.substring(url.indexOf(paramName));suffix = suffix.substring(suffix.indexOf("=") + 1);suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";url = prefix + paramName + "=" + paramValue + suffix;}else{if (url.indexOf("?") < 0)url += "?" + paramName + "=" + paramValue;else url += "&" + paramName + "=" + paramValue;}window.location.href = url;}</script><div id="calwrap"><form action="appointment_controller/fill" method="post">',//<table border="0" cellpadding="4" cellspacing="0"></table>
			'heading_row_start'			=> '<table id="top" class="animated fadeInUp"><tr><td><input id= "startT" type="text" value=""></td><td colspan="2"></td><td colspan="3">  </td><td></td><td id ="submitwrap" rowspan="2"><input id="submit" name="submit" type="submit" value="Add"></td></tr><tr>',//<th>
			'heading_previous_cell'		=> '<td id="pad"><input id="endT" type="text" value=""></td><td><a id="prevweek" href ="javascript:void(0);" onclick="nextOrPrev(this)"><div>&lt;</div></a></td><td><a id="previousnext" href="{previous_url}"><div>&lt;&lt;</div></a></td>',
			'heading_title_cell'		=> '<td>{heading}</td>',
			'heading_next_cell'			=> '<td><a id="nextprevious" href="{next_url}"><div>&gt;&gt;</div></a></td><td><a id="nextweek" href="javascript:void(0);" onclick="nextOrPrev(this)"><div>&gt;</div></a></td><td id="pad"></td></table>',
			'heading_row_end'			=> '', //</th>
			'week_row_start'			=> '', //<tr><div id="weekrow"></div>
			'week_day_cell'				=> '{week_day}',
			'week_row_end'				=> '', //</tr>
			'cal_row_start'				=> '<table class="scroll" ',
			'cal_cell_start'			=> '<th ',
			'cal_cell_start_today'		=> '<th ',
			'cal_cell_start_other'		=> '<th style="color: #f2f2f2;" ',
			'cal_cell_content'			=> '<a href="{content}">{day}<divid="cellcontent"></div></a>',
			'cal_cell_content_today'	=> '<a href="{content}"><strong>{day}<div id="cellcontenttoday"></div></strong></a>',
			'cal_cell_no_content'		=> '{day}',
			'cal_cell_no_content_today'	=> '<strong style="color: #e4481b;;">{day}</strong>',
			'cal_cell_blank'			=> '&nbsp;',
			'cal_cell_other'			=> '{day}',
			'cal_cell_end'				=> '</th>',
			'cal_cell_end_today'		=> '</th>',
			'cal_cell_end_other'		=> '</th>',
			'cal_row_end'				=> '</tbody></table>',
			'table_close'				=> '</div></form></div>'
                );}
	}

	// --------------------------------------------------------------------

	/**
	 * Parse Template
	 *
	 * Harvests the data within the template {pseudo-variables}
	 * used to display the calendar
	 *
	 * @return	CI_Calendar
	 */
	public function parse_template()
	{
		$this->replacements = $this->default_template();

		if (empty($this->template))
		{
			return $this;
		}

		if (is_string($this->template))
		{
			$today = array('cal_cell_start_today', 'cal_cell_content_today', 'cal_cell_no_content_today', 'cal_cell_end_today');

			foreach (array('table_open', 'table_close', 'heading_row_start', 'heading_previous_cell', 'heading_title_cell', 'heading_next_cell', 'heading_row_end', 'week_row_start', 'week_day_cell', 'week_row_end', 'cal_row_start', 'cal_cell_start', 'cal_cell_content', 'cal_cell_no_content', 'cal_cell_blank', 'cal_cell_end', 'cal_row_end', 'cal_cell_start_today', 'cal_cell_content_today', 'cal_cell_no_content_today', 'cal_cell_end_today', 'cal_cell_start_other', 'cal_cell_other', 'cal_cell_end_other') as $val)
			{
				if (preg_match('/\{'.$val.'\}(.*?)\{\/'.$val.'\}/si', $this->template, $match))
				{
					$this->replacements[$val] = $match[1];
				}
				elseif (in_array($val, $today, TRUE))
				{
					$this->replacements[$val] = $this->replacements[substr($val, 0, -6)];
				}
			}
		}
		elseif (is_array($this->template))
		{
			$this->replacements = array_merge($this->replacements, $this->template);
		}

		return $this;
	}

}
