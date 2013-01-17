<?php include("include/header.php"); ?>
		<h1>Login</h1>
<?php
	if(isset($_SESSION['ERROR'])) {
		echo "\t\t\t<p class=\"error\">{$_SESSION['ERROR']}</p>\n";
		unset($_SESSION['ERROR']);
	} 
?>
		<form action="database/update.php" method="post" />
			<input type="hidden" name="what" value="login" />
			<input type="hidden" name="return" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
			Email: <input type="text" name="email" />
			Password: <input type="password" name="password" />
			<input type="submit" value="Login" />
		</form>
<?php include("include/footer.php"); ?>
