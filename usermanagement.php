<?php
include("include/header.php");
$result = $db->query("SELECT email,realname,admin FROM users");
$users = $result->fetchArray(SQLITE3_ASSOC);


print_r($users);
?>

<?php
include("include/footer.php");
?>