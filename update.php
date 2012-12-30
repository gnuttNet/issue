<?php
	include("include/cookies.php");
	include_once( "include/password_functions.php" );

$db = new SQLite3("db/tracker.sqlite", SQLITE3_OPEN_READWRITE);

if($_POST['what'] == 'login') {
	$salt = $db->querySingle("SELECT salt FROM users WHERE email=\"$_POST[email]\"");
	$hash = sha1($_POST['password'] . $salt );
	$password = $db->querySingle("SELECT password from users where email=\"$_POST[email]\"");
	$UID=$db->querySingle("SELECT _ROWID_ FROM users WHERE email=\"$_POST[email]\" AND password=\"$hash\"");
	if(!isset($UID)) {
		$_SESSION['ERROR'] = "Wrong username/password";
		$_SESSION['RETURN'] = $_POST['return'];
		header("Location: login.php");
		die();
	} else {
		$_SESSION['UID'] = $UID;
		$_SESSION['EMAIL'] = $_POST['email'];
		$_SESSION['ADMIN'] = $db->querySingle("SELECT admin FROM users WHERE _ROWID_ = $UID");
		header("Location: $_POST[return]");
		die();
	}

}

if(!isset($_SESSION['UID'])) {
	header("Location: login.php");
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
	header("Location: issue.php?id=$IID");
	die();
}

if($_POST['what'] == 'closeissues') {
	foreach(array_keys($_POST['close']) as $issue) {
		$db->exec("UPDATE issues SET status=2 WHERE _ROWID_=$issue");
	}
	header("Location: $_SERVER[HTTP_REFERER]");
	die();
}

if( $_POST["what"] == "changepassword" )
{
	if( $_POST["old_password"] != "" && $_POST["new_password"] != "" && $_POST["confirm_password"] != "" )
	{
		if( $_POST["new_password"] == $_POST["confirm_password"] )
		{
			$result = $db->query("SELECT password,salt FROM users WHERE email=\"".$_SESSION['EMAIL']."\"");
			$passwordSalt = $result->fetchArray(SQLITE3_ASSOC);
			$oldPassword = sha1($_POST["old_password"].$passwordSalt['salt']);
			if( $passwordSalt['password'] == $oldPassword )
			{
				$newPassword = sha1($_POST["new_password"].$passwordSalt['salt']);
				$db->query("UPDATE users SET password=\"".$newPassword."\" WHERE email=\"".$_SESSION['EMAIL']."\"");
				$_SESSION['message'] = "Password updated";
			}
			else
			{
				$_SESSION['message'] = "Old password doesn't match";
			}
		}
		else
		{
			$_SESSION['message'] = "Passwords do not match";
		}
	}
	else
	{
		$_SESSION['message'] = "Not all fields set";
	}
	
	header("Location: $_SERVER[HTTP_REFERER]");
	die();
}

die("UNDEFINED");
?>
