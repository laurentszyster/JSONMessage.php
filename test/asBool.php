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
$message = new JSONMessage($data);

// Test Plan

$t = new TestMore();
$t->plan(11);
$t->is(
	$message->asBool('boolean'), TRUE,
	'JSONMessage::asBool returns the value of an existing property'
	);
$t->is(
	$message->asBool('foobar', TRUE), TRUE,
	'JSONMessage::asBool returns the provided default as a boolean if a property is missing'
	);
try {
	$message->asBool('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::asBool throws a Name Error when missing a property and no default value provided'
		);
}
$t->is(
	$message->asBool('foobar', 'true'), TRUE,
	'JSONMessage::asBool cast the default string provided as a boolean'
	);
$t->is(
	$message->asBool('foobar', 1), TRUE,
	'JSONMessage::asBool cast the default string provided as a boolean'
	);
foreach(array(
	'string' => FALSE,
	'numeric' => FALSE,
	'float' => FALSE,
	'boolean' => TRUE
	) as $key => $value) {
	$t->is(
		$message->asBool($key), $value,
		'JSONMessage::asBool cast '.gettype($message->map[$key]).' as a boolean'
		);
}
try {
	$message->asBool('list');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - list must be a scalar',
		'JSONMessage::asBool throws a Cast Error when the property is a list'
		);
}
try {
	$message->asBool('map');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - map must be a scalar',
		'JSONMessage::asBool throws a Cast Error when the property is a map'
		);
}
