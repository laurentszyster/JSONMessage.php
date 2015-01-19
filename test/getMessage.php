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
$t->plan(11);
$map = $message->getMessage('map', array('one' => 1, 'two' => 2, 'three' => 3));
$t->isclass_ok($map, 'JSONMessage');
$t->is(
	$map->map, array('one' => 1, 'two' => 2, 'three' => 3),
	'JSONMessage::getMessage returns a JSONMessage wrapping the value of an existing Map property'
	);
$t->is(
	$message->getMessage('foobar', array())->map, array(),
	'JSONMessage::getMessage returns the provided default Map when a property is missing'
	);
try {
	$message->getMessage('foobar');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Name Error - foobar missing',
		'JSONMessage::getMessage throws a Name Error when missing a property and no default value provided'
		);
}
try {
	$message->getMessage('foobar', array(1, 2, 3));
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - foobar must be a Map',
		'JSONMessage::getMessage throws a Type Error when the default provided is not a Map'
		);
}
foreach(array('string', 'numeric', 'integer', 'float', 'boolean') as $key) {
	try {
		$message->getMessage($key);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Type Error - '.$key.' must be an Array',
			'JSONMessage::getMessage throws a Type Error when the property value is a '
			.gettype($message->map[$key])
			);
	}
}
try {
	$message->getMessage('list');
} catch (Exception $e) {
	$t->is(
		$e->getMessage(),
		'Type Error - list must be a Map',
		'JSONMessage::getMessage throws a Type Error when the property value is a List'
		);
}
