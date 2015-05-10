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
$encoded = json_encode($data);
$properties = new JSONMessage($data, $encoded);

// Test Plan

$t = new TestMore();
$t->plan(2);
$t->is($properties instanceof Countable, TRUE, "JSONMessage is Countable");
$t->is(count($properties), 7, "JSONMessage count is its boxed array's count");
