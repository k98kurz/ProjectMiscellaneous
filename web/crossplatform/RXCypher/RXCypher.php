<?php

if (!defined("_CONST_")) { die("Unauthorized access"); }

class RXCypher {
	
	public function __construct ( ) { }
	
	public function encrypt ( $plaintext, $key ) {
		if (!isset($plaintext) || gettype($plaintext)!="string") { return NULL; }
		if (!isset($key) || gettype($key)!="string") { return NULL; }
		$key = str_split($key);
		for ($i=0; $i<sizeof($key); $i++) {
			$key[$i] = ord($key[$i]);
		}
		$plaintext = str_split($plaintext);
		$cyphertext = array();
		for ($i=0; $i<sizeof($plaintext); $i++) {
			$plaintext[$i] = ord($plaintext[$i]);
		}
		for ($i=0, $k=0; $i<sizeof($plaintext); $i++, $k++) {
			if ($k==sizeof($key)) { $k = 0; }
			array_push($cyphertext, chr($this->enc($plaintext[$i], $key[$k])));
		}
		return implode($cyphertext);
	}
	
	public function decrypt ( $cyphertext, $key ) {
		if (!isset($cyphertext) || gettype($cyphertext)!="string") { return NULL; }
		$cyphertext = str_split($cyphertext);
		$plaintext = array();
		for ($i=0; $i<sizeof($cyphertext); $i++) {
			$cyphertext[$i] = ord($cyphertext[$i]);
		}
		for ($i=0, $k=0; $i<sizeof($cyphertext); $i++, $k++) {
			if ($k==sizeof($key)) { $k = 0; }
			array_push($plaintext, chr($this->dec($cyph[$i], $key[$k])));
		}
		return implode($plaintext);
	}
	
	private function enc ( $c, $k ) {
		return ($c + $k > 255) ? ($c + $k - 255) ^ $k : ($c + $k) ^ $k;
	}
	
	private function dec ( $c, $k ) {
		return (($c ^ $k) - $k < 0) ? ($c ^ $k) - $k + 255 : ($c ^ $k) - $k;
	}
	
}

?>
