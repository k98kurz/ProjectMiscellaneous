<?php

/*
 * Failed submission template - Copyright Jonathan Voss
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
		$html = "<!doctype html>\n<html>\n\t<head>\n\t\t<title>Contact Form Submission Failed</title>\n";
		$html.= "\t\t<link rel=\"stylesheet\" href=\"contactform.css\">\n";
		$html.= "\t</head>\n\t<body class=\"contactform failed\">\n\t\t<h1>Contact Form Submission Failed</h1>\n";
		for ($i=0;$i<count($dataArray);$i++) {
			$html.= "\t\t<p>".$dataArray[$i]."</p>\n";
		}
		$html.= "</body></html>";
	}
}
