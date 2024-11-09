#!/bin/bash

# As a quick hack I created tokens for all users from the web view
# Then I ran this sql query to set all tokens to "token"
#
#  UPDATE Tokens SET UUID = 'token';
#
# ran the script
#
#  DELETE FROM Tokens;
#
# But in the future there should be a admin token that can access
# data from other users
# and this token can then be used by the script

OPENTUBE_TOKEN=token

for user_path in videos/users/*/
do
	[[ -d "$user_path" ]] || continue

	username="$(basename "$user_path")"
	for filepath in "$user_path"*.mp4
	do
		[[ -f "$filepath" ]] || continue

		title="$(basename "$filename" .mp4)"
		filename="$(basename "$filepath")"
		echo "[$username] $title"
		curl 'http://localhost/OpenTube/php/add_video.php' \
			-X POST \
			-H 'Content-Type: application/x-www-form-urlencoded' \
			--data-raw "title=$title&description=todo&filepath=$filename&token=$OPENTUBE_TOKEN&username=$username"
	done
done

