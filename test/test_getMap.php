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
	$message->getMap('map'), array('one' => 1, 'two' => 2, 'three' => 3),
	'JSONMessage::getMap returns the value of an existing Map property'
	);
$t->is(
	$message->getMap('foobar', array()), array(),
	'JSONMessage::getMap returns the provided default Map if a missing property'
	);
try {
	$message->getMap('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::getMap throws a Name Error when missing a property and no default value provided'
		);
}
try {
	$message->getMap('foobar', array(1, 2, 3));
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - foobar must be a Map',
		'JSONMessage::getMap throws a Type Error when the default provided is not a Map'
		);
}
foreach(array('string', 'numeric', 'integer', 'float', 'boolean') as $key) {
	try {
		$message->getMap($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Type Error - '.$key.' must be an Array',
			'JSONMessage::getMap throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
try {
	$message->getMap('list');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - list must be a Map',
		'JSONMessage::getMap throws a Type Error when the property value is a List'
		);
}
