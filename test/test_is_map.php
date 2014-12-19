<?php

require_once('deps/test-more-php/Test-More-OO.php');
require_once('src/JSONMessage.php');

$jsonStrings = array(
	'{"key": "value"}' => TRUE,
	'{"1": "one", "two": 2}' => TRUE,
	'{"1": "one", "3": "three"}' => TRUE,
	'{}' => TRUE,
	'[1,2,3]' => FALSE,
	'["string"]' => FALSE,
	'"string"' => FAlSE,
	'null' => FAlSE,
	'false' => FAlSE,
	'true' => FAlSE,
	'123' => FAlSE,
	'1.23' => FAlSE
	);

$t = new TestMore();

$t->plan(count(array_keys($jsonStrings)));

foreach ($jsonStrings as $json => $isMap) {
 	$t->is(
		JSONMessage::is_map(json_decode($json, TRUE)),
		$isMap,
		"'".$json.($isMap ? "' is" : "' is not")." a JSON object"
		);
}
