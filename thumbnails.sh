#!/bin/bash

# RESOLUTION=320x240 # 4:3
RESOLUTION=320x180 # 16:9

if [ ! -f index.php ] || [ ! -f video.php ]
then
    echo "Error make sure to be in the opentube root dir"
    exit 1
fi

mkdir -p thumbnails

function generate_thumbnail_static() {
    video_path="$1"
    png_path="thumbnails/$(basename "$video_path").png"
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
        "$png_path"
    # echo "seconds: '$seconds' second: '$second' $video_path"
}

function generate_thumbnail() {
    video_path="$1"
    gif_path="thumbnails/$(basename "$video_path").gif"
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
for video in \
    ./saved_videos/*.flv \
    ./saved_videos/*.mp4 \
    ./saved_videos/*.webm
do
    generate_thumbnail "$video"
    generate_thumbnail_static "$video"
done

