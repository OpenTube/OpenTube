#!/bin/bash

function run_app() {
    local port=$1
    if pgrep -f php > /dev/null
    then
        echo "Error: some php process is already running"
        for proc in $(pgrep -f php)
        do
            printf "  %s\n" "$(ps o cmd -p "$proc" | tail -n1)"
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

function run_tests() {
    run_test "shellcheck" \
        'find . -name "*.sh" -print0 | xargs --null shellcheck'
    run_test "javascript standard" \
        "npx standard"
    run_test_verbose "php lint" \
        "test_lint_php"
    run_test_verbose "run php" \
        "test_run_php"
}

if [ "$#" == "0" ] || [ "$1" == "--help" ] || [ "$1" == "-h" ]
then
    echo "usage: ./test.sh <test|run [PORT]>"
    exit 0
elif [ "$1" == "test" ]
then
    run_tests
elif [ "$1" == "run" ]
then
    run_app "${2:-8888}"
fi

