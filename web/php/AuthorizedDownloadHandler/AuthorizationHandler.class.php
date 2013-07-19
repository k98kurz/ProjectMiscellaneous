<?php
if (!defined("_CONST_")) { die("unathorized"); }

class DLAuthHandler {
	private $authorizedDirectories;
	private $restrictedDirectories;
	private $externalAuthModule;
	private $useExtAuthMod;
	
	public function __construct () {
		$this->useExtAuthMod = false;
	}
	public function passAuthInfo ($authdir, $restrdir) {
		$this->authorizedDirectories = $authdir;
		$this->restrictedDirectories = $restrdir;
	}
	public function passExternalAuthModule ($resource) {
		$this->useExtAuthMod = true;
		$this->externalAuthModule = $resource;
	}
	public function authorizeDownload ($filepath) {
		$filepathparts = pathinfo($filepath);
		if ($this->useExtAuthMod) {
			if (!$this->externalAuthModule->authorize("download", $filepath)) { return null; }
		} else {
			$filename = $filepathparts['filename'];
			$directory = $filepathparts['dirname'];
			if (!in_array($directory, $this->authorizedDirectories)) { return null; }
			if (in_array($directory, $this->restrictedDirectories)) { return null; }
		}
		$token = $this->makeAccessToken($filename);
		return $token;
	}
	
	private function makeAccessToken ($filename) {
		if (!empty($_SESSION['token'])) {
			$token = sha1(md5($filename) . $_SESSION['token'] . date("m/d/y"));
		}
	}
	
}
