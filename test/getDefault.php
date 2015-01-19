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
$t->plan(3);
$t->is(
	$message->getDefault('string'), 'text',
	'JSONMessage::getDefault returns the value of an existing property'
	);
$t->is(
	$message->getDefault('foobar', 'test'), 'test',
	'JSONMessage::getDefault returns the provided default if a missing property'
	);
try {
	$message->getDefault('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::getDefault throws a Name Error when missing a property and no default value provided'
		);
}
