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
	$message->getInt('integer'), 123,
	'JSONMessage::getInt returns the value of an existing integer property'
	);
$t->is(
	$message->getInt('foobar', 123), 123,
	'JSONMessage::getInt returns the provided default integer if a missing property'
	);
try {
	$message->getInt('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::getInt throws a Name Error when missing a property and no default value provided'
		);
}
try {
	$message->getInt('foobar', 'test');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - foobar must be an Integer',
		'JSONMessage::getInt throws a Type Error when the default provided is not an integer'
		);
}
foreach(array('string', 'numeric', 'float', 'boolean', 'list', 'map') as $key) {
	try {
		$message->getInt($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Type Error - '.$key.' must be an Integer',
			'JSONMessage::getInt throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
