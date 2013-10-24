<?php
/*	user_module_DAO
	Provides database access functions for the user module
	
*/


//User class. Holds information about users.
class User
{
	var $id;
	var $name;
	var $email;
	var $password;	
}

function connectMYSQLI()
{	
	$mysql = mysql_connect("attr192.srvr:4321", "perception", "qwerty");
	//$mysql = mysql_connect("localhost","hgoh","");
	
	if (!$mysql) {
		$GLOBALS['exception'] = 'Unable to connect to MySQL';
		return false;
	}
	
	mysql_select_db('perception', $mysql);
	//mysql_select_db('perception_users', $mysql);
	
	
	if (!$mysql) {
		$GLOBALS['exception'] = "Unable to connect to MySQL user database";		
		return false;
	}
		
	return $mysql;
	
}

//Add user to database
function DAO_AddUser($name, $email, $password)
{		
	$mysql = connectMYSQLI();	
	if (!$mysql) return false;		
			
	//hash passwords before entering
	$md5password = md5($password);
	if (!$mysql = mysql_query("INSERT INTO users (name,email,password)
							VALUES ('$name','$email','$md5password')"))
	{
		$GLOBALS['exception'] = 'Failed to insert user';
		return false;
	}	
		
	//$mysql->close();		
	return true;	
	
}

//Delete user from database, removing all traces.
function DAO_DeleteUser($user_id)
{	
	
	$mysql = connectMYSQLI();
	if (!mysql) return false;
			
	//Delete user record from all relevant records
	
	$result = mysql_query("DELETE FROM users WHERE id='".$user_id."'");
	$result = mysql_query("DELETE FROM user_completions WHERE user_id='".$user_id."'");
	$result = mysql_query("DELETE FROM user_interests WHERE user_id='".$user_id."'");
	$result = mysql_query("DELETE FROM user_reviews WHERE user_id='".$user_id."'");
	$result = mysql_query("DELETE FROM user_ratings WHERE user_id='".$user_id."'"); 
	
	//$mysql->close();	
	return true; 
}

//Verify user exists based on email and hashed password
function DAO_GetUserByLogin($email, $password)
{
	$mysql = connectMYSQLI();
	if (!$mysql) return false;
		
	$result = mysql_query("SELECT name,password FROM users WHERE email='".$email."' AND password='".md5($password)."'");
	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user.";
		return false;
	}	
	
	//If no results, user is not valid. Otherwise, user is valid
	if (mysql_num_rows($result) === 0) {
		return false;
	} else {
		mysql_data_seek($result, 0);
		$row = mysql_fetch_assoc($result);
		
		$user = new User;		
		$user->name = $row['name'];
		$user->password = $row['password'];
		return $user;
	}
}

function DAO_GetUserByName($name)
{

	$mysql = connectMYSQLI();
	if (!$mysql) return false;
	
	$result	 = $mysql = mysql_query("SELECT id,name,email FROM users WHERE name='".$name."'");
	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user.";
		return false;
	}
		
	if (mysql_num_rows($result) === 0) {
		return false;
	} else {				
		mysql_data_seek($result, 0);
		$row = mysql_fetch_assoc($result);
		
		$user = new User;
		$user->id = $row['id'];
		$user->name = $row['name'];
		$user->email = $row['email'];
		return $user;						
	}		
}

function DAO_GetUserByEmail($email)
{	
	
	$mysql = connectMYSQLI();
	if (!$mysql) return false;
		
	$result	= mysql_query("SELECT id,name,email,password FROM users WHERE email='".$email."'");	
	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user.";
		return false;
	}	

	if (mysql_num_rows($result) === 0) {
		return false;
	} else {
		mysql_data_seek($result, 0);
		$row = mysql_fetch_assoc($result);
		
		$user = new User;
		$user->id = $row['id'];
		$user->name = $row['name'];
		$user->email = $row['email'];		
		return $user;		
	}			
}

function DAO_GetUserInterestsByName($user_name)
{
	$user = DAO_GetUserByName($user_name);
	
	if (!$user)
	{
		$GLOBALS['exception'] = "Failed to select user - name resolution.";
		return false;
	}
	
	$mysql = connectMYSQLI();
	if (!$mysql) return false;
	
	$result	= mysql_query("SELECT course_id,course_name FROM user_interests WHERE user_id='".$user->id."'");
	
	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user.";
		return false;
	}
	
	$results = array();
	
	while ($row = mysql_fetch_assoc($result)) {
		$newCourse = new Course;
		$newCourse->ID = $row['course_id'];
		$newCourse->name = $row['course_name'];
		$results[] = $newCourse;
	}

	return $results;
	
}

