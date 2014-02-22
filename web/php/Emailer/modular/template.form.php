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
		$formappinputs = $fields;
		
		$html = "<!doctype html>\n<html>\n\t<head>\n\t\t<title>Contact Form</title>\n";
		$html.= "\t\t<link rel=\"stylesheet\" href=\"contactform.css\">\n\t\t<script src=\"formapp.js\">";
		$html.= "</script>\n\t\t<script src=\"MD5.js\"></script>\n";
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
		
		$html.= "\t\t</form>\n\t\t<input type=\"submit\" value=\"submit\" onclick=\"formapp.submit()\" id=\"sendbutton\">";
		if (!empty(Config::useResetButton)) {
			$html.= "<input type=\"submit\" value=\"reset\" onclick=\"formapp.reset()\">";
		}
		$html.= "\n\t\t<script>formapp.inputs = [false";
		foreach ($formappinputs as $k) {
			$html.=",\"".$k."\"";
		}
		$html.= "]\n\t\t</script>";
		$html.= "\n\t</body>\n</html>\n";
		
		return $html;
	}
}
