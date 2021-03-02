#!/bin/sh

rm composer.lock
git checkout 2.x
cp composer.json.local composer.json
composer update
