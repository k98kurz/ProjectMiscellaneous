<?php

if (!defined("_CONST_")) { die("Unauthorized access"); }

class RXCypher {
	
	private $password = array();
	
	public function __construct ( $pass ) {
		if (!isset($pass)) { return NULL; }
		$this->password = str_split($pass);
		for ($i=0; $i<sizeof($this->password); $i++) {
			$this->password[$i] = ord($this->password[$i]);
		}
	}
	
	public function encrypt ( $plainText ) {
		if (!isset($plainText) || gettype($plainText)!="string") { return NULL; }
		$plain = str_split($plainText);
		$cypherText = array(); $temp = 0;
		for ($i=0; $i<sizeof($plain); $i++) {
			$plain[$i] = ord($plain[$i]);
		}
		for ($i=0, $k=0; $i<sizeof($plain); $i++, $k++) {
			if ($k==sizeof($this->password)) { $k = 0; }
			$temp = $this->enc( $plain[$i], $this->password[$k] );
			array_push($cypherText, chr($temp));
		}
		return implode($cypherText);
	}
	
	public function decrypt ( $cypherText ) {
		if (!isset($cypherText) || gettype($cypherText)!="string") { return NULL; }
		$cyph = str_split($cypherText);
		$plainText = array(); $temp = 0;
		for ($i=0, $k=0; $i<sizeof($cyph); $i++, $k++) {
			if ($k==sizeof($this->password)) { $k = 0; }
			$temp = $this->dec( $cyph[$i], $this->password[$k] );
			array_push($plainText, chr($temp));
		}
		return implode($plainText);
	}
	
	private function enc ( $c, $k ) {
		return ($c + $k > 255) ? ($c + $k - 255) ^ $k : ($c + $k) ^ $k;
	}
	
	private function dec ( $c, $k ) {
		return (($c ^ $k) - $k < 0) ? ($c ^ $k) - $k + 255 : ($c ^ $k) - $k;
	}
	
}

?>
