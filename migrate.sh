#!/bin/bash

# Go to composer root
cd "$(dirname "$0")/../../../"

mkdir -p data

php vendor/bin/phinx migrate -c "$(dirname "$0")/phinx.yml"
