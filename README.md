JSONMessage.php
===
[![Build Status](https://travis-ci.org/laurentszyster/JSONMessage.php.svg)](https://travis-ci.org/laurentszyster/JSONMessage.php)

A convenience class to validate a PHP associative array as a typed JSON message with mandatory properties and default values.

Requirements
---
- box an associative array with conveniences for JSON message validation
- implement a uniform JSON representation to identify messages
- provide the static functions `is_map(array)` and `is_list(array)`
- support PHP 5.2

Synopsis
---
Use `JSONMessage` whenever a PHP function must validate the presence of keys, the type of values and supply defaults for an associative array.

### Validation

For instance in a function that validates options for an SQL query builder:

~~~php
<?php

function queryOptions($array) {
    $m = new JSONMessage($array);
    // mandatory values
    $table = $m->getString('table');
    $params = $m->getMap('params');
    // optional defaults
    $columns = $m->getList('columns', array());
    $offset = $m->getInt('offset', 0);
    $limit = $m->getInt('limit', 30);
    return array($table, $params, $columns, $offset, $limit);
}

?>
~~~

Without the convenience of `JSONMessage`, this function would look something like this error-prone, incomplete and butt-ugly code:

~~~php
<?php

function queryOptions($array) {
    // mandatory values
    if (isset($array['table']) && is_string($array['table'])) {
        $table = $array['table'];
    } else {
        throw new Exception("Name or Type error for 'table'");
    }
    if (isset($array['params']) && is_array($array['params'])) {
        $params = $array['params'];
    } else {
        throw new Exception("Name or Type error for 'params'");
    }
    // optional defaults
    if (isset($array['columns']) {
        if (is_array($array['columns'])) {
            $params = $array['columns'];
        } else {
            throw new Exception("Type error for 'columns'");
        }
    } else {
        $columns = array();
    }
    if (isset($array['offset']) {
        if (is_integer($array['offset'])) {
            $offset = $array['offset'];
        } else {
            throw new Exception("Type error for 'offset'");
        }
    } else {
        $offset = 0;
    }
    if (isset($array['limit']) {
        if (is_integer($array['limit'])) {
            $limit = $array['limit'];
        } else {
            throw new Exception("Type error for 'limit'");
        }
    } else {
        $limit = 0;
    }

    return array($table, $params, $columns, $offset, $limit);
}

?>
~~~

### Uniform

JSONModel is also usefull when testing associative arrays, because it provides a uniform JSON representation.

~~~php
<?php

$uniform = '{"a":1,"b":3,"d":4}';
$message = new JSONMessage(array("b"=>3,"a"=>1,"d"=>4));
$message->uniform() === $uniform;

?>
~~~

Having a uniform representation also means that we can identify a message by distributed key or validate the digital signature of a message.

It's too good to be left out of a JSON message class. 