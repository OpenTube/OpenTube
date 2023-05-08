# OpenTube
Basic video streaming platform. Simple php website to watch videos.

### Dependencys

        apt install php ffmpeg
        sudo curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp
        sudo chmod a+rx /usr/local/bin/yt-dlp


### Setup

        git clone https://github.com/OpenTube/OpenTube
        cd OpenTube

generate video folder and download sample video

        mkdir -p videos/{saved,downloaded,unlisted} && cd videos/saved/
        yt-dlp -f mp4 https://www.youtube.com/watch?v=2r1D-sXTVTo

generate thumbnails using ffmpeg
has to be run after adding new videos
there are two types of thumbnails:
- static png from the middle of the video
- animated gif showing the first seconds of the video

        cd ..
        ./scripts/thumbnails.sh

start test php server and open browser
not production ready just for testing

        ./dev.sh run

### Test

        ./dev.sh install
        ./dev.sh test

### Customization

You can write your own css at ``css/custom.css`` if you want to avoid git conflicts.

You can prepend or append to the landing page via ``custom/pre_index.php`` and ``custom/post_index.php``

The custom html header can be edited at ``custom/header.php``

Specify your own search term mappings in ``custom/search.csv`` check the example at ``data/search.csv``
it allows you to show search results for 'terminal' when the user typed in 'console'

### License

The whole code base and all images are licensed under public domain.
All graphics were handcrafted by [ChillerDragon](https://github.com/ChillerDragon) same goes for the code.
You are free to use any of it for anything. You are free to copy/redistribute/sell/edit this project without any limitations. Without any warranty tho for more information see LICENSE file at the root of this repository.

Credit is appreciated but not required.
