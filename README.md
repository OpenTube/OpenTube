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
mkdir videos
youtube-dl -f mp4 https://www.youtube.com/watch?v=2r1D-sXTVTo
./test.sh
```
