<?php
define("_CONST_", 1);

include "session.php";
include "security.php";

class EmailHandler {
	const fromMail = "no-reply@nobody.com";
	public const toMail = "nobody@nobody.com";
	
	public static function send ( $to, $subject, $message ) {
		if (empty($to) || empty($subject) || empty($message)) { return false; }
		$sec = new Security ();
		$to = $sec->filterText($to);
		$subject = $sec->filterText($subject);
		$message = $sec->filterText($message);
		return mail( $to, $subject, $message, "From: " . self::fromMail . "\r\nContent-Type: text/html; charset=ISO-8859-1" );
	}
	
	public static $formFailRedirectURL = "contact.html";
}

function pageDo () {
	if (empty($_POST['name'])||empty($_POST['email'])||empty($_POST['message'])||empty($_POST['actionhash'])||empty($_POST['ts'])) {
		$error_text = (empty($_POST['name']))? "Please specify your name.\n" : "";
		$error_text .= (empty($_POST['email']))? "Please specify your email.\n" : "";
		$error_text .= (empty($_POST['message']))? "Please write your message.\n" : "";
		$error_text .= (empty($_POST['actionhash'])||empty($_POST['ts']))? "Cryptographic error: please <a href='".EmailHandler::formFailRedirectURL."'>try again</a>.\n";
		return null;
	}
	if (empty($_POST['namehash'])||empty($_POST['emailhash'])||empty($_POST['messagehash'])) {
		$error_text = "Cryptographic error: please <a href='".EmailHandler::formFailRedirectURL."'>try again</a>.\n";
		return null;
	}
	
	$sec = new Security();
	
	if (!allValid(array('name','email','message'))) {
		$error_text = "Cryptographic error: please <a href='".EmailHandler::formFailRedirectURL."'>try again</a>.\n";
	} else {
		$message = validExtraFields();
		$message .= $_POST['message'];
		$result = EmailHandler::send(EmailHandler::toMail, "New message from " . $_POST['email'], $message);
	}
}

function allValid ($keys) {
	foreach ($keys as $k) {
		if (!$sec->validateData($k, $_POST[$k], $_POST['actionhash'], $_POST[$k.'hash'])) {
			return false;
		}
	}
	return true;
}

function validExtraFields () {
	$text = "";
	$keys = array();
	foreach ($keys as $k) {
		if ($sec->validateData($k, $_POST[$k], $_POST['actionhash'], $_POST[$k.'hash'])) {
			$text .= $_POST[$k];
		}
	}
}


$t = pageDo();
if ($t!=null) {
	$messageToUser = $t;
}

?>
