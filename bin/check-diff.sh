#!/bin/sh

cd `dirname $0`
cd ..

system_dir="../../codeigniter/framework/system"
replacing_dir="application/tests/_ci_phpunit_test/replacing"

diff -u ../../codeigniter/framework/index.php application/tests/Bootstrap.php

diff -u $system_dir/core/CodeIgniter.php $replacing_dir/core/CodeIgniter.php
diff -u $system_dir/core/Input.php $replacing_dir/core/Input.php
diff -u $system_dir/core/Loader.php $replacing_dir/core/Loader.php

diff -u $system_dir/helpers/download_helper.php $replacing_dir/helpers/download_helper.php
diff -u $system_dir/helpers/url_helper.php $replacing_dir/helpers/url_helper.php

diff -u $system_dir/libraries/Upload.php $replacing_dir/libraries/Upload.php
