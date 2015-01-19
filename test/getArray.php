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
$t->plan(10);
$t->is(
	$message->getArray('list'), array(1, 2, 3),
	'JSONMessage::getArray returns the value of an existing Array property'
	);
$t->is(
	$message->getArray('map'), array('one' => 1, 'two' => 2, 'three' => 3),
	'JSONMessage::getArray returns the value of an existing Array property'
	);
$t->is(
	$message->getArray('foobar', array()), array(),
	'JSONMessage::getArray returns the provided default Array if a missing property'
	);
try {
	$message->getArray('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::getArray throws a Name Error when missing a property and no default value provided'
		);
}
try {
	$message->getArray('foobar', 'test');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - foobar must be an Array',
		'JSONMessage::getArray throws a Type Error when the default provided is not an Array'
		);
}
foreach(array('string', 'numeric', 'integer', 'float', 'boolean') as $key) {
	try {
		$message->getArray($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Type Error - '.$key.' must be an Array',
			'JSONMessage::getArray throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
