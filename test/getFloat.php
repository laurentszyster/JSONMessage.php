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
$t->plan(7);
$t->is(
	$message->getFloat('float'), 12.3,
	'JSONMessage::getFloat returns the value of an existing float property'
	);
$t->is(
	$message->getFloat('foobar', 12.3), 12.3,
	'JSONMessage::getFloat returns the provided default float if a missing property'
	);
try {
	$message->getFloat('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::getFloat throws a Name Error when missing a property and no default value provided'
		);
}
try {
	$message->getFloat('foobar', 'test');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Cast Error - foobar must be numeric',
		'JSONMessage::getFloat throws a Type Error when the default provided is not an float'
		);
}
foreach(array('list', 'map') as $key) {
	try {
		$message->getFloat($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Cast Error - '.$key.' must be a scalar',
			'JSONMessage::getInt throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
foreach(array('string') as $key) {
	try {
		$message->getFloat($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Cast Error - '.$key.' must be numeric',
			'JSONMessage::getInt throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
