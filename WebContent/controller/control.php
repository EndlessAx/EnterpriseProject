<?php

require('functions/search_module.php');
require('functions/user_module.php');
require('functions/cms_module.php');

//Global variable for exception data;
$exception = "";

//Validate login status via sessions
session_start();
$login_status = setLoginStatus();
 	
//Default command is home. Otherwise, get command
$command = "home";

foreach (array_keys($_REQUEST) as $key)
{
	//if found command parameter
	if (strstr($key, 'c_')) 
		$command = $key;
}

if ($command === "home")
{
	include("../view/home.html");
	return;		
}

//If command is from a header
if (strstr($command, 'header_'))
{
	switch ($command)
	{
		case "c_header_login":
			include('../view/login.html');
			break;
		case "c_header_logout":
			User_Logout();
			break;
		case "c_header_register":
			include('../view/register.html');
			break;			
	}
	return;
	
}

if (strstr($command, 'home_'))	
{
	switch ($command) 
	{			
		case "c_home_search":			
			Search_ExecuteQuery($_REQUEST['search_text'],
								$_REQUEST['query_type'],
								$_REQUEST['query_num']);
			break;			
					
		case "c_home_logout":
			User_Logout();
			break;
			
		case "c_home_register":
			include('../view/register.html');
			break;							
	}
	
	return;
}

if (strstr($command, 'login_'))
{
	switch($command)
	{
		case "c_login_normal":
			User_Login($_REQUEST['user_email'], $_REQUEST['user_password']);
			break;				
		case "c_login_error":
			$login_error = true;
			include("../view/login.html");
			break;
		case "c_login_register":
			include("../view/register.html");
			break;
	}
	return;
}

if (strstr($command, 'register_'))
{
	switch ($command)
	{
		case "c_register_normal":
			//user interaction command
			User_RegisterNormal($_REQUEST["user_name"],
			$_REQUEST["user_email"],
			$_REQUEST["user_password"],
			$_REQUEST["user_confirmpassword"]);
			break;
				
		case "c_register_facebook":
			//user interaction command
			//echo "<h1>registered with facebook...</h1>";
			break;
	}	
	
	return;
}

if (strstr($command, "results_"))
{
	switch (true)
	{
		case strstr($command, 'courseinfo_'):
			$parts = explode("_",$command);
			$identifier = $parts[3];
			Search_ExecuteQuery($identifier, 'course_info', 0);
			break;
		
		case strstr($command, 'info_'):
			$parts = explode("_",$command);
			$identifier = $parts[3];				
			Search_ExecuteQuery($identifier, $_REQUEST['query_type'], 0);								 								 
			break;

		case strstr($command, 'interest_'):		
			Search_ExecuteQuery( $_REQUEST['search_text'],
								 $_REQUEST['query_type'], 0);
			break;

		
	}	
}

if (strstr($command, "provider_"))
{
	switch (true)
	{
		case strstr($command, 'login'):
			include('../view/provider_profile.html');
			break;		
			
		case strstr($command, 'addcourse'):
			CMS_AddCourse(	$_REQUEST['addcourse_title'],
							$_REQUEST['addcourse_id'],
							$_REQUEST['addcourse_file']);			
			break;
		case strstr($command, 'deletecourse'):
			CMS_DeleteCourse($_REQUEST['deletecourse_id']);
			break;
			
		case strstr($command, 'addmaterial'):
			CMS_AddMaterial($_REQUEST['addmaterial_title'],
							$_REQUEST['addmaterial_id'],
							$_REQUEST['addmaterial_file']);
			break;
		case strstr($command, 'deletematerial'):
			CMS_DeleteMaterial($_REQUEST['deletematerial_id']);
				break;
	}
}


//Print errors (using the global exception and message)
function Control_DisplayErrorMessage($message)
{	
	include('../view/error.html');	
}

function setLoginStatus()
{
	if (isset($_SESSION['user_name']))
	{				
			return true;		
	}	
	return false;
}

?>
	
	
