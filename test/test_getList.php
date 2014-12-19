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
$t->plan(10);
$t->is(
	$message->getList('list'), array(1, 2, 3),
	'JSONMessage::getList returns the value of an existing List property'
	);
$t->is(
	$message->getList('foobar', array()), array(),
	'JSONMessage::getList returns the provided default List if a missing property'
	);
try {
	$message->getList('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::getList throws a Name Error when missing a property and no default value provided'
		);
}
try {
	$message->getList('foobar', array('one' => 1, 'two' => 2, 'three' => 3));
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - foobar must be a List',
		'JSONMessage::getList throws a Type Error when the default provided is not a List'
		);
}
foreach(array('string', 'numeric', 'integer', 'float', 'boolean') as $key) {
	try {
		$message->getList($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Type Error - '.$key.' must be an Array',
			'JSONMessage::getList throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
try {
	$message->getList('map');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - map must be a List',
		'JSONMessage::getList throws a Type Error when the property value is a Map'
		);
}
