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

class Log
{
	public $recorded;
	public $user_id;	
	public $action;
	public $search_text;
	public $course_id;
	public $material_id;	
}

class QueryType
{
	const CourseList = 'course_list';
	const CourseInfo = 'course_info';
	const MaterialList = 'material_list';
	const MaterialInfo = 'material_info';
}

class UserAction
{
	const Created = 'created';
	const Search = 'search';
	const ViewCourse = 'view_course';
	const ViewMaterial = 'view_material';
	const Completed = 'completed';
	const Interest = 'interest';
	const Review = 'review';
	const Rate = 'rate';
}


?>