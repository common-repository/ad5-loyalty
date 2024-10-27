<?php
/**
 * Authentication and sign-in in front
 * 
 * @category Module
 * @package AD5
 * @author AD5
 */

class AD5_Auth
{
	const ERROR_EMAIL_NOT_EXISTS = 'ERROR_EMAIL_NOT_EXISTS';
	const ERROR_NO_PERMISSION = 'ERROR_NO_PERMISSION';
	const ERROR_PASSWORD_FAILED = 'ERROR_PASSWORD_FAILED';
	const ERROR_UNKNOWN = 'ERROR_UNKNOWN';
	
	private $user = null;
	private $error = null;

	/**
	 * __construct
	 */
	public function __construct()
	{
		$user = wp_get_current_user();
		if ( $user ) {
			$this->user = $user;
		} else {
			$this->user = null;
		}
	}

	/**
	 * return user info
	 * @return WP_User $user
	 */
	public function get_user() {
		return $this->user ? $this->user : null;
	}

	/**
	 * return user id
	 * @return int $user_id
	 */
	public function get_userid() {
		return $this->user ? $this->user->data->ID : null;
	}

	/**
	 * do sign in
	 * @return boolean
	 */
	public function sign_in( $email, $password )
	{
		$this->error = null;
		$user = get_user_by( 'email', $email );
		if ( false === $user ) {
			$this->error = self::ERROR_EMAIL_NOT_EXISTS;
			return false;
		} elseif ( false ) {
			$this->error = self::ERROR_NO_PERMISSION;
			return false;
		} else {
			$creds = array();
			$creds['user_login'] = $user->data->user_login;
			$creds['user_password'] = $password;
			$creds['remember'] = true;
			$user = wp_signon( $creds, false );
			if ( is_wp_error( $user ) ) {
				if ( array_key_exists( 'incorrect_password', $user->errors ) ) {
					$this->error = self::ERROR_PASSWORD_FAILED;
				} else {
					$this->error = self::ERROR_UNKNOWN;
				}
				$this->user = null;
				return false;
			} else {
				$this->user = $user;
				return true;
			}
		}
	}

	/**
	 * return error info
	 * @return string $error
	 */
	public function get_error()
	{
		return $this->error;
	}

	/**
	 * check if signed in
	 * @return boolean
	 */
	public function is_author_signed_in()
	{
		if ( $this->user ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * check if signed in (static)
	 * @return boolean
	 */
	static public function is_author_logged_in()
	{
		$user = wp_get_current_user();
		if ( ! empty( $user->roles ) && in_array( FS_Const::AUTHOR_ROLE, $user->roles ) ) {
			return true;
		} else {
			return false;
		}
	}
}