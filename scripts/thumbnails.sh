#!/bin/bash

# RESOLUTION=320x240 # 4:3
RESOLUTION=320x180 # 16:9

if [ ! -f index.php ] || [ ! -f video.php ]
then
    echo "Error make sure to be in the opentube root dir"
    exit 1
fi

arg_force=0

for arg in "$@"
do
    if [ "$arg" == "--help" ] || [ "$arg" == "-h" ] || [ "$arg" == "help" ]
    then
        echo "usage: $0 [OPTION]"
        echo "options:"
        echo "  --force|-f      regenerate existing thumbnails"
        exit 0
    elif [ "$arg" == "--force" ] || [ "$arg" == "-f" ]
    then
        arg_force=1
    else
        echo "invalid argument '$arg' check help for more info"
        tput bold
        echo "  $0 --help"
        tput sgr0
        exit 1
    fi
done

mkdir -p thumbnails

function generate_thumbnail_static() {
    local video_path="$1"
    local png_path="$2"
    seconds="$(ffprobe \
        -v error \
        -show_entries format=duration \
        -of default=noprint_wrappers=1:nokey=1 \
        "$video_path" \
        | cut -d'.' -f1)"
    second="$((seconds/2))"
    # 1 or 0 second long videos should be 0.1 not 0
    second="$second.1"
    ffmpeg \
        -y \
        -ss "$second" \
        -i "$video_path" \
        -t 1 \
        -s "$RESOLUTION" \
        -f image2 \
        -frames:v 1 \
        "$png_path"
    # echo "seconds: '$seconds' second: '$second' $video_path"
}

function generate_thumbnail() {
    local video_path="$1"
    local gif_path="$2"
    ffmpeg \
        -y \
        -i "$video_path" \
        -ss 00:00:00.000 \
        -pix_fmt rgb24 \
        -r 10 \
        -s "$RESOLUTION" \
        -t 00:00:10.000 \
        "$gif_path"
}

shopt -s nullglob

function generate_thumbnail_all() {
    local dir="$1"
    local video="$2"
    local img="$3"
    img="thumbnails/$dir/$(basename "$video").gif"
    if [ ! -f "$img" ] || [ "$arg_force" == "1" ]
    then
        generate_thumbnail "$video" "$img"
    fi
    img="thumbnails/$dir/$(basename "$video").png"
    if [ ! -f "$img" ] || [ "$arg_force" == "1" ]
    then
        generate_thumbnail_static "$video" "$img"
    fi
}

for category in {saved,downloaded,unlisted}
do
    mkdir -p thumbnails/"$category"
    for video in \
        ./videos/"$category"/*.flv \
        ./videos/"$category"/*.mp4 \
        ./videos/"$category"/*.webm
    do
        generate_thumbnail_all "$category" "$video" "$img"
    done
done

for user in ./videos/users/*/
do
    user="${user:9:-1}"
    mkdir -p thumbnails/"$user"
    for video in \
        ./videos/"$user"/*.flv \
        ./videos/"$user"/*.mp4 \
        ./videos/"$user"/*.webm
    do
        generate_thumbnail_all "$user" "$video" "$img"
    done
done

