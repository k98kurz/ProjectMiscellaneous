<?php
define("_CONST_", 1);

include "session.php";
include "security.php";

$sec = new Security();

$datafieldnames = array("name","email","message");
$timestamp = $sec->getTimestamp();
$actionhash = $sec->makeActionHash("SendEmail", $timestamp);
$salts = $sec->setSalts($datafieldnames);


$inputs = array();
$saltinputs = array();
foreach ($salts as $fn=>$s) {
	if ($fn=="message") {
		$inputs[$fn] = "<textarea type=\"text\" name=\"$fn\"></textarea>";
	} else {
		$inputs[$fn] = "<input type=\"text\" name=\"$fn\">";
	}
	$inputs[$fn] .= "<input type=\"hidden\" name=\"".$fn."hash\">\n";
	$saltinputs[$fn] = "<input type=\"hidden\" name=\"$fn\" value=\"$s\">";
}

function writeInputs () {
	foreach ($inputs as $k=>$i) {
		echo $i;
	}
}

function writeInputsWithLabels ($indentstring = "") {
	foreach ($inputs as $k=>$i) {
		echo "<label for:$k>$k: </label>".$i.$indentstring;
	}
	echo "<input type=\"hidden\" id=\"actionhash\" value=\"$actionhash\">";
	echo "<input type=\"hidden\" id=\"ts\" value=\"$timestamp\">\n";
}

function writeSaltInputs () {
	foreach ($saltinputs as $k=>$i) {
		echo $i;
	}
}
?>
<html>
	<head>
		<title>/\/\/\/</title>
		<script src="md5.js"></script>
		<script src="emailform.js"></script>
	</head>
	<body>
		<form action="#" id="salts"><? writeSaltInputs(); ?></form>
		<form action="emailer.php" method="post" target="_parent" id="emailform">
			<? writeInputsWithLabels("			"); echo "\n"; ?>
		</form>
		<input type="submit" value="Send" onclick="form.submit()" id="sendbutton">
		<script>
			form.inputs = [false<?
			foreach ($datafieldnames as $k) {
				echo ",".$k;
			}
		?>];
		</script>
	</body>
</html>
