# OpenTube
Basic video streaming platform. Simple php website to watch videos.

### Dependencys

```
apt install php ffmpeg
curl -L https://yt-dl.org/downloads/latest/youtube-dl -o /usr/local/bin/youtube-dl
chmod a+rx /usr/local/bin/youtube-dl
```

### Setup
```
git clone https://github.com/OpenTube/OpenTube
cd OpenTube

# generate video folder and download sample video
mkdir -p saved_videos && cd saved_videos
youtube-dl -f mp4 https://www.youtube.com/watch?v=2r1D-sXTVTo

# generate thumbnails using ffmpeg
# has to be run after adding new videos
# there are two types of thumbnails:
# - static png from the middle of the video
# - animated gif showing the first seconds of the video
cd ..
./thumbnails.sh

# start test php server and open browser
# not production ready just for testing
./test.sh
```

### license

The whole code base and all images are licensed under public domain.
All graphics were handcrafted by [ChillerDragon](https://github.com/ChillerDragon) same goes for the code.
You are free to use any of it for anything. You are free to copy/redistribute/sell/edit this project without any limitations. Without any warranty tho for more information see LICENSE file at the root of this repository.

Credit is appreciated but not required.
