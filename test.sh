#!/bin/bash
PORT=${1:-8888}
procs="$(ps aux | grep php | grep -v grep)"
if [ "$procs" != "" ]
then
    echo "Error: some php process is already running"
    echo "$procs"
    exit 1
fi
if [ ! -d ../FooTube ]
then
    echo "Error: directory not found '../FooTube'"
    exit 1
fi
${BROWSER:-firefox} "http://localhost:$PORT/index.php" &
php -S "localhost:$PORT"

