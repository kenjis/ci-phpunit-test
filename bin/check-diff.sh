#!/bin/sh

cd `dirname $0`
cd ..

system_dir="../../codeigniter/framework/system"
replacing_dir="application/tests/_ci_phpunit_test/replacing"

diff -u ../../codeigniter/framework/index.php application/tests/Bootstrap.php

. bin/filelist.sh

for i in $list
do
    if [ "$i" = "core/Common.php" ]; then
        continue
    fi
	diff -u "$system_dir/$i" "$replacing_dir/$i"
done
