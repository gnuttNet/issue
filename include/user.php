<?php
 
// @TODO: Remove debug line
include_once("cookies.php");
error_reporting( E_ALL );

abstract class User
{
	// The names of the session variables used by the user object
	
	// Stores a unique identifier of the user
	const SESSION_UID = 'UID';
	// Stores the current users email
	const SESSION_EMAIL = 'EMAIL';
	// Stores if the current user is admin
	const SESSION_ADMIN = 'ADMIN';
	
	static protected $DB;
	static protected $CachedUser;
	
	// Needs to be called before any functions will work
	// Should really refactor this
	static function Init( $db ) {
		User::$DB = $db;
	}
	
	protected function __construct() {
	}
	
	static function GetUserFromSession() {
		if( isset($_SESSION[self::SESSION_UID]) ) {
			if( !isset( $CachedUser ) )
			{
				User::$CachedUser = new RegisteredUser( $_SESSION[self::SESSION_UID], $_SESSION[self::SESSION_EMAIL], $_SESSION[self::SESSION_ADMIN] );
			}
			return User::$CachedUser;			
		}
		
		return new GuestUser();
	}
	
	static function Login( $email, $password ) {
		$email = strtolower( $email );
		$salt = self::$DB->querySingle("SELECT salt FROM users WHERE email=\"$email\"");
		$hash = sha1( $password . $salt );
		$password = User::$DB->querySingle("SELECT password from users where email=\"$email\"");
		$UID = User::$DB->querySingle("SELECT _ROWID_ FROM users WHERE email=\"$email\" AND password=\"$hash\"");
		if(isset($UID)) {
			User::UserLoggedIn( $UID, $email );
			
			return true;
		}
		
		return false;
	}
	
	// Called whenever a user has logged in
	static function UserLoggedIn( $UID, $email ) {
		$_SESSION['UID'] = $UID;
		$_SESSION['EMAIL'] = $email;
		$_SESSION['ADMIN'] = User::$DB->querySingle("SELECT admin FROM users WHERE _ROWID_ = $UID");
	}
	
	// @TODO - refactor, as it can only be done on the current logged in user, and not on any user
	static function ChangePassword( $oldPassword, $newPassword, $confirmPassword ) {
		if( $oldPassword != "" && $newPassword != "" && $confirmPassword != "" )
		{
			if( $newPassword == $confirmPassword )
			{
				$result = User::$DB->query("SELECT password,salt FROM users WHERE email=\"".$_SESSION[User::SESSION_EMAIL]."\"");
				$passwordSalt = $result->fetchArray(SQLITE3_ASSOC);
				$oldHashedPassword = sha1( $oldPassword.$passwordSalt['salt'] );
				if( $passwordSalt['password'] == $oldHashedPassword )
				{
					$newHashedPassword = sha1( $newPassword.$passwordSalt['salt'] );
					User::$DB->query("UPDATE users SET password=\"".$newHashedPassword."\" WHERE email=\"".$_SESSION[User::SESSION_EMAIL]."\"");
					$_SESSION['message'] = "Password updated";
					
					return true;
				}
				else
				{
					$_SESSION['message'] = "Old password doesn't match";
				}
			}
			else
			{
				$_SESSION['message'] = "Passwords do not match";
			}
		}
		else
		{
			$_SESSION['message'] = "Not all fields set";
		}
		
		return false;
	}
	
	static function Logout()
	{
		session_destroy();
	}
	
	abstract function IsLoggedIn();
	
	abstract function IsAdmin();
	
	abstract function GetEmail();
	
	abstract function GetUID();
}

class GuestUser extends User
{
	protected function __construct() {
		parent::__construct();
	}
	
	function IsLoggedIn() {
		return false;
	}
	
	function IsAdmin() {
		return false;
	}
	
	function GetEmail() {
		return "guest";
	}
	
	function GetUID() {
		return -1;
	}
}

class RegisteredUser extends User
{
	private $Email;
	private $RealName;
	private $Admin;
	private $UID;
	
	protected function __construct( $uid, $email, $admin ) {
		parent::__construct();
		
		$this->UID = $uid;
		$this->Email = $email;
		$this->Admin = $admin;
		
		//echo "Creating user";
		//print_r($this);
	}
	
	function __destruct() {
		//echo "Destroying user";
		//print_r($this);
	}
	
	function IsLoggedIn() {
		return true;
	}
	
	function IsAdmin() {
		return $this->Admin;
	}
	
	function GetEmail() {
		return $this->Email;
	}
	
	function GetUID() {
		return $this->UID;
	}
}

?>