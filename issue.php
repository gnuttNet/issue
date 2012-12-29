<?php include("include/header.php");?>
		<h1>Issue</h1>
		<form action="update.php" method="post">
			<input type="hidden" name="what" value="postissue" />
			<label for="title">Title:</label><input type="text" name="title" /><br />
			<label for="description">Description:</label><textarea name="description" rows="25" cols="80"></textarea><br />
			<input type="submit" value="Post" />
		</form>
<?php include("include/footer.php");?>
