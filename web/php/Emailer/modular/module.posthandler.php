<?php
/*
 * Post handler - Copyright Jonathan Voss
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

class PostHandler {
	private $sec;
	
	public function __construct () {
		$sec = new Security ();
	}
	
	// Main method:
	//		checks if data is valid
	//		sends contact email
	//		returns redirect: error or success
	public function handle () {
		$ac = $_POST['actionhash'];
		$ts = $_POST['timestamp'];
		$redirect = "<!doctype html><html><head><title>Form Submission Redirect</title>";
		$data = new array();
		$error = false;
		if (!$sec->validateActionHash("sendContactForm", $ts, $ac)) { array_push($data, "Cryptographic error: invalid actionhash."); $error = true; }
		if (!$this->checkRequired()) { array_push($data, "Error: required field not filled out."); $error = true; )
		if (!$this->checkDataValid($ac)) { array_push($data, "Error: invalid data field(s)."); $error = true; }
		if (!$error) {
			$res = EmailHandler::sendContactForm($this->getData(), "New contact form message");
			if (!$res) {
				$error = true;
				array_push($data, "Internal error: email function failed.");
			} else {
				array_push($data, "Email success!");
			}
		}
		$data = implode(",", $data);
		$redirect .= "<meta http-equiv=\"refresh\" value=\"0; url=index.php?option=";
		$redirect .= (($error) ? "failed&err" : "success&") . "data=".urlencode($data)."\">";
		$redirect .= "</head><body>Refreshing...</body></html>\n";
		echo $redirect;
	}
	
	// Check that all required fields are submitted
	private function checkRequired ( ) {
		$requiredFields = Config::requiredFields();
		while (count($requiredFields)>0) {
			$f = array_pop($requiredFields);
			if (empty($_POST[$f])||empty($_POST[$f."hash"])) { return false; }
		}
		return true;
	}
	
	// Check that all field data submitted has correct hash
	private function checkDataValid ( $actionhash ) {
		$fields = Config::fields();
		while (count($fields)>0) {
			$f = array_pop($fields);
			if (!empty($_POST[$f])&&!empty($_POST[$f."hash"]) {
				if (!$this->sec->validateData($f, $_POST[$f], $actionhash, $_POST[$f.'hash'])) { return false; }
			}
		}
	}
	
	// Returns all data submitted
	private function getData () {
		$data = new array();
		$fields = Config::fields();
		while (count($fields)>0) {
			$f = array_pop($fields);
			array_push($data, $_POST[$f]);
		}
		return $data;
	}
}
