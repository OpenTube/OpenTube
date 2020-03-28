#!/bin/bash

if [[ ! -d .git ]] || [[ ! -f index.php ]]
then
    echo "Error: this is not a OpenTube directory."
    exit 1
fi

if [ "$1" == "--help" ] || [ "$1" == "-h" ]
then
    echo "usage: $(basename "$0") [target] [host]"
    echo "description: will scp all contents to host/target"
    echo "example:    $(basename "$0") foo root@localhost:/var/www"
    echo 'results in: scp -r ./* "root@localhost:/var/www/foo"'
    exit 0
fi

target="${1:-OpenTube}"
host="${2:-chiller@149.202.127.134:/var/www/html}"

echo "[*] uploading to '$host/$target' ..."

scp -r ./* "$host/$target"

