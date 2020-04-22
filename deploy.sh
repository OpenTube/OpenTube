#!/bin/bash

if [[ ! -d .git ]] || [[ ! -f index.php ]]
then
    echo "Error: this is not a OpenTube directory."
    exit 1
fi

if [ "$1" == "--help" ] || [ "$1" == "-h" ]
then
    echo "usage: $(basename "$0") [host]"
    echo "example:    $(basename "$0") root@localhost:/var/www/html/OpenTube"
    exit 0
fi

host="${1:-chiller@149.202.127.134:/var/www/html/OpenTube}"

function upload_cmd() {
    file="$1"
    cmd="scp -r ./$file $host"
    echo "[*] $cmd"
    eval "$cmd"
}

echo "[*] uploading to '$host' ..."

upload_cmd *.html
upload_cmd *.htm
upload_cmd *.css
upload_cmd *.php
upload_cmd *.sh
# upload_cmd .git

