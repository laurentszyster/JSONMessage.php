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
	$message->getBool('boolean'), TRUE,
	'JSONMessage::getBool returns the value of an existing Boolean property'
	);
$t->is(
	$message->getBool('foobar', TRUE), TRUE,
	'JSONMessage::getBool returns the provided default Boolean if a missing property'
	);
try {
	$message->getBool('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::getBool throws a Name Error when missing a property and no default value provided'
		);
}
try {
	$message->getBool('foobar', 'test');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - foobar must be a Boolean',
		'JSONMessage::getBool throws a Type Error when the default provided is not an Boolean'
		);
}
foreach(array('list', 'map') as $key) {
	try {
		$message->getBool($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Cast Error - '.$key.' must be a scalar',
			'JSONMessage::getInt throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
foreach(array('string', 'numeric', 'integer', 'float') as $key) {
	try {
		$message->getBool($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Type Error - '.$key.' must be a Boolean',
			'JSONMessage::getBool throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
