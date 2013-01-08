<?php
	include("../include/cookies.php");
	include_once( "../include/user.php" );
	include_once( "../include/password_functions.php" );

$db = new SQLite3("../db/tracker.sqlite", SQLITE3_OPEN_READWRITE);

User::Init( $db );
$user = User::GetUserFromSession();

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
	foreach(array_keys($_POST['close']) as $issue) {
		$db->exec("UPDATE issues SET status=2 WHERE _ROWID_=$issue");
	}
	header("Location: $_SERVER[HTTP_REFERER]");
	die();
}

if( $_POST["what"] == "changepassword" )
{
	User::ChangePassword( $_POST["old_password"], $_POST["new_password"], $_POST["confirm_password"] );
		
	header("Location: $_SERVER[HTTP_REFERER]");
	die();
}

die("UNDEFINED");
?>
