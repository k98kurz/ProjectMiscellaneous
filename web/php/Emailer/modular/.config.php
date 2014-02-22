<?php

/*
 * Configuration - Copyright Jonathan Voss
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

class Config {
	const contactEmail = "",
		devEmail = "",
		fromEmail = "no-reply@domain.tld",
		emailTemplate = "template.email.php",
		useHTMLEmail = true,
		automatedEmailHeader = "AUTOMATED MESSAGE FROM CONTACT FORM",
		formTemplate = "template.formfields.php",
		useFormLabels = true,
		submitSuccessTemplate = "template.submit.php",
		submitFailTemplate = "template.submitfailed.php",
		useResetButton = true;
	
	// Array of field names reversed due to the parsing being reversed
	// This applies for both fields and requiredFields
	public static function fields () {
		$filehandle = fopen("fields.csv.txt");
		$fields = fgetcsv($filehandle);
		fclose($filehandle);
		return $fields;
	}
	public static function requiredFields () {
		$filehandle = fopen("fieldsrequired.csv.txt");
		$fields = fgetcsv($filehandle);
		fclose($filehandle);
		return array_reverse($fields);
	}
	public static function textareas () {
		$filehandle = fopen("fieldstextareas.csv.txt");
		$fields = fgetcsv($filehandle);
		fclose($filehandle);
		return array_reverse($fields);
	}
	public static function fieldLabels () {
		return = parse_ini_file("fieldlabels.ini");
	}
}

define("_CONFIG_", 1);
