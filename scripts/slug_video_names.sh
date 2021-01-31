#!/bin/bash

if [ ! -d saved_videos/ ]
then
    echo "Error: saved_videos/ directory not found"
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

for f in ./saved_videos/*
do
    f_slug="${f//[^a-zA-Z0-9\.]/_}"
    f_slug="./saved_videos/${f_slug:15}"
    if [ "$f" != "$f_slug" ]
    then
        printf '\033[1m"\033[0m%s\033[1m" -> "\033[0m%s\033[1m"\033[0m\n' "$(f_chomp "$f")" "$(f_chomp "$f_slug")"
        mv "$f" "$f_slug"
        c="$((c+1))"
    fi
done

echo ""
echo "updated $c filenames."

