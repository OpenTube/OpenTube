#!/bin/bash

if [ ! -d videos/saved/ ]
then
    echo "Error: videos/saved/ directory not found"
    exit 1
fi

tw="$(tput cols)"
mw="$(((tw-12)/2))"
if [ "$mw" -lt "3" ]
then
    mw=5
fi

function f_chomp() {
    local str=$1
    local strlen=${#str}
    if [ "$strlen" -gt "$mw" ]
    then
        printf "%s.." "${str:15:mw}"
    else
        printf "%s" "${str:15}"
    fi
}

c=0

for f in ./videos/saved/*
do
    f_slug="${f//[^a-zA-Z0-9\.]/_}"
    f_slug="./videos/saved/${f_slug:15}"
    if [ "$f" != "$f_slug" ]
    then
        printf '\033[1m"\033[0m%s\033[1m" -> "\033[0m%s\033[1m"\033[0m\n' "$(f_chomp "$f")" "$(f_chomp "$f_slug")"
        mv "$f" "$f_slug"
        c="$((c+1))"
    fi
done

echo ""
echo "updated $c filenames."

