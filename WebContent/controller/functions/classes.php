<?php

//Holds stored course data from Lucene responses

class Course
{
	public $ID;
	public $name;
	public $provider;
	public $university;
	
	public $description;
	public $duration;
	public $website;
	public $logoURI;
	public $videoURI;
		
}

class Material
{	
	public $ID;
	public $title;
	public $path;
	
	public $courseID;
	public $courseName;

	public $googleDriveID;
	public $viewLink;	
}

?>