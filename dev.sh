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
    run_test "validate html" \
        "html5validator --root test/"
}

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

