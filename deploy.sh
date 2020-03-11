#!/bin/bash

if [ ! -d ../FooTube ]
then
    echo "wrong dir"
    exit 1
fi

scp -r ./* chiller@149.202.127.134:/var/www/html/FooTube

