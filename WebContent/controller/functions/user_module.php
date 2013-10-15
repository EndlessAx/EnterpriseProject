<?php
//User module

require('../model/user_module_DAO.php');

	/*
 * function RegisterUserNormal($name, $email, $password, $confirmpassword)
 * function CheckLoginStatus($cookies)
 * function DeleteUser($user_id)
 * function ValidateLogin($email, $password)
 * function setLoginCookie($name, $email, $password)
 * function unsetLoginCookie($email, $password)
 * function CheckLoginStatus($cookies) 
 * 
 */


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
		return;
	}
			
	if ($result = DAO_GetUserByName($name))
	{		
		$input_errors = "A user with the name '$result->name' already exists";		
		include('../view/register.html');
		return;
	} else if (strlen($GLOBALS['exception']) > 0)
	{
		Control_DisplayErrorMessage("Query failed: GetUserByName");
		return;
	}
	
	if ($result = DAO_GetUserByEmail($email))
	{
		$input_errors = "A user with the email '$result->email' already exists";
		include('../view/register.html');
		return;
	} else if (strlen($GLOBALS['exception']) > 0)
	{
		Control_DisplayErrorMessage("Query failed: GetUserByName");
		return;
	}
		
	
	if (!DAO_AddUser($name, $email, $password))
	{				
		Control_DisplayErrorMessage("Query failed: AddUser");
		return;
	}
	
	//User added.
	setLoginSession($name);	
	include('../view/home.html');
	return;			
}


function User_RegisterFacebook() 
{
	
}

function DeleteUser($user_id) 
{
	if (!DAO_DeleteUser($user_id)) 
	{
		Control_DisplayErrorMessage("Unable to delete user: ".$user_id);
	} else
	{
		//Set cookie "logged out"
		//include('../view/search.html');		
	}	
	return;
} 

function GetUserByEmail($email)
{
	
}

function User_Login($email, $password)
{		
	$user = DAO_ValidateLogin($email, md5($password));
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


function User_LoginStatus($name, $password)
{	
	if (!DAO_UserIsValid_Name($name, $password))
		return false;
	else
		return true;
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
	/*
	setcookie('user_name', '', time()-3600);	
	setcookie('user_password', '', time()-3600);
	*/	
}

//Check if user is logged in via cookies.
//Takes the $_COOKIES array
//True if logged in, false if not




?>