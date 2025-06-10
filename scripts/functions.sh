#!/bin/bash
script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
source $script_path/config.sh

#CREATE DIRECTORIES IF THEY DO NOT ALREADY EXIST -p
mkdir -p $script_path/xml
mkdir -p $script_path/csv
mkdir -p $script_path/traveltek
mkdir -p $script_path/log

