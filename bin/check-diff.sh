#!/bin/sh

cd `dirname $0`
cd ..

diff -u ../../codeigniter/framework/index.php application/tests/Bootstrap.php 
diff -u ../../codeigniter/framework/system/core/Loader.php application/tests/_ci_phpunit_test/replacing/core/Loader.php
