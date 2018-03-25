#!/bin/sh

usage() {
	echo "Create links to state CodeIgniter version"
	echo " usage: $0 <version>"
	echo "    eg: $0 3.1.3"
}

if [ $# -eq 0 ]; then
	usage
	exit
fi

version="$1"

. ../../../../bin/filelist.sh

for i in $list
do
	(cd `dirname $i`
	ln -sf "../`basename $i`" "old/$version-`basename $i`"
	echo "$i -> old/$version-`basename $i`")
done
