<?php include_once("settings.php");?>
<?php include_once("cookies.php");?>
<html>
	</head>
		<title><?php echo $TRACK_TITLE?></title>
		<link rel="StyleSheet" href="include/styelsheet.css" type="text/css" />
	</head>
	<body>
	<div id="header">
<?php
	if(!isset($_SESSION['UID'])) {
		echo "<a href=\"login.php\">Login</a>";
	} else {
		echo "<a href=\"issue.php?id=new\">[+] New Issue</a> | ";
		echo $_SESSION['EMAIL'];
		echo "| <a href=\"logout.php\">Logout</a>";
	}
?>
	</div>
