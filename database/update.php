<?php
	include("../include/cookies.php");
	include_once( "../include/user.php" );
	include_once( "../include/password_functions.php" );

$db = new SQLite3("../db/tracker.sqlite", SQLITE3_OPEN_READWRITE);

User::Init( $db );
$user = User::GetFromSession();

if($_POST['what'] == 'login') {
	if( User::Login( $_POST['email'], $_POST['password'] ) ) {
		header("Location: {$_POST['return']}");
		die();
	} else {
		$_SESSION['ERROR'] = "Wrong username/password";
		$_SESSION['RETURN'] = $_POST['return'];
		header("Location: ../login.php");
		die();
	}
}

if(!$user->IsLoggedIn()) {
	header("Location: ../login.php");
	die();
}

if($_POST['what'] == 'postissue') {
	$CURRENTTIME = strftime("%s");
	if($_POST['issue'] == "new") {
		$db->exec("INSERT INTO issues(title,description,owner,assigned,createdate,updated) values(\"" . $db->escapeString($_POST['title']) . "\", \"" . $db->escapeString($_POST['description']) . "\", $_SESSION[UID], 0, $CURRENTTIME, $CURRENTTIME)");
		$IID=$db->lastInsertRowID();
		$_SESSION['message']="Created new issue $IID";
	} else {
		$db->exec("UPDATE issues SET title=\"" . $db->escapeString($_POST['title']) . "\", description=\"" . $db->escapeString($_POST['description']) . "\", assigned=$_POST[assigned], updated=$CURRENTTIME, status=$_POST[status] WHERE _ROWID_=$_POST[issue]");
		$IID=$_POST['issue'];
		$_SESSION['message']="Updated issue #$IID at " . strftime("%H:%I:%S", $CURRENTTIME);
	}
	header("Location: ../issue.php?id=$IID");
	die();
}

if($_POST['what'] == 'closeissues') {
	if( isset( $_POST['close'] ) )
	{
		foreach(array_keys($_POST['close']) as $issue) {
			$db->exec("UPDATE issues SET status=2 WHERE _ROWID_=$issue");
		}
	}
			
	header("Location: $_SERVER[HTTP_REFERER]");
	die();
}

if( $_POST["what"] == "changepassword" )
{
	if( $_POST["new_password"] == $_POST["confirm_password"] ) {
		$user = User::GetFromSession();
		$user->ChangePassword( $_POST["old_password"], $_POST["new_password"] );
	}
	else
	{
		$_SESSION['message'] = "Passwords do not match";
	}
	
		
	header("Location: $_SERVER[HTTP_REFERER]");
	die();
}

if( $_POST["what"] == "updateuser" )
{
	$user = User::FromUID( $_POST['id'] );
	if( $user != NULL )
	{
		// @TODO: Cleanse these from SQL-attack possibilities
		$user->SetRealName( $_POST['realname'] );
		$user->SetEmail( $_POST['email'] );
		$asAdmin = isset($_POST['isadmin']);
		$user->SetAsAdmin( $asAdmin );
		$user->CommitChanges();
		
		if( $_POST['new_password'] == $_POST['confirm_password'] )
		{
			$user->SetNewPassword( $_POST['new_password'] );
		}
	}
	else
	{
		$_SESSION['ERROR'] = "Invalid user";
	}

	header("Location: ../usermanagement.php?id={$_POST['id']}");
	die();
}

if( $_POST["what"] == "deleteusers" )
{
	if( isset($_POST['delete']) )
	{
		foreach( array_keys($_POST['delete']) as $userToDelete ) {
			// @TODO: SQL injection (BIG WARNING HERE!)
			User::DeleteFromUID( $userToDelete );
		}
	}

	header("Location: ../usermanagement.php");
	die();
}

if( $_POST["what"] == "adduser" )
{
	
	if( $_POST["email"] != "" && $_POST["realname"] != "" && $_POST["password"] != "" && $_POST["confirm_password"] != "" )
	{
		if( $_POST["password"] == $_POST["confirm_password"] )
		{
			// @TODO: Add a proper query to verify if the email already exists
			if( !User::CreateUser( $_POST["email"], $_POST["realname"], isset($_POST["admin"]), $_POST["password"] ) )
			{
				$_SESSION['ERROR'] = "Email already exists";
			}
		}
		else
		{
			$_SESSION['ERROR'] = "Passwords doesn't match";
		}
	}
	else
	{
		$_SESSION['ERROR'] = "Not all fields set";
	}
	
	$additionalURL = "";
	if( isset($_SESSION['ERROR']) )
	{
		$isAdmin = isset($_POST["admin"]) ? 1 : 0;
		$additionalURL = "?email=".$_POST["email"]."&realname=".$_POST["realname"]."&admin=".$isAdmin;
	}

	header("Location: ../usermanagement.php".$additionalURL);
	die();
}

die("UNDEFINED");
?>
