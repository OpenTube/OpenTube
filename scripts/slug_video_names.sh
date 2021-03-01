#!/bin/bash

if [ ! -d videos/ ]
then
    echo "Error: videos/ directory not found"
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
        printf "%s.." "${str::mw}"
    else
        printf "%s" "${str}"
    fi
}

c=0

function save_mv() {
	local src="$1"
	local dst="$2"
	if [ -f "$dst" ]
	then
		echo "Error: failed to move file already exists"
		echo "	src: $src"
		echo "	dst: $dst"
		exit 1
	fi
	mv "$src" "$dst" || exit 1
}

function slug_video() {
    local filename="$1"
    local path
    local filename_slug
    path="$(dirname "$filename")"
    filename="$(basename "$filename")"
    filename_slug="${filename//[^a-zA-Z0-9\.]/_}"
    if [ "$filename" != "$filename_slug" ]
    then
        printf '\033[1m"\033[0m%s\033[1m" -> "\033[0m%s\033[1m"\033[0m\n' \
            "$(f_chomp "$filename")" "$(f_chomp "$filename_slug")"
        filename_slug="$path/$filename_slug"
        save_mv "$f" "$filename_slug"
        c="$((c+1))"
    fi
}

for f in ./videos/{saved,downloaded,unlisted}/*.{flv,mp4,webm}
do
    [ -f "$f" ] && slug_video "$f"
done
for f in ./videos/users/*/*.{flv,mp4,webm}
do
    [ -f "$f" ] && slug_video "$f"
done

echo ""
echo "updated $c filenames."

