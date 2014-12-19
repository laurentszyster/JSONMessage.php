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
$uniform = ('{'
	.'"boolean":true,'
	.'"float":12.3,'
	.'"integer":123,'
	.'"list":[1,2,3],'
	.'"map":{"one":1,"three":3,"two":2},'
	.'"numeric":"123",'
	.'"string":"text"'
	.'}');
$message = new JSONMessage($data);

$inputFilenames = glob('test/data/*.json');

$t = new TestMore();
$t->plan(1+count($inputFilenames));
$t->is(
	$message->uniform(), $uniform,
	'JSONMessage::uniform returns uniformely encoded JSON'
	);
foreach($inputFilenames as $filename) {
	$basename = basename($filename, '.json');
	$message = JSONMessage::parse(file_get_contents($filename));
	$t->is(
		file_get_contents('test/data/'.$basename.'.ujson'),
		$message->uniform(),
		'uniformed '.$basename
		);
}
