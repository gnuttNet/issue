<?php
include( "include/header.php" );
include_once( "include/password_functions.php" );

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
				$message = "Password updated";
			}
			else
			{
				$message = "Old password doesn't match";
			}
		}
		else
		{
			$message = "Passwords do not match";
		}
	}
	else
	{
		$message = "Not all fields set";
	}
}
?>
		<h1>User settings</h1>
<?php
	if( isset( $message ) )
	{
		echo "		<p>".$message."</p>\n";
	}
?>
		<form action="usersettings.php" method="post">
			<input type="hidden" name="what" value="changepassword" />
			<label for="old_password">Old password:</label><input type="password" name="old_password" size="40" /> <br />
			<label for="new_password">New password:</label><input type="password" name="new_password" size="40" /> <br />
			<label for="confirm_password">Confirm password:</label><input type="password" name="confirm_password" size="40" /> <br />
			<input type="submit" value="Change password" />
		</form>
<?php
include( "include/footer.php" );
?>