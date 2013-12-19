<?php

/*
 * Form template - Copyright Jonathan Voss
 * Developer email: k98kurz@gmail.com
 * Intended to be used in an iframe (thus easily installed in static web pages)
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
	public static function fetchTemplate ( $dataArray, $salts ) {
		$fields = array_reverse(Config::fields());
		$requiredFields = Config::requiredFields();
		$textareas = Config::textareas();
		
		$html = "<!doctype html>\n<html>\n\t<head>\n\t\t<title>Contact Form</title>\n";
		$html.= "\t\t<link rel=\"stylesheet\" href=\"contactform.css\">\n";
		$html.= "\t</head>\n\t<body class=\"contactform\">\n";
		$html.= "\t\t<form onsubmit=\"return false;\" action=\"#\" target=\"_self\" id=\"salts\">\n";
		while (count($fields>0)) {
			$f = array_pop($fields);
			$s = $salts[$f];
			$html.= "\t\t\t<input type=\"hidden\" name=\"".$f."\" value=\"".$s."\">\n";
		}
		$html.= "\t\t</form>";
		$html.= "\t\t<form action=\"index.php?submit\" method=\"post\" target=\"_self\" id=\"contactform\">\n";
		while(count($dataArray>0)) {
			$f = array_pop($dataArray);
			$html.= "\t\t\t".$f."\n";
		}
		
		$html.= "</form>\t</body>\n</html>\n"
		
		return $html;
	}
}
