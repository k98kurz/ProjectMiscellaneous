<?php

/*
 * HTMl email template - Copyright Jonathan Voss
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
		$labels = Config::fieldLabels();
		$textareas = Config::textareas();
		
		$html = "<html><head><title>".$dataArray['subject']."</title>";
		$html.= "<style>p{text-transform:capitalize;color:#fff;}\n";
		$html.= "a{color:#999;text-decoration:none;}</style></head>";
		$html.= "<body><h2>New Contact Form Message</h2>";
		
		foreach ($dataArray as $key=>$value) {
			$html.= "<p>".(empty($labels[$key]) ? $key : $labels[$key]).":";
			$html.= ((array_search($key, $textareas)!==false) ? "</p><p>" : " ").$value."</p><p>&nbsp;</p>";
		}
		
		$html.= "</body></html>\n";
		
		return $html;
	}
}
