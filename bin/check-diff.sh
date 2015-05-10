#!/bin/sh

cd `dirname $0`
cd ..

diff -u ../../codeigniter/framework/index.php application/tests/Bootstrap.php 
diff -u ../../codeigniter/framework/system/core/Loader.php application/tests/replace/core/Loader.php
