#!/bin/bash

# To allow video uploads by my personal user
# And video deletion by the web server
# I give group and user enough permissions and then do something like

# chown -R myuser:webuser videos
# chown -R chiller:www-data videos

# to make sure the web user then can create and delete videos run this script
# recursivley give write permission to the owning group on all video folders
# should be run every time a user folder is added

sudo chmod -R g+w videos
sudo chmod -R u+w videos

