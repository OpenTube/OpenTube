#!/bin/bash

function generate_thumbnail() {
    video_path="$1"
    gif_path="thumbnails/$(basename "$video_path").gif"
    ffmpeg -i "$video_path" \
        -ss 00:00:00.000 \
        -pix_fmt rgb24 \
        -r 10 \
        -s 320x240 \
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
done

