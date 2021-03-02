#!/bin/sh

rm composer.lock
git checkout master
cp composer.json.local composer.json
composer update
