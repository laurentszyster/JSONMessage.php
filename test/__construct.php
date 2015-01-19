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
$json = json_encode($data);

// Test Plan

$t = new TestMore();
$t->plan(12);

$message = new JSONMessage(array());
$t->isclass_ok($message, 'JSONMessage');
$t->is($message->encoded(), NULL, '$message->encoded() is null');
$t->isclass_ok($message->exception('test'), 'Exception');
$t->is($message->exception('test')->getMessage(), 'test', 'Set the exception message');

$message = new JSONMessage($data);
$t->isclass_ok($message->exception('test'), 'Exception');

$message = new JSONMessage($data, $json);
$t->is($message->encoded(), $json, '$message->encoded() is '.$json);

foreach(array(
	'[1,2,3]',
	'null',
	'1',
	'1.1',
	'"string"',
	'true'
	) as $json) {
	$value = json_decode($json);
	try {
		$message = new JSONMessage($value, $json);
	} catch (Exception $e) {
		$t->is(
			$e->getMessage(),
			'Type Error - not an Object: '.$json,
			'throws a Type Error when passing a '.gettype($value).' to JSONMessage::__construct'
			);
	}
}
