<?php
/*	user_module_DAO
	Provides database access functions for the user module
	
*/


//User class. Holds information about users.
class User
{
	public $id;
	public $name;
	public $email;
	public $password;	
}

/*
 * DAO_AddUser($username, $password, $email)
 * DAO_DeleteUser($user_id)
 * DAO_UserIsValid($email, $password)
 * DAO_UserNameOrEmailExists($name,$email)
 */

function connectMYSQLI()
{
	$mysqli = new mysqli("localhost", "hgoh", "", "perception_users");
	
	if ($mysqli->connect_errno) {
		$GLOBALS['exception'] = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;		
		return false;
	}
		
	return $mysqli;
	
}

//Add user to database
function DAO_AddUser($name, $email, $password)
{		
	$mysqli = connectMYSQLI();	
	if (!$mysqli) return false;		
			
	//hash passwords before entering
	$md5password = md5($password);
	if (!$mysqli->query("INSERT INTO users (name,email,password)
							VALUES ('$name','$email','$md5password')"))
	{
		$GLOBALS['exception'] = 'Failed to insert user';
		return false;
	}	
		
	$mysqli->close();		
	return true;	
	
}

//Delete user from database, removing all traces.
function DAO_DeleteUser($user_id)
{	
	
	$mysqli = connectMYSQLI();
	if (!mysqli) return false;
			
	//Delete user record from all relevant records
	if (!$mysqli->query("DELETE FROM users WHERE id='$user_id'") ||
		!$mysqli->query("DELETE FROM user_completions WHERE user_id='$user_id'") ||
		!$mysqli->query("DELETE FROM user_interests WHERE user_id='$user_id'") ||
		!$mysqli->query("DELETE FROM user_reviews WHERE user_id='$user_id'") ||
		!$mysqli->query("DELETE FROM user_ratings WHERE user_id='$user_id'")) 
	{
		$GLOBALS['exception'] = "Failed to delete user.";
		return false;
	}
	
	$mysqli->close();	
	return true; 
}

//Verify user exists based on name and hashed password
function DAO_UserIsValid_Name($name, $password)
{
	
	$mysqli = connectMYSQLI();
	if (!$mysqli) return false;
			
	$result = $mysqli->query("SELECT id FROM users WHERE name='$name' AND password='$password'");	
	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user.";		
		return false;
	}
	
	$mysqli->close();

	//If no results, user is not valid. Otherwise, user is valid
	if ($result->num_rows === 0) {
		return false;
	} else { 
		return true;
	}
}

//Verify user exists based on email and hashed password
function DAO_ValidateLogin($email, $password)
{
	$mysqli = connectMYSQLI();
	if (!$mysqli) return false;
		
	$result = $mysqli->query("SELECT name,password FROM users WHERE email='$email' AND password='$password'");
	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user.";
		return false;
	}
	
	$mysqli->close();
	//If no results, user is not valid. Otherwise, user is valid
	if ($result->num_rows === 0) {
		return false;
	} else {
		$result->data_seek(0);
		$row = $result->fetch_assoc();
		
		$user = new User;		
		$user->name = $row['name'];
		$user->password = $row['password'];
		return $user;
	}
}

function DAO_GetUserByName($name)
{

	$mysqli = connectMYSQLI();
	if (!$mysqli) return false;
	
	$result	 = $mysqli->query("SELECT id,name,email FROM users WHERE name='$name'");
	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user.";
		return false;
	}
	$mysqli->close();
		
	if ($result->num_rows === 0) {
		return false;
	} else {				
		$result->data_seek(0);
		$row = $result->fetch_assoc();
		
		$user = new User;
		$user->id = $row['id'];
		$user->name = $row['name'];
		$user->email = $row['email'];
		return $user;						
	}		
}

function DAO_GetUserByEmail($email)
{	
	
	$mysqli = connectMYSQLI();
	if (!$mysqli) return false;
		
	$result	 = $mysqli->query("SELECT id,name,email,password FROM users WHERE email='$email'");	
	if (!$result) {
		$GLOBALS['exception'] = "Failed to select user.";
		return false;
	}	
	$mysqli->close();

	if ($result->num_rows === 0) {
		return false;
	} else {
		$result->data_seek(0);
		$row = $result->fetch_assoc();
				
		$user = new User;
		$user->id = $row['id'];
		$user->name = $row['name'];
		$user->email = $row['email'];		
		return $user;		
	}			
}





/*
if (!$mysqli->query("DROP TABLE IF EXISTS test") ||
!$mysqli->query("CREATE TABLE test(id INT)") ||
!$mysqli->query("INSERT INTO test(id) VALUES (1), (2), (3)")) {
	echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

$res = $mysqli->query("SELECT id FROM test ORDER BY id ASC");

echo "Reverse order...\n";
for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {
	$res->data_seek($row_no);
	$row = $res->fetch_assoc();
	echo " id = " . $row['id'] . "\n";
}

echo "Result set order...\n";
$res->data_seek(0);
while ($row = $res->fetch_assoc()) {
	echo " id = " . $row['id'] . "\n";
}
*/

?>
