#!/bin/bash

if [[ ! -d .git ]] || [[ ! -f index.php ]]
then
    echo "Error: this is not a OpenTube directory."
    exit 1
fi

scp -r ./* chiller@149.202.127.134:/var/www/html/OpenTube

