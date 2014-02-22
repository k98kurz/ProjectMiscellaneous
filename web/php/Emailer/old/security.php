<?php

if (!defined("_CONST_")) { die("Unauthorized access"); }
if (!defined("_SES_CLASS_")) { die("Unauthorized access"); }

class Security {
	private $session;
	
	public function __construct ( ) {
		$this->session = new Session();
	}
	
	public function filterText ( $text ) {
		if (empty($text)) { return NULL; }
		return strip_tags($text);
	}
	
	public function makeActionHash ( $action, $timestamp ) {
		return md5($action . hash("sha256", $timestamp) . $session->getInfo());
	}
	
	public function validateActionHash ( $action, $timestamp, $actionhash ) {
		if ($timestamp+600>$this->getTimestamp()) {
			return ($this->makeActionHash($action, $timestamp)==$actionhash);
		}
		return false;
	}
	
	public function validateData ( $dataname, $data, $actionhash, $datahash ) {
		$salt = $session->getSalt($dataname);
		return (md5($data . $salt . $actionhash)==$datahash);
	}
	
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
