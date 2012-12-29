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
	$CURRENTTIME = strftime("%s");
	if($_POST['issue'] == "new") {
		$db->exec("INSERT INTO issues(title,description,owner,assigned,createdate,updated) values(\"" . $db->escapeString($_POST['title']) . "\", \"" . $db->escapeString($_POST['description']) . "\", $_SESSION[UID], 0, $CURRENTTIME, $CURRENTTIME)");
		$IID=$db->lastInsertRowID();
	} else {
		$db->exec("UPDATE issues SET title=\"" . $db->escapeString($_POST['title']) . "\", description=\"" . $db->escapeString($_POST['description']) . "\", assigned=$_POST[assigned], updated=$CURRENTTIME WHERE _ROWID_=$_POST[issue]");
		$IID=$_POST['issue'];
	}
	header("Location: issue.php?id=$IID");
	die();
}

die("UNDEFINED");
?>
