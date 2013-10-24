<?php

require('functions/search_module.php');
require('functions/user_module.php');
require('functions/cms_module.php');

//Global variable for exception data;
$exception = "";
$apikey;

//Validate login status via sessions
session_start();
$login_status = Control_GetLoginStatus();
 	
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
	switch (true)
	{
		case strstr($command, 'login'):
			include('../view/login.html');
			break;
		case strstr($command, 'logout'):
			User_Logout();
			break;
		case strstr($command, 'register'):
			include('../view/register.html');
			break;			
		case strstr($command, 'profile'):
			User_DisplayProfile($_SESSION['user_name']);
			break;
	}
	return;
	
}

if (strstr($command, 'home_'))	
{
	switch (true) 
	{			
		case strstr($command, 'search'):
									
			Search_ExecuteQuery($_REQUEST['search_text'],
								$_REQUEST['query_type'],
								$_REQUEST['query_num']);
			
			Control_LogUserAction(UserAction::Search,'('.$_REQUEST['query_type'].') '.$_REQUEST['search_text']);
			
			break;			
					
		case strstr($command, 'logout'):
			User_Logout();
			break;
			
		case strstr($command, 'register'):
			include('../view/register.html');
			break;

		case strstr($command, 'profile'):
			User_DisplayProfile($_SESSION['user_name']);
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
			$result = User_RegisterNormal($_REQUEST["user_name"],
											$_REQUEST["user_email"],
											$_REQUEST["user_password"],
											$_REQUEST["user_confirmpassword"]);
			
			if ($result === true)				
				Control_LogUserAction(UserAction::Created, '');
			
			break;
				
		case "c_register_facebook":

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
			Search_ExecuteQuery($identifier, QueryType::CourseInfo, 0);
									
			Control_LogUserAction(UserAction::ViewCourse, $identifier);							
			break;
		
		case strstr($command, 'materialinfo_'):
			$parts = explode("_",$command);
			$identifier = $parts[3];				
			Search_ExecuteQuery($identifier, QueryType::MaterialInfo, 0);
						
			Control_LogUserAction(UserAction::ViewMaterial, $identifier);			
			break;

		case strstr($command, 'showinterest_'):

			if (Control_GetLoginStatus() === false)
			{
				$register_error = true;
				include('../view/login.html');
				return;
			}
			
			$parts = explode("_",$command);
			$course_id = $parts[3];
			$course_name = Search_GetCourseName($course_id);
							
			$result = User_AddInterestToUser($_SESSION['user_name'],$course_id, $course_name);
			if ($result === true)
			{
				$user_interests = array();
				if (Control_GetLoginStatus())
				{
					$user_interest_courses  = User_GetUserInterests(Control_GetUserName());
					foreach ($user_interest_courses as $course)
						$user_interests[] = $course->ID;				
				}
				
				Search_ExecuteQuery($_REQUEST['saved_search_text'],					
									$_REQUEST['saved_query_type'],
									$_REQUEST['saved_query_num']);
				
				Control_LogUserAction(UserAction::Interest, $course_id);
			}
												
			break;
			
		case strstr($command, 'removeinterest_'):
			$parts = explode("_",$command);
			$course_id = $parts[3];
			
			$result = User_RemoveInterestFromUser($_SESSION['user_name'],$course_id); 
			if ($result === true)
			{
				Search_ExecuteQuery($_REQUEST['saved_search_text'],
									$_REQUEST['saved_query_type'],
									$_REQUEST['saved_query_num']);
				
				Control_LogUserAction(UserAction::Interest, $course_id);
			}		
				
			break;			
	}	
}

if (strstr($command, 'courseview_'))
{
	switch (true)
	{
		case strstr($command, 'showinterest_'):
			
			if (Control_GetLoginStatus() === false)
			{
				$register_error = true;
				include('../view/login.html');
				return;
			}
				
			$parts = explode("_",$command);
			$course_id = $parts[3];
			$course_name = Search_GetCourseName($course_id);
				
			$result = User_AddInterestToUser($_SESSION['user_name'],$course_id, $course_name);
						
			if ($result === true)		
				Search_ExecuteQuery($course_id, QueryType::CourseInfo, 0);
			
			
			break;
			
		case strstr($command, 'complete_'):

			if (Control_GetLoginStatus() === false)
			{
				$register_error = true;
				include('../view/login.html');
				return;
			}
			
			$parts = explode("_",$command);
			$course_id = $parts[3];
			$course_name = Search_GetCourseName($course_id);
			
			$result = User_AddCompletionToUser($_SESSION['user_name'],$course_id, $course_name);
				
			if ($result === true)
				Search_ExecuteQuery($course_id, QueryType::CourseInfo, 0);			
			break;
			
		case strstr($command, 'removeinterest_'):
			
			$parts = explode("_",$command);
			$course_id = $parts[3];
			$result = User_RemoveInterestFromUser($_SESSION['user_name'],$course_id);
			
			if ($result === true)
				Search_ExecuteQuery($course_id, QueryType::CourseInfo, 0);
			
			break;
		case strstr($command, 'removecompletion_'):
			
			$parts = explode("_",$command);
			$course_id = $parts[3];
			
			$result = User_RemoveCompletionFromUser($_SESSION['user_name'],$course_id);
			
			if ($result === true)
				Search_ExecuteQuery($course_id, QueryType::CourseInfo, 0);
			
			break;
	}
}

