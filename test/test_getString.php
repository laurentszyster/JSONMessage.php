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
$t->plan(9);
$t->is(
	$message->getString('string'), 'text',
	'JSONMessage::getString returns the value of an existing string property'
	);
$t->is(
	$message->getString('foobar', 'test'), 'test',
	'JSONMessage::getString returns the provided default string if a missing property'
	);
try {
	$message->getString('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::getString throws a Name Error when missing a property and no default value provided'
		);
}
try {
	$message->getString('foobar', 1);
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - foobar must be a String',
		'JSONMessage::getString throws a Type Error when the default provided is not a string'
		);
}
foreach(array('integer', 'float', 'boolean', 'list', 'map') as $key) {
	try {
		$message->getString($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Type Error - '.$key.' must be a String',
			'JSONMessage::getString throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
