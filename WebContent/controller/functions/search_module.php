<?php
//search_module
//Provides functions for sending search requests and processing
//	responses from the Searching and Indexing modules

//Provides classes for storing response data
require('classes.php');

class QueryType
{
	const Course_List = 'course_list';
	const Course_Info = 'course_info';
	const Material_List = 'material_list';
	const Material_Info = 'material_info';
}

function Search_ExecuteQuery($identifier, $query_type, $num_results)
{		
			
	//Send service query
	if (!$xmlResponse = sendServiceRequest($identifier, $query_type, $num_results)) {
		Control_DisplayErrorMessage("sendServiceRequest failed.");
		return;
	}

	//Get results of query. Either list or info (courses and materials)
	if (!$result = parseServiceResponse($xmlResponse, $query_type))
	{
		Control_DisplayErrorMessage("parseServiceResponse failed");
		return;
	}
	
	//Display result of query		
	switch ($query_type)
	{
		case QueryType::Course_List:
			$items = $result;
			include('../view/search_results.html');
			break;
		case QueryType::Course_Info:
			$course = $result;
			include('../view/course_view.html');
			break;
		case QueryType::Material_List:
			$items = $result;
			include('../view/search_results.html');
			break;
		case QueryType::Material_Info:
			$material = $result;
			//include('../view/course_view.html');
			//header('Location: '.$material->path);
			header('Location: http://attr192.srvr.cse.unsw.edu.au/COMP9323-MOOCIndexSearchServices/materials/10');
			break;
			
	}
	 	
}

//Creates/sends a HTTP request with the search string
//Returns the XML response
function sendServiceRequest($identifier, $query_type, $num_results)
{	
	$xmlResponse  = "";
	$query_URL = "";
	$query_string = ""; 	
	
	//Get XML response from URL required
	switch ($query_type)
	{				
		case QueryType::Course_List:
			$query_URL = "http://attr192.srvr.cse.unsw.edu.au/COMP9323-MOOCIndexSearchServices/courses/gsearch?";
			$query_string = http_build_query(array('q' => $identifier, 'num' => $num_results));
			//remove when finished debugging
			//$xmlResponse = file_get_contents("../model/course_list.xml");
			break;
		case QueryType::Course_Info:
			$query_URL = "http://attr192.srvr.cse.unsw.edu.au/COMP9323-MOOCIndexSearchServices/courses/";
			$query_string = $identifier;	
			//remove when finished debugging		
			//$xmlResponse = file_get_contents("../model/course_info.xml");
			break;
		case QueryType::Material_List:			
			$query_URL = "http://attr192.srvr.cse.unsw.edu.au/COMP9323-MOOCIndexSearchServices/materials/search?";
			$query_string = http_build_query(array('q' => $identifier, 'num' => $num_results));
			//remove when finished debugging
			//$xmlResponse = file_get_contents("../model/material_list.xml");			
			break;
		case QueryType::Material_Info:
			$query_URL = "http://attr192.srvr.cse.unsw.edu.au/COMP9323-MOOCIndexSearchServices/materials/";
			//$query_string = $identifier;
			$query_string = "10";
			//remove when finished debugging
			//$xmlResponse = file_get_contents("../model/material_info.xml");
			break;
	}
	
	
	$opts = array('http' => array('method'  => 'GET'));	
	$context  = stream_context_create($opts);
	$xmlResponse = file_get_contents($query_URL.$query_string, false, $context);
	if (!xmlResponse)
	{
		$GLOBALS['exception'] = "Failed to receive courses from CDE module.";
		//$GLOBALS['exception'] = $query_URL.$query_string;
		//$GLOBALS['exception'] = file_get_contents($query_URL.$querystring, false, $context);
		//$GLOBALS['exception'] = "what";
		return false;
	}
			

	/*
	if (!$xmlResponse)
	{
		$GLOBALS['exception'] = "Failed to receive courses from CDE module.";
		return false;
	}
	*/	
	
	return $xmlResponse;	
}

//Parse XML response from Lucene
//Returns an array of Course objects
function parseServiceResponse($xmlstring, $query_type) {
		
	//Parse the response	
	$xml_elements = new SimpleXMLElement($xmlstring);	
	$items = '';
	
	//Process XML here for courses	
	switch ($query_type)
	{
		case QueryType::Course_List:
			$items = getXML_Course_List($xml_elements);
			break;
		case QueryType::Course_Info:
			$items = getXML_Course_Info($xml_elements);
			break;
		case QueryType::Material_List:
			$items = getXML_Material_List($xml_elements);
			break;			
		case QueryType::Material_Info:
			$items = getXML_Material_Info($xml_elements);
			break;
	}	
	
	if (!$items)
	{
		$GLOBALS['exception'] = 'Unable to parse service response';
		return false;
	}
	
	return $items;	
}

function getXML_Course_List($course_elements)
{
	$course_list = array();
		
	foreach ($course_elements as $course)
	{
		$newCourse = new Course;
		$newCourse->ID = $course->ID;
		$newCourse->name = $course->name;
		$newCourse->provider = $course->provider;
		$newCourse->university = $course->university;
		
		$course_list[] = $newCourse;
	}		
	
	return $course_list;
}

function getXML_Course_Info($course_element)
{

	$newCourse = new Course;
	$newCourse->ID = $course_element->ID;
	$newCourse->name = $course_element->name;
	$newCourse->provider = $course_element->provider;
	$newCourse->university = $course_element->university;
	
	$newCourse->description = $course_element->description;
	$newCourse->duration = $course_element->duration;
	$newCourse->website = $course_element->website;
	$newCourse->logoURI = $course_element->logoURI;
	$newCourse->videoURI = $course_element->videoURI;		

	return $newCourse;
}

function getXML_Material_List($material_elements)
{
	$material_list = array();
	
	foreach ($material_elements as $material)
	{
		$newMaterial = new Material;
		$newMaterial->courseID = $material->courseID;
		$newMaterial->courseName = urldecode($material->courseName);
		$newMaterial->ID = $material->ID;
		$newMaterial->title = urldecode($material->title);
	
		$material_list[] = $newMaterial;
	}	
	
	return $material_list;
}

function getXML_Material_Info($material_elements)
{
	$newMaterial = new Material;
	$newMaterial->ID = $material_elements->ID;
	$newMaterial->title = urldecode($material_elements->title);
	$newMaterial->path = $material_elements->path;

	return $newMaterial;
}



/* Send POST requests
 $postdata = http_build_query(array('search_text' => $search_text));

$opts = array('http' =>
		array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
		)
);

$context  = stream_context_create($opts);

$result = @file_get_contents('http://localhost:81search', false, $context);
*/


?>