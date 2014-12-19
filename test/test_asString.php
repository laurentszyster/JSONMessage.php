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
	$message->asString('string'), 'text',
	'JSONMessage::asString returns the value of an existing property'
	);
$t->is(
	$message->asString('foobar', 123), '123',
	'JSONMessage::asString returns the provided default as a string if a missing property'
	);
try {
	$message->asString('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::asString throws a Name Error when missing a property and no default value provided'
		);
}
$t->is(
	$message->asString('foobar', 1), '1',
	'JSONMessage::asString cast the default provided as a string'
	);
foreach(array(
	'integer' => '123',
	'float' => '12.3',
	'boolean' => 'true',
	'list' => '[1,2,3]',
	'map' => '{"one":1,"two":2,"three":3}'
	) as $key => $value) {
	$t->is(
		$message->asString($key), $value,
		'JSONMessage::asString cast '.gettype($message->map[$key]).' as a (JSON) string'
		);
}