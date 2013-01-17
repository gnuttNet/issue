<?php

include_once( "input_validation.php" );
include_once( "password_functions.php" );

error_reporting( E_ALL );

abstract class User {
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

	
	static function GetFromSession() {
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
	
	// @TODO: Add support for generating passwords
	static function CreateUser( $email, $realName, $admin, $password )
	{
		$isAdmin = $admin ? 1 : 0;
		$salt = generateRandomString( 8 );
		$hashedPassword = sha1( $password.$salt );
		
		// suppress the error message and just return false if the is any issues
		return @User::$DB->exec( "INSERT INTO users VALUES( '{$email}' ,'{$realName}','{$hashedPassword}','{$salt}', $isAdmin )" );
	}
	
	static function DeleteFromUID( $UID ) {
		User::$DB->exec("DELETE FROM users WHERE _ROWID_=$UID");
	}
	
	abstract function CommitChanges();
	
	// Called whenever a user has logged in
	static function UserLoggedIn( $UID, $email ) {
		$_SESSION[User::SESSION_UID] = $UID;
		$_SESSION[User::SESSION_EMAIL] = $email;
		$_SESSION[User::SESSION_ADMIN] = User::$DB->querySingle("SELECT admin FROM users WHERE _ROWID_ = $UID");
	}
	
	static function UpdateSessionWithUser( $user ) {
		$_SESSION[User::SESSION_UID] = $user->GetUID();
		$_SESSION[User::SESSION_EMAIL] = $user->GetEmail();
		$_SESSION[User::SESSION_ADMIN] = $user->IsAdmin();
	}
	
	abstract function ChangePassword( $oldPassword, $newPassword );
	
	abstract function SetNewPassword( $newPassword );
	
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

class GuestUser extends User {
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
	
	function SetNewPassword( $newPassword ) {
		assert( false, "Called SetNewPassword on GuestUser" );
	}
	
	function ChangePassword( $oldPassword, $newPassword ) {
		assert( false, "Called ChangePassword on GuestUser" );
	}
}

class RegisteredUser extends User {
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
	
	function SetNewPassword( $newPassword ) {
		$salt = generateRandomString( 8 );
		$newHashedPassword = sha1( $newPassword.$salt );
		User::$DB->exec("UPDATE users SET password=\"".$newHashedPassword."\",salt=\"".$salt."\" WHERE _ROWID_=\"".$this->UID."\"");
	}
	
	function ChangePassword( $oldPassword, $newPassword ) {
		$result = User::$DB->query("SELECT password,salt FROM users WHERE _ROWID_=\"".$this->UID."\"");
		$passwordSalt = $result->fetchArray(SQLITE3_ASSOC);
		$oldHashedPassword = sha1( $oldPassword.$passwordSalt['salt'] );
		if( $passwordSalt['password'] == $oldHashedPassword ) {
			$newHashedPassword = sha1( $newPassword.$passwordSalt['salt'] );
			User::$DB->query("UPDATE users SET password=\"".$newHashedPassword."\" WHERE _ROWID_=\"".$this->UID."\"");
			$_SESSION['message'] = "Password updated";
			
			return true;
		}
		else
		{
			$_SESSION['message'] = "Old password doesn't match";
		}
		
		return false;
	}
		
	function CommitChanges() {
		if( User::GetFromSession()->GetUID() == $this->UID )
		{
			User::UpdateSessionWithUser( $this );
		}
	
		$isAdmin = $this->Admin ? 1 : 0;
		User::$DB->exec( "UPDATE users SET realname = '$this->RealName', admin = $isAdmin, email = '$this->Email' WHERE _ROWID_=$this->UID" );
	}
}

?>