<?php
if (!defined("_CONST_")) { die("unatuhorized"); }

class DownloadHandler {
	public function __construct () {}
	public function doFile ($filename, $newname = "") {
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
	public function doArchive ($filearray, $zipname = "") {
		if (gettype($filearray)!="array") { return null; }
		if (gettype($zipname)!="string") { return null; }
		if (empty($zipname)) { $zipname = "download.zip"; }
		$f = array();
		foreach ($filearray as $a) {
			if (file_exists($a)) {
				array_push($f, $a);
			}
		}
		if (sizeof($f)>1) {
			$zip = new ZipArchive;
			if (!$zip->open("./".$zipname)) { return false; }
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
