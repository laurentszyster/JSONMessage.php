<?php

require_once('deps/test-more-php/Test-More-OO.php');
require_once('src/JSONMessage.php');

class TestException extends Exception {}

$data = array(
	'string' => 'text',
	'numeric' => '123',
	'integer' => 123,
	'float' => 12.3,
	'boolean' => TRUE,
	'list' => array(1, 2, 3),
	'map' => array('one' => 1, 'two' => 2, 'three' => 3)
	);
$message = new JSONMessage($data);

// Test Plan

$t = new TestMore();
$t->plan(10);
$t->is(
	$message->asFloat('float'), 12.3,
	'JSONMessage::asFloat returns the value of an existing property'
	);
$t->is(
	$message->asFloat('foobar', 12.3), 12.3,
	'JSONMessage::asFloat returns the provided default as an integer if a property is missing'
	);
try {
	$message->asFloat('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::asFloat throws a Name Error when missing a property and no default value provided'
		);
}
$t->is(
	$message->asFloat('foobar', '12.3'), 12.3,
	'JSONMessage::asFloat cast the default provided as a float'
	);
foreach(array(
	'numeric' => 123.0,
	'integer' => 123.0,
	) as $key => $value) {
	$t->is(
		$message->asFloat($key), $value,
		'JSONMessage::asFloat cast '.gettype($message->map[$key]).' as a float'
		);
}
try {
	$message->asFloat('string');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - string must be numeric',
		'JSONMessage::asFloat throws a Cast Error when the property is a non-numeric string'
		);
}
try {
	$message->asFloat('boolean');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - boolean must be numeric',
		'JSONMessage::asFloat throws a Cast Error when the property is a list'
		);
}
try {
	$message->asFloat('list');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - list must be a numeric scalar',
		'JSONMessage::asFloat throws a Cast Error when the property is a list'
		);
}
try {
	$message->asFloat('map');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - map must be a numeric scalar',
		'JSONMessage::asFloat throws a Cast Error when the property is a map'
		);
}
