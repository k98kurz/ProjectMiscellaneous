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

class postHandler {
	
	// Check that all required fields are submitted
	public function checkRequired () {
		$requiredFields = Config::requiredFields();
	}
	
	// Check that all field data submitted has correct hash
	public function checkDataValid () {
		
	}
	
	// Returns all data submitted
	public function getData () {
		
	}
}
