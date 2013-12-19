<?php

/*
 * Emailer dependency - Copyright Jonathan Voss
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
if (!defined("_SEC_CLASS_")) { die("Dependency failed to load"); }

class EmailHandler {
	// core method
	public static function send ( $to, $subject, $message ) {
		if (empty($to) || empty($subject) || empty($message)) { return false; }
		$sec = new Security ();
		$to = $sec->filterText($to);
		$subject = $sec->filterText($subject);
		if (Config::useHTMLEmail==false) { $message = $sec->filterText($message); }
		return mail( $to, $subject, $message, "From: " . Config::fromMail .
			"\r\nContent-Type: text".((Config::useHTMLEmail) ? "/html" : "")."; charset=ISO-8859-1" );
	}
	
	// sends message from contactform message information
	// $messageData is an associative array of pre-validated information
	public static function sendContactForm ( $subject, $messageData ) {
		if (empty($messageData['subject'])) { $messageData['subject'] = $subject; }
		$message = self::parseEmailTemplate($messageData);
		return self::send ( (empty(Config::contactEmail) ? Config::devEmail : Config::contactEmail), $subject, $message );
	}
	
	// parses html template into $message variable, which is then returned
	// $messageData is an associative array of pre-validated information
	public static function parseEmailTemplate ( $messageData ) {
		if (Config::useHTMLEmail) {
			require_once(Config::emailTemplate);
			$message = template::fetchTemplate($messageData);
			return $message;
		} else {
			$message = Config::automatedEmailHeader . "\n\n";
			foreach ($message as $f=>$d) {
				$message .= $f . ": " . $d . "\n\n";
			}
			return $message;
		}
	}
	
}
