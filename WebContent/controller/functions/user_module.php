<?php
//User module.

require('../model/user_module_DAO.php');

function User_RegisterNormal($name, $email, $password, $confirmpassword) 
{	
	$input_errors = "";	
	
	if (preg_match('/[^a-zA-Z0-9_]+/', $name) || strlen($name) < 6) {
		$input_errors = 'Invalid name. Must be greater than '.
								'5 characters long and not contain special characters<br/>';
	}					
	
	if (!preg_match('/[^@]+@[^\.]+\.[^\.]+/', $email)) {
		$input_errors = $input_errors.'Invalid email address.<br/>';
	}
	
	if (strcmp($password, $confirmpassword) !== 0) {
		$input_errors = $input_errors.'Passwords do not match.<br/>';
	}		
	
	if (strlen($input_errors) > 0)		
	{			
		include('../view/register.html');
		return false;
	}
			
	if ($result = DAO_GetUserByName($name))
	{		
		$input_errors = "A user with the name '$result->name' already exists";		
		include('../view/register.html');
		return false;
	} else if (strlen($GLOBALS['exception']) > 0)
	{
		Control_DisplayErrorMessage("Query failed: GetUserByName");
		return false;
	}
	
	if ($result = DAO_GetUserByEmail($email))
	{
		$input_errors = "A user with the email '$result->email' already exists";
		include('../view/register.html');
		return false;
	} else if (strlen($GLOBALS['exception']) > 0)
	{
		Control_DisplayErrorMessage("Query failed: GetUserByName");
		return false;
	}
		
	
	if (!DAO_AddUser($name, $email, $password))
	{				
		Control_DisplayErrorMessage("Query failed: AddUser");
		return false;
	}
		
	//User added.
	setLoginSession($name);	
	
	include('../view/home.html');
	return true;
}


function User_RegisterFacebook() 
{
	
}

function User_AddInterestToUser($user_name, $course_id, $course_name)
{		
	$user = DAO_GetUserByName($user_name);
	$result = DAO_AddInterestToUser($user->id, $course_id, $course_name);

	if (!$result)
	{	
		Control_DisplayErrorMessage("Unable to add interest to user.");
		return false;
	}
	else
	{
		return true;
	}	
}

function User_RemoveInterestFromUser($user_name, $course_id)
{
	$user = DAO_GetUserByName($user_name);
	$result = DAO_RemoveInterestFromUser($user->id, $course_id);
	
	if (!$result)
	{	
		Control_DisplayErrorMessage("Unable to remove interest to user.");
		return false;
	}	
	
	return true;
	
}

function User_GetUserInterests($user_name)
{	
	$result = DAO_GetUserInterestsByName($user_name);
	
	if (!$result)
	{
		Control_DisplayErrorMessage("Unable to get user interests");
		return false;
	}
	
	return $result;				
}

function User_AddCompletionToUser($user_name, $course_id, $course_name)
{
	$user = DAO_GetUserByName($user_name);
	$result = DAO_AddCompletionToUser($user->id, $course_id, $course_name);

	if (!$result)
	{
		Control_DisplayErrorMessage("Unable to add completion from user.");
		return false;
	}
	else
	{		
		return true;
	}
}

function User_RemoveCompletionFromUser($user_name, $course_id)
{
	$user = DAO_GetUserByName($user_name);
	$result = DAO_RemoveCompletionFromUser($user->id, $course_id);

	if (!$result)
	{
		Control_DisplayErrorMessage("Unable to remove completion from user.");
		return false;
	}
		
	return true;
}

function User_GetUserCompletions($user_name)
{
	$result = DAO_GetUserCompletionsByName($user_name);

	if (!$result)
	{
		Control_DisplayErrorMessage("Unable to get user interests");
		return false;
	}

	return $result;
}

function User_Login($email, $password)
{		
	$user = DAO_GetUserByLogin($email, $password);
	if ($user) {						
		setLoginSession($user->name);
		include('../view/home.html');
		
	} else if (!$GLOBALS['exception']) {					
		$login_error = "Invalid email/password";
		include('../view/login.html');
		
	} else {
		Control_DisplayErrorMessage("Login failed.");
		return false;
	}				
}

function User_Logout()
{
	unsetLoginSession();
	include('../view/home.html');
}

function setLoginSession($name)
{		
	$_SESSION['user_name'] = $name;	
	return;
}

function unsetLoginSession()
{
	unset($_SESSION['user_name']);
	return;	
}

function User_GetUserByName($user_name)
{
	return DAO_GetUserByName($user_name);
}

function User_LogUserAction($user_id, $action, $search_text, $course_id, $material_id)
{
	if (!DAO_AddUserTrackingData($user_id, $action, $search_text, $course_id, $material_id))
	{
		Control_DisplayErrorMessage("Log User Action failed");
		return;
	}	
}

function User_DisplayProfile($user_name)
{
	$user = DAO_GetUserByName($user_name);
	$user_interests = DAO_GetUserInterestsByName($user_name);
	$user_completions = DAO_GetUserCompletionsByName($user_name);	
	include('../view/user_profile.html');
	return;
	
	
}

function User_DeleteUser($user_name)
{
	$user = DAO_GetUserByName($user_name);
	
	if (DAO_DeleteUser($user->id))
	{
		unset($_SESSION['user_name']);
		include('../view/home.html');
	}
	else	
	{
		Control_DisplayErrorMessage("Unable to delete user: ".$user_id);
	}			
		
	return;
}

//Check if user is logged in via cookies.
//Takes the $_COOKIES array
//True if logged in, false if not




?>