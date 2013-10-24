<?php

//Manages user-course interaction and provider-course interaction




function CMS_AddCourse($apikey, $xml)
{
	$opts = array(
			'http' => array(
			'method'  => 'POST',
			'header'=> "Content-type: application/xml",
			'content' => $xml
			)
		);	
	
	$context  = stream_context_create($opts);
	
	//http://attr192.srvr.cse.unsw.edu.au/COMP9323-MOOCIndexSearchServices/
	if ($id = file_get_contents("http://localhost:8080/COMP9323-MOOCIndexSearchServices/courses/add?APIkey=". $apikey, false, $context)) {	
		$provider_message =  'Added course: <a href="http://comp4920.com/perception/WebContent/controller/control.php?query_type=course_info&c_results_info_'.$id.'=View+course">'.$id .'</a>';
		include('../view/provider_profile.html');
	}
	
	return;	
}

function CMS_DeleteCourse($apikey,$id)
{
	$opts = array(
		'http' => array(
			'method'  => 'DELETE'));
	$context  = stream_context_create($opts);
	if (!file_get_contents('http://localhost:8080/COMP9323-MOOCIndexSearchServices/courses/'.$id.'/delete?APIkey='. $apikey, false, $context)) {	
		$provider_message = 'Delete course: <a href="http://comp4920.com/perception/WebContent/controller/control.php?query_type=course_info&c_results_info_'.$id.'=View+course">'.$id .		'</a>';
		include('../view/provider_profile.html');
	}

	return;
}

function CMS_AddMaterial($title, $id, $file)
{
	
	$provider_message = 'Added course: '.$title;
	
	include('../view/provider_profile.html');
	return;
}

function CMS_DeleteMaterial($apikey, $id)
{
	
	$opts = array(
		'http' => array(
			'method'  => 'DELETE'));
	$context  = stream_context_create($opts);
	if (!file_get_contents('http://localhost:8080/COMP9323-MOOCIndexSearchServices/materials/'.$id.'/delete?APIkey='. $apikey, false, $context)) {	
		$provider_message = 'Delete materials: <a href="http://comp4920.com/perception/WebContent/controller/control.php?query_type=material_info&c_results_info_'.$id.'=View+material">'.$id .		'</a>';
		include('../view/provider_profile.html');
	}
}

function CMS_DownloadUserLogs()
{	
	$user_log = DAO_GetUserLogs();
	include('../view/user_log.html');
}


?>