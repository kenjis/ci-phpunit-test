#!/bin/sh

usage() {
	echo "Get diff for CodeIgniter two versions"
	echo " usage: $0 <old_zip> <new_zip>"
	echo "    eg: $0 CodeIgniter-3.0.0.zip CodeIgniter-3.0.1.zip"
}

if [ $# -eq 0 ]; then
	usage
	exit
fi

f1="$1"
f2="$2"
v1=${f1%.*}
v2=${f2%.*}

rm -rf "$v1" "$v2"

unzip "$v1"
unzip "$v2"

rm -rf "$v1/user_guide"
rm -rf "$v2/user_guide"
diff -uwbrN "$v1" "$v2" > "$v1-$v2.diff"

. bin/filelist.sh

diff="$v1-$v2.ci-phpunit-test-only.diff"
/bin/echo -n > "$diff"

diff -uwb "$v1/index.php" "$v2/index.php" >> "$diff"

for i in $list
do
	diff -uwb "$v1/system/$i" "$v2/system/$i" >> "$diff"
done
