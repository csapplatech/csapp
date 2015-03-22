<?php
class Checklistexport extends CI_Controller
{
	public function index($person = NULL)	
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

		//Change to xls or pdf or whatever
		
		//Return file object (PDF or XLS)
	
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=test.txt");
	        print "TESTING";
	}
}
