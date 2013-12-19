<?php

/*
 * Security dependency - Copyright Jonathan Voss
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
if (!defined("_SES_CLASS_")) { die("Dependency failed to load"); }

class Security {
	private $session;
	
	public function __construct ( ) {
		$this->session = new Session();
	}
	
	public function filterText ( $text ) {
		if (empty($text)) { return NULL; }
		return strip_tags($text);
	}
	
	// Make unique algorithm here if you like
	// Just be sure to update the validation method below
	public function makeActionHash ( $action, $timestamp ) {
		return md5($action . hash("sha256", $timestamp) . $session->getInfo());
	}
	
	public function validateActionHash ( $action, $timestamp, $actionhash ) {
		if ($timestamp+600<$this->getTimestamp()) {
			return ($this->makeActionHash($action, $timestamp)==$actionhash);
		}
		return false;
	}
	
	// Make unique algorithm here, as well
	// Just be sure you can replicate this exactly via JavaScript
	// Also, update the JavaScript data hashing algorithm
	public function validateData ( $dataname, $data, $actionhash, $datahash ) {
		$salt = $session->getSalt($dataname);
		return (md5($data . $salt . $actionhash)==$datahash);
	}
	
	// Generates and returns array of unique, random data field salts
	// All salts are single-use session variables
	public function setSalts ( $datanames ) {
		$returnarray = new array();
		foreach ($datanames as $k) {
			$returnarray[$k.'salt'] = $session->setSalt($k);
		}
		return $returnarray;
	}
	
	public function getTimestamp () {
		$d = new DateTime();
		if (phpversion()<5.3) {
			return $d->format('U');
		} else {
			return $d->getTimestamp();
		}
	}
}

define("_SEC_CLASS_", 1);
