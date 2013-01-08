<?php

include_once( "input_validation.php" );

error_reporting( E_ALL );

abstract class User
{
	const INVALID_UID = -1;

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
	
		
	// User specific code
	
	abstract function IsLoggedIn();
	
	abstract function IsAdmin();
	abstract function SetAsAdmin( $newAsAdmin );
	
	abstract function GetEmail();
	abstract function SetEmail( $newEmail );
	
	abstract function GetUID();
	
	abstract function GetRealName();
	abstract function SetRealName( $realName );	

	
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
		
	static function FromUID( $UID )	{
		if( !isInt( $UID ) )
		{
			// Possible SQL injection attack (or just someone who tries to use the API incorrectly
			return NULL;
		}
		
		$result = User::$DB->query("SELECT email,realname,admin,_ROWID_ as UID FROM users WHERE _ROWID_ = $UID");
		
		$user = $result->fetchArray(SQLITE3_ASSOC);

		if( $user == FALSE )
		{
			return NULL;
		}

		$dbUser = new RegisteredUser( $user['UID'], $user['email'], $user['admin'] );
		$dbUser->SetRealName( $user['realname'] );
		
		return $dbUser;
	}
	
	abstract function CommitChanges();
	
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
	
	static function Logout() {
		session_destroy();
	}
	
	// User management code
	static function GetAllUsers() {
		$users = array();
	
		$result = User::$DB->query("SELECT email,realname,admin,_ROWID_ as UID FROM users");
		
		while( $user = $result->fetchArray(SQLITE3_ASSOC) ) {
			$dbUser = new RegisteredUser( $user['UID'], $user['email'], $user['admin'] );
			$dbUser->SetRealName( $user['realname'] );
			array_push( $users, $dbUser );
		}
		
		return $users;
	}
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
	
	function SetAsAdmin( $newIsAdmin ) {
		assert( false, "Called SetAsAdmin on GuestUser" );
	}
	
	function GetEmail() {
		return "guest";
	}
	
	function SetEmail( $newEmail ) {
		assert( false, "Called SetEmail on GuestUser" );
	}
	
	function GetRealName() {
		return "guest";
	}
	
	function SetRealName( $realName ) {
		assert( false, "Called SetFullName on GuestUser" );
	}
	
	function GetUID() {
		return User::INVALID_UID;
	}
	
	function CommitChanges() {
		assert( false, "Called CommitChanges on GuestUser" );
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
	
	function SetAsAdmin( $newIsAdmin ) {
		$this->Admin = $newIsAdmin;
	}
	
	function GetEmail() {
		return $this->Email;
	}
	
	function SetEmail( $newEmail ) {
		$this->Email = $newEmail;
	}
	
	function GetUID() {
		return $this->UID;
	}
	
	function SetRealName( $realName ) {
		$this->RealName = $realName;
	}
	
	function GetRealName() {
		return $this->RealName;
	}
	
	function CommitChanges() {
		$isAdmin = $this->Admin ? 1 : 0;
		User::$DB->exec( "UPDATE users SET realname = '$this->RealName', admin = $isAdmin, email = '$this->Email' WHERE _ROWID_=$this->UID" );
	}
}

?>