if (strstr($command, "user_"))
{
	switch (true)
	{
		case strstr($command, 'courseinfo_'):
			$parts = explode("_",$command);
			$identifier = $parts[3];
			Search_ExecuteQuery($identifier, QueryType::CourseInfo, 0);
			
			Control_LogUserAction(UserAction::ViewCourse, $identifier);
			break;
			
		case strstr($command, 'removeinterest_'):
			$parts = explode("_",$command);
			$identifier = $parts[3];			
			User_RemoveInterestFromUser($_SESSION['user_name'], $identifier);
			User_DisplayProfile($_SESSION['user_name']);
			break;
			
		case strstr($command, 'removecompletion_'):
			$parts = explode("_",$command);
			$identifier = $parts[3];
			User_RemoveCompletionFromUser($_SESSION['user_name'], $identifier);					
			User_DisplayProfile($_SESSION['user_name']);
			break;
			
		case strstr($command, 'deleteaccount');
			User_DeleteUser($_SESSION['user_name']);									
			break;			
	}
}

if (strstr($command, "provider_"))
{	
	if (isset($_POST['provider_key'])) {
		$_SESSION['apikey'] = $_POST['provider_key'];
	}
	
	switch (true)
	{
		case strstr($command, 'login'):
			include('../view/provider_profile.html');			
			break;		
			
		case strstr($command, 'addcourse'):
			CMS_AddCourse(	$_SESSION['apikey'],
							$_POST['xml']);			
			break;
		case strstr($command, 'deletecourse'):
			CMS_DeleteCourse($_SESSION['apikey'],$_REQUEST['deletecourse_id']);
			break;
			
		case strstr($command, 'addmaterial'):
			CMS_AddMaterial($_SESSION['apikey'], $_REQUEST['addmaterial_title'],
							$_REQUEST['addmaterial_id'],
							$_REQUEST['addmaterial_file']);
			break;
		case strstr($command, 'deletematerial'):
			CMS_DeleteMaterial($_SESSION['apikey'], $_REQUEST['deletematerial_id']);
				break;
	}
}

if (strstr($command, "advertiser_"))
{	
	if (isset($_REQUEST['advertiser_key'])) {
		$_SESSION['apikey'] = $_REQUEST['advertiser_key'];
	}
	
	switch (true)
	{
		case strstr($command, 'download'):			
			CMS_DownloadUserLogs();
			break;
	}
	
	
	
}


//Print errors (using the global exception and message)
function Control_DisplayErrorMessage($message)
{	
	include('../view/error.html');	
}

function Control_LogUserAction($action, $detail)
{
	if (Control_GetLoginStatus() === false)
		return;
		
	$user = User_GetUserByName($_SESSION['user_name']);
		
	$search_text = '';
	$course_id = '';
	$material_id = '';
	 
	if ($action === UserAction::Search)
	{
		$search_text = $detail;
	}
	else if ($action === UserAction::ViewMaterial)
	{
		$material_id = $detail;		
	}
	else if ($action === UserAction::ViewCourse || 
			 $action === UserAction::Interest || 
			 $action === UserAction::Completed || 
			 $action === UserAction::Review ||
			 $action === UserAction::Rate)
	{
		$course_id = $detail;
	}
		
	User_LogUserAction($user->id, $action, $search_text, $course_id, $material_id);
}

function Control_GetLoginStatus()
{
	if (isset($_SESSION['user_name']))
	{				
			return true;		
	}	
	
	return false;
}

function Control_GetUserName()
{
	if (isset($_SESSION['user_name']))
	{
		return $_SESSION['user_name'];
	}
	
	return '';
}

?>