<?php
include( "include/header.php" );
?>
		<h1>User settings</h1>
<?php
	if( isset( $_SESSION['message'] ) )
	{
		echo "		<p>".$_SESSION['message']."</p>\n";
		unset( $_SESSION['message'] );
	}
?>
		<form action="database/update.php" method="post">
			<input type="hidden" name="what" value="changepassword" />
			<label for="old_password">Old password:</label><input type="password" name="old_password" size="40" /> <br />
			<label for="new_password">New password:</label><input type="password" name="new_password" size="40" /> <br />
			<label for="confirm_password">Confirm password:</label><input type="password" name="confirm_password" size="40" /> <br />
			<input type="submit" value="Change password" />
		</form>
<?php
include( "include/footer.php" );
?>