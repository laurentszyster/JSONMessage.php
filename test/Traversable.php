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

// Test Plan

$t = new TestMore();
$t->plan(8);
$properties = new JSONMessage($data, $encoded);
$t->is($properties instanceof Traversable, TRUE, "JSONMessage is Traversable");
foreach ($properties as $key => $value) {
	$t->is($properties[$key], $value, "property '".$key."' has value ".json_encode($value));
}
