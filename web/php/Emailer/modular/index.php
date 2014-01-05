<?php

if (!empty($_GET['option']) {
	define("_CONST_", 1);
}

require_once(".config.php");
require_once("dependency.session.php");
require_once("dependency.security.php");
require_once("dependency.emailer.php");

$option = $_GET['option'];
$data = urldecode($_GET['data']);
if (!empty($data)) { $data = explode(",", $data); }

switch ($option) {
	case "form":
		require_once("module.formfields.php");
		require_once(Config::formTemplate);
		$security = new Security ();
		$ts = $security->getTimestamp();
		$ac = $security->makeActionHash("sendContactForm", $ts);
		$fieldgenerator = new FormFieldGenerator ();
		if (Config::useFormLabels) {
			$fields = $fieldgenerator->getFieldsWithLabels($ac, $ts);
		} else {
			$fields = $fieldgenerator->getFields($ac, $ts);
		}
		echo template::fetchTemplate($fields,$security->setSalts(Config::fields());
		break;
	case "send":
		require_once("module.posthandler.php");
		$posthandler = new PostHandler ();
		$posthandler->handlePosts("send");
		break;
	case "failed":
		require_once("template.submitfailed.php");
		$data = explode(",", $_GET['errdata']);
		echo template::fetchTemplate($data);
		break;
	case "success":
		require_once("template.submit.php");
		$data = explode(",", $_GET['data']);
		echo template::fetchTemplate($data);
		break;
	default:
		die("Unauthorized access.");
}
