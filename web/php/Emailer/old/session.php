<?php

if (!defined("_CONST_")) { die("Unauthorized access"); }

class Session {
	
	public function __construct () {
		session_start();
		if (empty($_SESSION['addr'])) { $_SESSION['addr'] = $_SERVER['REMOTE_ADDR']; }
		if (empty($_SESSION['token'])) { $_SESSION['token'] = $this->makeToken(); }
	}
	
	public function isValid () {
		if (!isset($_SESSION['addr'], $_SESSION['token'])) { return false; }
		return ($_SESSION['addr'] == $_SERVER['REMOTE_ADDR']) ? true : false;
	}
	
	public function getSalt ($dataname) {
		if (!empty($_SESSION['salt'.$dataname])) {
			$s = $_SESSION['salt'.$dataname];
			unset($_SESSION['salt'.$dataname]);
			return $s;
		}
		return null;
	}
	
	public function getInfo () {
		return $_SESSION['addr'] . $_SERVER['REMOTE_ADDR'] . $_SESSION['token'];
	}
	
	public function get ( $key ) {
		if (!is_string($key)) { return ""; }
		return (!empty($_SESSION[$key])) ? $_SESSION[$key] : "";
	}
	
	public function setSalt ( $dataname ) {
		$salt = $this->makeToken();
		$this->set($dataname.'salt', $salt);
		return $salt;
	}
	
	private function set ( $key, $value ) {
		if (is_string($key)&&is_string($value)) {
			$_SESSION[$key] = $value;
		}
	}
	
	private function makeToken () {
		$token = array();
		for ($i=0; $i<32; $i++) {
			array_push( $token, chr( mt_rand(32, 126)) );
		}
		return md5(implode($token));
	}
	
}

define("_SES_CLASS_", 1);
