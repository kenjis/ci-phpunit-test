#!/bin/sh

cd `dirname $0`
cd ..

diff -u ../../codeigniter/framework/index.php application/tests/Bootstrap.php

diff -u ../../codeigniter/framework/system/core/CodeIgniter.php application/tests/_ci_phpunit_test/replacing/core/CodeIgniter.php
diff -u ../../codeigniter/framework/system/core/Input.php application/tests/_ci_phpunit_test/replacing/core/Input.php
diff -u ../../codeigniter/framework/system/core/Loader.php application/tests/_ci_phpunit_test/replacing/core/Loader.php

diff -u ../../codeigniter/framework/system/helpers/download_helper.php application/tests/_ci_phpunit_test/replacing/helpers/download_helper.php
diff -u ../../codeigniter/framework/system/helpers/url_helper.php application/tests/_ci_phpunit_test/replacing/helpers/url_helper.php

diff -u ../../codeigniter/framework/system/libraries/Upload.php application/tests/_ci_phpunit_test/replacing/libraries/Upload.php
