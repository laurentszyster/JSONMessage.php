test: pull
	php test/is_list.php
	php test/is_map.php
	php test/__construct.php
	php test/has.php
	php test/keys_values.php
	php test/getDefault.php
	php test/setDefault.php
	php test/getString.php
	php test/getInt.php
	php test/getFloat.php
	php test/getBool.php
	php test/getArray.php
	php test/getList.php
	php test/getMap.php
	php test/asString.php
	php test/asInt.php
	php test/asFloat.php
	php test/asBool.php
	php test/ArrayAccess.php
	php test/Traversable.php
	php test/Countable.php
	php test/JsonSerializable.php

pull: deps deps/test-more-php

deps:
	mkdir -p deps

deps/test-more-php:
	svn checkout http://test-more-php.googlecode.com/svn/trunk/ deps/test-more-php

