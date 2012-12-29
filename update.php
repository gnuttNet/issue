<?php
	include("include/cookies.php");

$db = new SQLite3("db/tracker.sqlite", SQLITE3_OPEN_READWRITE);

if($_POST['what'] == 'login') {
	$salt = $db->querySingle("SELECT salt FROM users WHERE email=\"$_POST[email]\"");
	$hash = sha1($_POST['password'] . $salt );
	$password = $db->querySingle("SELECT password from users where email=\"$_POST[email]\"");
	$UID=$db->querySingle("SELECT _ROWID_ FROM users WHERE email=\"$_POST[email]\" AND password=\"$hash\"");
	if(!isset($UID)) {
		$_SESSION['ERROR'] = "Wrong username/password";
		$_SESSOIN['RETURN'] = $_POST['return'];
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

if($_POST['what'] == 'postissue') {
	if($_POST['issue'] == "new") {
		$CURRENTTIME = strftime("%s");
		$db->exec("INSERT INTO issues(title,description,owner,assigned,createdate,updated) values(\"" . $db->escapeString($_POST['title']) . "\", \"" . $db->escapeString($_POST['description']) . "\", $_SESSION[UID], -1, $CURRENTTIME, $CURRENTTIME)");
		$IID=$db->lastInsertRowID();
	} else {
		$IID=$_POST['issue'];
		die("NEIN");
	}
	header("Location: issue.php?id=$IID");
	die();
}

die("UNDEFINED");
?>
