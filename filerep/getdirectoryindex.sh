#!/bin/bash
#find "$3" -type d > $1 2>$2 &
#find "$@" -type d
ls -fR "$@" | grep ^/
