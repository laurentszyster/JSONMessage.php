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
	$message->asInt('integer'), 123,
	'JSONMessage::asInt returns the value of an existing property'
	);
$t->is(
	$message->asInt('foobar', 123), 123,
	'JSONMessage::asInt returns the provided default as an integer if a property is missing'
	);
try {
	$message->asInt('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::asInt throws a Name Error when missing a property and no default value provided'
		);
}
$t->is(
	$message->asInt('foobar', '123'), 123,
	'JSONMessage::asInt cast the default provided as an integer'
	);
foreach(array(
	'numeric' => 123,
	'float' => 12,
	'boolean' => 1
	) as $key => $value) {
	$t->is(
		$message->asInt($key), $value,
		'JSONMessage::asInt cast '.gettype($message->map[$key]).' as an integer'
		);
}
try {
	$message->asInt('string');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - string must be numeric or boolean',
		'JSONMessage::asInt throws a Cast Error when the property is a non-numeric string'
		);
}
try {
	$message->asInt('list');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - list must be a scalar',
		'JSONMessage::asInt throws a Cast Error when the property is a list'
		);
}
try {
	$message->asInt('map');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - map must be a scalar',
		'JSONMessage::asInt throws a Cast Error when the property is a map'
		);
}
