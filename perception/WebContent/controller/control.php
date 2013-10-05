<?php

require('functions/search_module.php');
require('functions/user_module.php');

error_reporting(E_ALL);

//Global variable for exception data;
$exception = "";

//Validate login status via cookies
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
			echo "<h1>registered with facebook...</h1>";
			break;
	}	
}

if (strstr($command, "results_"))
{
	switch (true)
	{
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


//Print errors (using the global exception and message)
function Control_DisplayErrorMessage($message)
{	
	include('../view/error.html');	
}

function setLoginStatus()
{
	if (isset($_COOKIE['user_name']) && isset($_COOKIE['user_password']))
	{		
		if (User_LoginStatus($_COOKIE['user_name'], $_COOKIE['user_password']))
		{
			setLoginCookie($_COOKIE['user_name'], $_COOKIE['user_password']);
			return true;
		}
	}	
	return false;
}

?>
	
	
