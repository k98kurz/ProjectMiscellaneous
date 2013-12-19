<?php

/*
 * Successful submission template - Copyright Jonathan Voss
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

class template {
	public static function fetchTemplate ( $dataArray ) {
		$html = "<!doctype html>\n<html>\n\t<head>\n\t\t<title>Contact Submission Sent</title>\n";
		$html.= "\t\t<link rel=\"stylesheet\" href=\"contactform.css\">\n\t</head>\n";
		$html.= "\t<body class=\"contactform success\">\n\t\t<h1>Submission Successful</h1>\n";
		$dataArray = array_reverse($dataArray);
		while (count($dataArray)>0) {
			$d = array_pop($dataArray);
			$html.= "\t\t<p>".$d."</p>\n";
		}
		$html.= "\t</body>\n</html>\n"
	}
	return $html;
}
