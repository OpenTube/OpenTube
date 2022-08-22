#!/bin/bash

# To allow video uploads by my personal user
# And video deletion by the web server
# I give group and user enough permissions and then do something like

# chown -R myuser:webuser videos
# chown -R chiller:www-data videos

# to make sure the web user then can create and delete videos run this script
# recursivley give write permission to the owning group on all video folders
# should be run every time a user folder is added

function check_dir() {
	local dir="$1"
	if [ ! -d "$dir" ]
	then
		echo "Error: directory $dir not found"
		echo "       you have in the root of OpenTube"
		exit 1
	fi
}
check_dir scripts
check_dir videos
check_dir .git
check_dir php

webgroup="$(getent group | cut -d: -f1 | grep -E '^(www-data|http)$' | tail -n 1)"

if [ "$USER" == "" ]
then
	echo $'Error: $USER variable not set'
	exit 1
fi

echo "Detected user: $USER"
if [ "$webgroup" == "" ]
then
	echo "Enter group name of web server:"
	read -r webgroup
else
	echo "Detected web server group: $webgroup"
fi

sudo chown -R "$USER:$webgroup" videos
sudo chown -R "$USER:$webgroup" db

sudo chmod -R g+w db

sudo chmod -R g+w videos
sudo chmod -R u+w videos

