<?php

require_once('deps/test-more-php/Test-More-OO.php');
require_once('src/JSONMessage.php');

$jsonStrings = array(
	'[1,2,3]' => TRUE,
	'[]' => TRUE,
	'["string"]' => TRUE,
	'{"key": "value"}' => FALSE,
	'"string"' => FAlSE,
	'null' => FAlSE,
	'false' => FAlSE,
	'true' => FAlSE,
	'123' => FAlSE,
	'1.23' => FAlSE
	);

$t = new TestMore();

$t->plan(count(array_keys($jsonStrings)));

foreach ($jsonStrings as $json => $isList) {
 	$t->is(
		JSONMessage::is_list(json_decode($json, TRUE)),
		$isList,
		"'".$json.($isList ? "' is" : "' is not")." a JSON list"
		);
}
