<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class LTP extends CI_Controller
{
    
    public function index()
    {
        error_reporting(E_ALL & ~E_WARNING & ~E_STRICT);
        $this->load->helper('url');
        //$uid = $_SESSION['UserID'];
        /*if (!isset($_SESSION['UserID']))
        {
            redirect('login');
        }*/
        $uid = 10210078;
        //$uid = $_SESSION['UserID'];
        //$year = 2015;
        
        
        
        $data = array();
        $this->load->view('ltp_view', $data);
    }
    
  
}
