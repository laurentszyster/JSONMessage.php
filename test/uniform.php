<?php

require_once('deps/test-more-php/Test-More-OO.php');
require_once('src/JSONMessage.php');

$t = new TestMore();

$inputFilenames = glob('test/data/*.json');

$t->plan(2+count($inputFilenames));

// 1

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
$t->is(
	$message->uniform(), $uniform,
	'JSONMessage::uniform returns uniformely encoded JSON'
);

// 2

$data = array(
	'3' => "one",
	'2' => "two",
	'4' => "three"
);
$uniform = ('{'
	.'"2":"two",'
	.'"3":"one",'
	.'"4":"three"'
.'}');
$message = new JSONMessage($data);
$t->is(
	$message->uniform(), $uniform,
	'JSONMessage::uniform works around one corner case ,-)'
);

// +

foreach($inputFilenames as $filename) {
	$basename = basename($filename, '.json');
	$message = JSONMessage::parse(file_get_contents($filename));
	$t->is(
		file_get_contents('test/data/'.$basename.'.ujson'),
		$message->uniform(),
		'uniformed '.$basename
	);
}