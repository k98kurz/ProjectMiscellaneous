<?php

/*
 * Form field generator - Copyright Jonathan Voss
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

define("_CONST_", 1);

require_once(".config.php");
require_once("dependency.session.php");
require_once("dependency.security.php");

class FormFieldGenerator {
	// returns an array of html input fields
	// defaults to grabbing labels
	public function getFields ( $actionhash, $timestamp ) {
		
		$fields = Config::fields();
		$requiredFields = Config::requiredFields();
		$textareas = Config::textareas();
		$htmlInputArray = array();
		while (count($fields)>0) {
			$f = array_pop($fields);
			if (array_search($f, $textareas)!==false) {
				$d = "<textarea name=\"".$f."\"" . ((array_search($f, $requiredFields)!==false) ? " required" : "");
				$d.= "></textarea>";
			} else {
				$d = "<input name=\"".$f."\"" . ((array_search($f, $requiredFields)!==false) ? " required" : "");
				$d.= ">";
			}
			array_push($htmlInputArray, $d);
			array_push($htmlInputArray, "<input name=\"".$f."hash\">");
		}
		array_push($htmlInputArray, "<input type=\"hidden\" id=\"actionhash\" name=\"actionhash\" value=\"".$actionhash."\">");
		array_push($htmlInputArray, "<input type=\"hidden\" id=\"timestamp\" name=\"timestamp\" value=\"".$timestamp."\">");
		return $htmlInputArray;
	}
	
	public function getFieldsWithLabels ( $actionhash $timestamp ) {
		$labels = Config::fieldLabels();
		if (empty($labels)||$labels==false) {
			return $this->getFields(false);
		}
		$fields = Config::fields();
		$requiredFields = Config::requiredFields();
		$textareas = Config::textareas();
		$htmlInputArray = array();
		while (count($fields)>1) {
			$f = array_pop($fields);
			if (array_search($f, $textareas)!==false) {
				$l = (empty($labels[$f])) ? $f : $labels[$f];
				$d = "<label for=\"".$f."\" class=\"textarea\">".$l."</label>"
				$d.= "<textarea name=\"".$f."\"" . ((array_search($f, $requiredFields)!==false) ? " required" : "");
				$d.= "></textarea>";
			} else {
				$l = (empty($labels[$f])) ? $f : $labels[$f];
				$d = "<label for=\"".$f."\">".$l."</label>"
				$d.= "<input name=\"".$f."\"" . ((array_search($f, $requiredFields)!==false) ? " required" : "");
				$d.= ">";
			}
			array_push($htmlInputArray, $d);
			array_push($htmlInputArray, "<input name=\"".$f."hash\">");
		}
		array_push($htmlInputArray, "<input type=\"hidden\" id=\"actionhash\" value=\"".$actionhash."\">");
		array_push($htmlInputArray, "<input type=\"hidden\" id=\"timestamp\" value=\"".$timestamp."\">");
		return $htmlInputArray;
	}
}
