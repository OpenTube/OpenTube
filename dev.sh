#!/bin/bash

function check_deps() {
    # shell lint
    if [ ! -x "$(command -v shellcheck)" ]
    then
        echo "Error: you need shellcheck installed"
        exit 1
    fi
    # html lint
    if [ ! -x "$(command -v html5validator)" ]
    then
        if [ ! -x "$(command -v python3)" ]
        then
            echo "Error: you need python3 installed"
            exit 1
        fi
        if [ ! -x "$(command -v java)" ]
        then
            echo "Error: you need java 8 installed"
            exit 1
        fi
        python3 -m pip install html5validator
    fi
    # youtube-dl
    if [ ! -x "$(command -v youtube-dl)" ]
    then
        echo "Error: you need youtube-dl installed"
        echo "  curl -L https://yt-dl.org/downloads/latest/youtube-dl -o /usr/local/bin/youtube-dl"
        echo "  chmod a+rx /usr/local/bin/youtube-dl"
        exit 1
    fi
}

function install_deps() {
    check_deps
    # javascript lint
    if [ ! -x "$(command -v npm)" ]
    then
        echo "Error: you need npm installed"
        exit 1
    fi
    npm install
}

function run_app() {
    local port=$1
    if pgrep -f php > /dev/null
    then
        echo "Error: some php process is already running"
        for proc in $(pgrep -f php)
        do
            printf "  %s\\n" "$(ps o cmd -p "$proc" | tail -n1)"
        done
        exit 1
    fi
    if [[ ! -d .git ]] || [[ ! -f index.php ]]
    then
        echo "Error: this is not a OpenTube directory."
        exit 1
    fi
    ${BROWSER:-firefox} "http://localhost:$port/index.php" &
    php -S "localhost:$port"
}

function log() {
    printf '\033[1m[\033[0m*\033[1m]\033[0m '
    if [ "$1" == "-n" ]
    then
        printf "%s" "$2"
    else
        echo "$1"
    fi
}

function err() {
    printf '\033[1m[\033[0m-\033[1m]\033[0m '
    if [ "$1" == "-n" ]
    then
        printf "%s" "$2"
    else
        echo "$1"
    fi
}

function run_test_verbose() {
    local name="$1"
    local cmd="$2"
    log "$name"
    if ! eval "$cmd"
    then
        exit 1
    fi
}

function run_test() {
    local name="$1"
    local cmd="$2"
    log -n "$name"
    if eval "$cmd"
    then
        echo " ... OK"
    else
        exit 1
    fi
}

function test_lint_php() {
    local f
    mkdir -p test
    for f in ./*.php
    do
        if ! php -l "$f"
        then
            exit 1
        fi
    done
}

function test_run_php() {
    local f
    local html
    mkdir -p test
    for f in ./*.php
    do
        html="test/$(basename "$f" .php).html"
        if ! php "$f" > "$html"
        then
            exit 1
        fi
    done
}

function test_dl_videos() {
    mkdir -p videos/saved
    (
        cd videos/saved || exit 1
        youtube-dl --quiet https://www.youtube.com/watch?v=tPEE9ZwTmy0
        youtube-dl --quiet https://www.youtube.com/watch?v=YDiZB42z3TM
    ) || exit 1
}

function test_thumbnails() {
    bash -e ./scripts/thumbnails.sh
    local count=0
    count="$(find thumbnails/ | wc -l)"
    if [ "$count" -lt "4" ]
    then
        echo "error: expected 4 thumbnails but got $count"
        exit 1
    fi
}

function test_slug() {
    bash -e ./scripts/slug_video_names.sh
    if find videos/saved/ -type f -printf "%f\\n" | grep -q '[^a-zA-Z0-9\._]'
    then
        err "ERROR: found non slugged characters:"
        echo ""
        find videos/saved/ -type f -printf "%f\\n" | grep --color=auto '[^a-zA-Z0-9\._]'
        exit 1
    fi
}

function run_tests() {
    run_test "shellcheck" \
        'find . -name "*.sh" -print0 | xargs --null shellcheck'
    run_test "javascript standard" \
        "npx standard"
    run_test_verbose "php lint" \
        "test_lint_php"
    run_test_verbose "run php" \
        "test_run_php"
    run_test "validate html" \
        "html5validator --root test/"
    run_test "download sample videos" \
        "test_dl_videos"
    run_test_verbose "script/slug_video_names.sh" \
        "test_slug"
    run_test "script/thumbnails.sh" \
        "test_thumbnails"
}

mkdir -p videos/{saved,downloaded,unlisted}

if [ "$1" == "test" ]
then
    run_tests
elif [ "$1" == "run" ]
then
    run_app "${2:-8888}"
elif [ "$1" == "install" ]
then
   install_deps
else
    echo "usage: ./test.sh <test|install|run [PORT]>"
    echo "  test"
    echo "    runs tests"
    echo "  install"
    echo "    installs test depdencys"
    echo "  run [PORT]"
    echo "    runs the opentube web app"
    exit 0
fi

