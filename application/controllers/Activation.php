<?php
class Activation extends CI_Controller
{
	//Sets a user's password to a random password
	//	Emails user the new password and their username
	//		And a link to login
	//	Email is either the user's set email, or if passed
	//					the optional passed one
	public function index($userID = NULL, $email = NULL)
	{
		$this->load->model('User_model');
		$this->load->library('email');

		$user = new User_Model();
		$user->loadPropertiesFromPrimaryKey($userID);

		//Loads user's email if optional email wasn't set
		if ($email == NULL)
			$email = $user->getEmailAddress();

		//Array of characters to generate password
		$charset = array(
			'0','1','2','3','4','5','6','7','8','9',
			'a','b','c','d','e','f','g','h','i','j',
			'k','l','m','n','o','p','q','r','s','t',
			'u','v','w','x','w','z',
			'A','B','C','D','E','F','G','H','I','J',
			'K','L','M','N','O','P','Q','R','S','T',
			'U','V','W','X','W','Z'
		);
		
		//Generate random password
		$passlen = mt_rand(8,12);
		$pass = NULL;
		for ($i = 0; $i < $passlen; $i++)
			$pass = $pass.$charset[mt_rand(0, count($charset)-1)];

		//Set user password


		//Email user their login information
		$this->email->from   ('williamgkeen@gmail.com', 'Admin Name');
		$this->email->to     ('williamgkeen@gmail.com');
		$this->email->subject('Subject');
		$this->email->message(
			'Password: '.$pass. "\r\n".
			'Username: '.$email."\r\n"
			);
		$this->email->send(FALSE);
		$this->email->print_debugger(array('headers','subject','body'));
	}
}
