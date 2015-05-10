<?php

require_once('deps/test-more-php/Test-More-OO.php');
require_once('src/JSONMessage.php');

$data = array(
	'string' => 'text',
	'numeric' => '123',
	'integer' => 123,
	'float' => 12.3,
	'boolean' => TRUE,
	'list' => array(1, 2, 3),
	'map' => array('one' => 1, 'two' => 2, 'three' => 3)
	);
$encoded = json_encode($data);
$properties = new JSONMessage($data, $encoded);

// Test Plan

$t = new TestMore();
if (version_compare(phpversion(), '5.4.0') >= 0) {
	$t->plan(3);
	$t->is(json_encode($properties), $encoded, "JsonSerializable is used by json_encode");
} else {
	$t->plan(2);
}
$t->is($properties->jsonSerialize(), $data, "JsonSerializable returns JSONMessage->map");
$t->is($properties instanceof Traversable, TRUE, "JSONMessage is JsonSerializable");
