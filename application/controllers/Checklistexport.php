<?php
class Checklistexport extends CI_Controller
{
	public function index()
	{
		$this->load->helper('form');
		echo '<html>';
		echo '<head>';
		echo '<title>KEEN-HJORTH</title>';
		echo '</head>';
		echo '<body>';
		echo '<h1>Hello World!</h1>';
		echo '<br />';
		echo '<h2>Keen and Hjorth Rock</h2>';
		$this->xls(NULL);
		echo '</body>';
		echo '</html>';
	}

	public function xls($person)
	{
		//Assuming a userobject with classes is passed
		//	Must be valid!
		
		//Parse classes
		//	Curriculum in database
		//		Has associated slots on the curriculum
		//		Curriculum slots have valid classes associated that can fill those slots
		//	User has list of taken classes in an array
		//	All classes are simply plain text names

		//Create file object (plaintext?)

		//Return file object (PDF or XLS)
	
		echo "<h1>IT WORKED</h1>";		
	}
}
