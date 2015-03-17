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
$t->plan(11);

$message = new JSONMessage($data);

$t->is($message['string'], 'text', "\$message['string'] is 'text'");
$t->is($message['numeric'], '123', "\$message['numeric'] is '123'");
$t->is($message['integer'], 123, "\$message['integer'] is 123");
$t->is($message['float'], 12.3, "\$message['float'] is 12.3");
$t->is($message['boolean'], TRUE, "\$message['boolean'] is TRUE");
$t->is($message['list'], array(1, 2, 3), "\$message['list'] is [1,2,3]");
$t->is($message['map'], array('one' => 1, 'two' => 2, 'three' => 3), "\$message['map'] is {'one':1,'two':2,'three':3}");
$t->is($message[1], NULL, "\$message[1] is NULL");
$t->is($message['undefined'], NULL, "\$message['undefined'] is NULL");

$message['defined'] = 'one, two, three';

$t->is($message->map['defined'], 'one, two, three', "offsetSet updated the state of the message's map");

unset($message['defined']);

$t->is($message->map['defined'], NULL, "offsetUnset updated the state of the message's map");
