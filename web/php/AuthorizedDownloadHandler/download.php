<?php
define("_CONST_", 1);

if (empty($_GET)) { die (""); }

require "./DownloadHandler.class.php";
require "./AuthorizationHandler.class.php";

$files = array();
foreach ($_GET as $k=>$v) {
	if (strpos($k, "file")===0) {
		array_push($files, $v);
	}
}
if (sizeof($files)>0) {
	$downloader = new DownloadHandler;
	if (sizeof($files)>1) {
		$zname = (!empty($_GET['downloadname'])) ? $_GET['downloadname'] : "";
		$res = $downloader->doArchive($files, $zname);
	} else {
		$res = $downloader->doFile($files[0], (!empty($_GET['downloadname'])) ? $_GET['downloadname'] : "");
	}
	if (!$res) {
		echo "Download failed.";
	}
}

die("");
