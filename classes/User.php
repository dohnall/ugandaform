<?php

class User {

	public function __construct($user_id) {
		$this->user_id = $user_id;
	}

	public static function login($username, $passwd, $adminOnly = false) {
		$ret = dibi::select('user_id')->from(DBPREF.'user')
				   ->where('username = %s', $username)
				   ->where('passwd = %s', hash('sha512', md5($passwd)))
				   ->where('status = %s', 'Y');
		if($adminOnly === true) {
			$ret = $ret->where('admin = %s', 'Y');
		}
		return $ret->fetchSingle();
	}

	public static function logLogin($values) {
		dibi::insert(DBPREF."login", $values)->execute();
	}

	public static function isUsername($username, $id = 0) {
		$query = "SELECT user_id FROM ".DBPREF."user WHERE username = %s AND user_id <> %i";
		$return =  dibi::query($query, $username, $id)->fetchSingle();
		return $return ? true : false;
	}

	public static function isEmail($email, $id = 0) {
		$query = "SELECT user_id FROM ".DBPREF."user WHERE email = %s AND user_id <> %i";
		$return =  dibi::query($query, $email, $id)->fetchSingle();
		return $return ? true : false;
	}

	public static function getUserByUsername($username, $status = '') {
		if($status) {
			$query = "SELECT user_id FROM ".DBPREF."user WHERE username = %s AND status = %s";
			$return =  dibi::query($query, $username, $status)->fetchSingle();
		} else {
			$query = "SELECT user_id FROM ".DBPREF."user WHERE username = %s";
			$return =  dibi::query($query, $username)->fetchSingle();
		}
		return $return ? $return : false;
	}

	public static function getUserByHash($hash, $verification = false) {
		$status = $verification === true ? 'W' : 'Y';
		$query = "SELECT user_id
				  FROM ".DBPREF."user
				  WHERE MD5(CONCAT_WS('_', user_id, username)) = %s AND
				  		status = %s
				  LIMIT 0, 1";
		$user_id = dibi::query($query, $hash, $status)->fetchSingle();
		return $user_id ? $user_id : false;
	}

	public static function getUserByForgotHash($hash) {
		$query = "SELECT user_id
				  FROM ".DBPREF."user
				  WHERE forgot = %s AND
				  		forgot_timestamp > NOW() - INTERVAL 1 DAY
				  LIMIT 0, 1";
		$user_id = dibi::query($query, $hash)->fetchSingle();
		return $user_id ? $user_id : false;
	}

	public function getData() {
		$query = "SELECT * FROM ".DBPREF."user WHERE user_id = %i";
		$return = dibi::query($query, $this->user_id)->fetch();
		return $return;
	}

	public function isAdmin() {
		$query = "SELECT admin FROM ".DBPREF."user WHERE user_id = %i";
		$return = dibi::query($query, $this->user_id)->fetchSingle();
		return $return == 'Y' ? true : false;
	}

}
