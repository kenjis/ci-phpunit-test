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

. filelist.sh

for i in $list
do
	(cd `dirname $i`
	ln -sf "`basename $i`" "`basename $i`.$version"
	echo "$i -> `basename $i`.$version")
done
