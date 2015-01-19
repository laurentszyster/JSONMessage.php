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
$t->plan(3);
$t->is(
	$message->setDefault('string', 'test'), 'text',
	'JSONMessage::setDefault returns the value of an existing property'
	);
$t->is(
	$message->setDefault('foobar', 'test'), 'test',
	'JSONMessage::setDefault returns the provided default if a missing property'
	);
$t->is(
	$message->has('foobar'), TRUE,
	'JSONMessage::setDefault sets the provided default if a missing property'
	);
