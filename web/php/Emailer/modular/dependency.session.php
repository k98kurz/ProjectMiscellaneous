<?php

/*
 * Session dependency - Copyright Jonathan Voss
 * Developer email: k98kurz@gmail.com
 * License: NOT for use in commercial projects without my permission. Commercial
 * 			licenses are granted per specific application/website.
 * 			Distributing site-specific versions can cause security vulnerabilities.
 * 			Modifying this may cause security vulnerabilities or loss of functionality.
 * 			I am not in any way responsible under the above circumstances.
 * 
 * 	For other cool, free & open source software projects,
 * 	see my public repository: https://github.com/k98kurz/ProjectMiscellaneous
 * 	To distribute, please refer to my repository.
 * 	I will also use modified versions of this system for commercial projects,
 * 	crafting the security algorithms for each application.
*/

if (!defined("_CONST_")) { die("Unauthorized access"); }
if (!defined("_CONFIG_")) { die("Configuration data failed to load"); }

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
	
	// Do not touch this
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
	
	// Nor this
	public function setSalt ( $dataname ) {
		$salt = $this->makeToken();
		$this->set('salt'.$dataname, $salt);
		return $salt;
	}
	
	private function set ( $key, $value ) {
		if (is_string($key)&&is_string($value)) {
			$_SESSION[$key] = $value;
		}
	}
	
	// Responsible for making salts and the like
	// You can touch this, but keep it random
	// Make a more unique algorithm here, if you like
	private function makeToken () {
		$token = array();
		for ($i=0; $i<32; $i++) {
			array_push( $token, chr( mt_rand(32, 126)) );
		}
		return md5(implode($token));
	}
	
}

define("_SES_CLASS_", 1);