function DAO_AddInterestToUser($user_id, $course_id, $course_name)
{
	$mysql = connectMYSQLI();
	if (!$mysql) return false;
	
	$result = mysql_query("SELECT user_id FROM user_interests WHERE user_id='".$user_id."' AND course_id='".$course_id."'");
	if (!$result) {
		$GLOBALS['exception'] = "Failed to insert user interest.";
		return false;
	}
	
	//Only insert if no interest exists.
	if (mysql_num_rows($result) !== 0) return true;
	
	if (!$mysql = mysql_query("INSERT INTO user_interests (user_id, course_id, course_name)
								VALUES ('".$user_id."','".$course_id."','".$course_name."')"))
	{
		$GLOBALS['exception'] = 'Failed to insert user interest';
		return false;
	}	
	
	return true;
	
}

function DAO_RemoveInterestFromUser($user_id, $course_id)
{
	$mysql = connectMYSQLI();
	if (!$mysql) return false;
	
	if (!$mysql = mysql_query("DELETE FROM user_interests 
								WHERE user_id='".$user_id."' AND course_id='".$course_id."'"))
	{
		$GLOBALS['exception'] = 'Failed to delete user interest '.$user_id.' '.$course_id;
		return false;
	}
	
	return true;	
}


function DAO_GetUserCompletionsByName($user_name)
{
	$user = DAO_GetUserByName($user_name);
	
	if (!$user)
	{
		$GLOBALS['exception'] = "Failed to select user - name resolution.";
		return false;
	}

	$mysql = connectMYSQLI();
	if (!$mysql) return false;

	$result	= mysql_query("SELECT course_id,course_name,completed FROM user_completions WHERE user_id='".$user->id."'");

	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user.";
		return false;
	}

	$results = array();

	while ($row = mysql_fetch_assoc($result)) {
		$newCourse = new Course;
		$newCourse->ID = $row['course_id'];
		$newCourse->name = $row['course_name'];
		$completed_string = explode(' ',$row['completed']);
		$newCourse->duration = $completed_string[0];
		$results[] = $newCourse;
	}

	return $results;

}

function DAO_AddCompletionToUser($user_id, $course_id, $course_name)
{
	$mysql = connectMYSQLI();
	if (!$mysql) return false;

	$result = mysql_query("SELECT user_id FROM user_completions WHERE user_id='".$user_id."' AND course_id='".$course_id."'");
	if (!$result) {
		$GLOBALS['exception'] = "Failed to insert user completion check.";
		return false;
	}

	//Only insert if no interest exists.
	if (mysql_num_rows($result) !== 0) return true;
	
	
	if (!$mysql = mysql_query("INSERT INTO user_completions (user_id, course_id, course_name, completed)
								VALUES ('".$user_id."','".$course_id."','".$course_name."', NOW())"))
	{
		$GLOBALS['exception'] = 'Failed to insert user completion';
		return false;
	}
	

	return true;

}

function DAO_RemoveCompletionFromUser($user_id, $course_id)
{
	$mysql = connectMYSQLI();
	if (!$mysql) return false;

	if (!$mysql = mysql_query("DELETE FROM user_completions
								WHERE user_id='".$user_id."' AND course_id='".$course_id."'"))
	{
		$GLOBALS['exception'] = 'Failed to delete user completion '.$user_id.' '.$course_id;
		return false;
	}

	return true;
}

function DAO_AddUserTrackingData($user_id, $action, $search_text, $course_id, $material_id)
{
	$mysql = connectMYSQLI();
	if (!$mysql) return false;
	
	if (!$search_text)
		$search_text = '';
	
	if (!$course_id)
		$course_id = '';
	
	if (!$material_id)
		$material_id = '';		
		
	if (!$mysql = mysql_query("INSERT INTO user_log (recorded,user_id,action,search_text,course_id,material_id)
								VALUES (NOW(),'".$user_id."','".$action."','".$search_text."','".$course_id."','".$material_id."')"))
	{
		$GLOBALS['exception'] = 'Failed to insert tracking data';
		return false;
	}
		
	return true;	
}

function DAO_GetUserLogs()
{
	$mysql = connectMYSQLI();
	if (!$mysql) return false;
	
	$result	= mysql_query("SELECT * FROM user_log");

	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user logs.";
		return false;
	}

	$results = array();

	while ($row = mysql_fetch_assoc($result)) {
		$newLog = new Log;
		$newLog->recorded = $row['recorded'];
		$newLog->user_id = $row['user_id'];
		$newLog->action = $row['action'];
		$newLog->search_text = $row['search_text'];
		$newLog->course_id = $row['course_id'];
		$newLog->material_id = $row['material_id'];
		$results[] = $newLog;
	}
	
	return $results;
}

?>
