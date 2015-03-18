<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ModelTest short summary.
 *
 * ModelTest is a controller for outputting simple test results for unit testing various models as they are created
 * This controller should not exist in the final software product for the CS App product
 */
class ModelTest extends CI_Controller
{
    /*
     * Simple Index action for this controller to ensure the controller is accessible
     */
    public function index()
    {
        echo "THIS IS A TEST";
    }
    
    public function user()
    {
        // Check to see if a user id segment in the URI was specified
        if( ! $this->uri->segment(3, 0))
        {
            echo "<h3>No user model Primary key specified</h3>";
            echo "<p>Try <code>https://localhost/index.php/ModelTest/user/1</code></p>";
        }
        else
        {
            // Load a user model instance and give the variable name 'user'
            $this->load->model('User_model', 'user');
            
            // Try to load the user model values from the primary key provided in the URL
            if($this->user->loadPropertiesFromPrimaryKey($this->uri->segment(3, 0)))
            {
                echo "<h3>Success in finding user!</h3><code>";
                print_r($this->user);
                echo "</code>";
            }
            // Invalid UserID format or no user of that ID was found
            else
            {
                echo "<h2>Error 404 - User not found";
            }
        }
    }
}
