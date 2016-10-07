#!/bin/bash

cd ../../../
echo php vendor/bin/phinx migrate -c `dirname $0`/phinx.yml

