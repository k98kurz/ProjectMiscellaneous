<?php
if (!defined("_CONST_")) { die("unatuhorized"); }

class DownloadHandler {
	private $authModule;
	
	public function __construct () {}
	public function passAuthModule ($aum) {
		$this->authModule = $aum;
	}
	public function doFile ($filename, $token, $newname = "") {
		if (!$this->authModule->isAuthorized($filename, $token)) { return false; }
		if (!file_exists($filename)) { return false; }
		$fcontents = file_get_contents($filename);
		$p = pathinfo($filename);
		$name = (!empty($newname)&&gettype($newname)=="string") ? $newname : $p['basename'];
		header("Content-type: application/octet-stream");
		header("Content-Disposition: filename=\"".$name."\"");
		header("Content-length: ".strlen($fcontents));
		header("Cache-control: private");
		echo $fcontents;
		return true;
	}
	public function doArchive ($filearray, $tokens, $zipname = "") {
		if (gettype($filearray)!="array") { return null; }
		if (gettype($zipname)!="string") { return null; }
		if (gettype($tokens)!="array"||sizeof($tokens)!=sizeof($filearray)) { return null; }
		if (empty($zipname)) { $zipname = "download.zip"; }
		$f = array(); $i=0;
		for ($i=0; $i<sizeof($filearray); $i++) {
			if (file_exists($filearray[$i])&&$this->authModule->isAuthorized($filearray[$i], $tokens[$i])) {
				array_push($f, $filearray[$i]);
			}
		}
		// authorize zip file via public directory
		if (sizeof($f)>1) {
			$zip = new ZipArchive;
			if (!$zip->open("./".$zipname, ZipArchive::CREATE)) { return false; }
			foreach ($f as $a) {
				$zip->addFile($a);
			}
			$zip->close();
			$ret = $this->doFile($zipname);
			unlink("./".$zipname);
			return $ret;
		}
		if (sizeof($f)==1) { return $this->doFile($n[0]); }
		return false;
	}
}

?>
