<?php
include("include/header.php");
$result = $db->query("SELECT email,realname,admin FROM users");
while( $user = $result->fetchArray(SQLITE3_ASSOC) ){
}
?>

<?php
include("include/footer.php");
?>