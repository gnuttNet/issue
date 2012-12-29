<?php
	include("include/cookies.php");

$db_user = new SQLite3("db/tracker.sqlite");

if($_POST['what'] == 'login') {
	$salt = $db_user->querySingle("SELECT salt FROM users WHERE email=\"$_POST[email]\"");
	$hash = sha1($_POST['password'] . $salt );
	$password = $db_user->querySingle("SELECT password from users where email=\"$_POST[email]\"");
	$UID=$db_user->querySingle("SELECT _ROWID_ FROM users WHERE email=\"$_POST[email]\" AND password=\"$hash\"");
	if(!isset($UID)) {
		$_SESSION['ERROR'] = "Wrong username/password";
		$_SESSOIN['RETURN'] = $_POST['return'];
		header("Location: login.php");
		die();
	} else {
		$_SESSION['UID'] = $UID;
		$_SESSION['EMAIL'] = $_POST['email'];
		$_SESSION['ADMIN'] = $db_user->querySingle("SELECT admin FROM users WHERE _ROWID_ = $UID");
		header("Location: $_POST[return]");
		die();
	}

}

if($_POST['what'] == 'postissue') {
	print_r($_POST);
}

die("UNDEFINED");
?>
