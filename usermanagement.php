<?php
	include("include/header.php");
	include_once("include/functions.php");

	if( isset($_SESSION['ERROR']) ) {
		echo "\t\t<p>{$_SESSION['ERROR']}</p>\n";
		unset($_SESSION['ERROR']);
	}
	
	if( !isset($_GET['id']) ) {
		echo "\t\t<h1>User management</h1>\n";
		echo "\t\t<form action=\"database/update.php\" method=\"post\">\n";
		echo "\t\t\t<input type=\"hidden\" name=\"what\" value=\"deleteusers\" />\n";
		echo "\t\t\t<input type=\"submit\" value=\"Delete\" />\n";
		echo "\t\t\t<table>\n";
		echo "\t\t\t\t<tr>\n";
		echo "\t\t\t\t\t<th></th>\n";
		echo "\t\t\t\t\t<th>User #</th>\n";
		echo "\t\t\t\t\t<th>Email</th>\n";
		echo "\t\t\t\t\t<th>Full Name</th>\n";
		echo "\t\t\t\t\t<th>Admin</th>\n";
		echo "\t\t\t\t</tr>\n";
		$users = User::GetAllUsers();
		
		foreach( $users as $dbUser ) {
			$isAdminString = $dbUser->IsAdmin() ? "Yes" : "No";
			echo "\t\t\t\t<tr>\n";
			echo "\t\t\t\t\t<td><input type=\"checkbox\" name=\"delete[{$dbUser->GetUID()}]\" /></td>\n";
			echo "\t\t\t\t\t<td>{$dbUser->GetUID()}</td>\n";
			echo "\t\t\t\t\t<td><a href=\"usermanagement.php?id={$dbUser->GetUID()}\">{$dbUser->GetEmail()}</a></td>\n";
			echo "\t\t\t\t\t<td>{$dbUser->GetRealName()}</td>\n";
			echo "\t\t\t\t\t<td>{$isAdminString}</td>\n";
			echo "\t\t\t\t</tr>\n";
		}
		echo "\t\t\t</table>\n";
		echo "\t\t</form>\n";
		echo "\t\t<h2>Add new user</h2>\n";
		
		$presetEmail = getPresetString( $_GET, 'email' );
		$presetRealName = getPresetString( $_GET, 'realname' );
		$presetAdmin = isset($_GET["admin"]) && $_GET["admin"] == "1" ? "checked=\"checked\"" : "";

		echo "\t\t<form action=\"database/update.php\" method=\"post\">\n";
		echo "\t\t\t<input type=\"hidden\" name=\"what\" value=\"adduser\" />\n";
		echo "\t\t\t<label for=\"email\">Email:</label><input type=\"text\" name=\"email\" value=\"{$presetEmail}\" /><br />\n";
		echo "\t\t\t<label for=\"realname\">Real Name:</label><input type=\"text\" name=\"realname\" value=\"{$presetRealName}\" /><br />\n";
		echo "\t\t\t<label for=\"password\">Password:</label><input type=\"password\" name=\"password\" value=\"\" /><br />\n";
		echo "\t\t\t<label for=\"confirm_password\">Confirm password:</label><input type=\"password\" name=\"confirm_password\" value=\"\" /><br />\n";
		
		echo "\t\t\t<label for=\"admin\">Is Admin:</label><input type=\"checkbox\" name=\"admin\" {$presetAdmin} /><br />\n";
		echo "\t\t\t<input type=\"submit\" value=\"Add new user\" />\n";
		echo "\t\t</form>\n";
	} else if( $currentUser = User::FromUID( $_GET['id'] ) ) {
		$realName = $currentUser->GetRealName();
		if( $realName == "" ) {
			$realName = $currentUser->GetEmail();
		}
		echo "\t\t<h1>{$realName}</h1>\n";
		echo "\t\t<form action=\"database/update.php\" method=\"post\">\n";
		echo "\t\t\t<input type=\"hidden\" name=\"what\" value=\"updateuser\" />\n";
		echo "\t\t\t<input type=\"hidden\" name=\"id\" value=\"{$currentUser->GetUID()}\" />\n";
		echo "\t\t\t<label for=\"realname\">Real Name:</label><input type=\"text\" name=\"realname\" value=\"{$currentUser->GetRealName()}\" /><br />\n";
		echo "\t\t\t<label for=\"email\">Email:</label><input type=\"text\" name=\"email\" value=\"{$currentUser->GetEmail()}\" /><br />\n";
		echo "\t\t\t<label for=\"isadmin\">Is Admin:</label><input type=\"checkbox\" name=\"isadmin\"".($currentUser->IsAdmin() ? "checked=\"checked\"" : "")."/><br />\n";
		echo "\t\t\t<label for=\"new_password\">New password:</label><input type=\"password\" name=\"new_password\" /> <br />\n";
		echo "\t\t\t<label for=\"confirm_password\">Confirm password:</label><input type=\"password\" name=\"confirm_password\" /> <br />\n";
		echo "\t\t\t<input type=\"submit\" value=\"Update user\" />\n";
		echo "\t\t</form>\n";
	} else {
		echo "\t\t<h1>No user with the specified ID</h1>\n";
	}
include("include/footer.php");
?>