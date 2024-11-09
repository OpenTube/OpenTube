#!/bin/bash

set -u

OPENTUBE_TOKEN="$(cat /proc/sys/kernel/random/uuid)"

get_id_by_username() {
	local username="$1"
	sqlite3 -init /dev/null db/opentube.db < <(echo "SELECT ID FROM Users WHERE Username = '$username';")
}

insert_token_query() {
	local username="$1"
	local user_id
	user_id="$(get_id_by_username "$username")"
	if [ "$user_id" = "" ]
	then
		echo "Error: user with name '$username' not found." 1>&2
		exit 1
	fi
	local expire_date
	local issue_date
	expire_date="$(date '+%F %H:%M:%S' -d '+3 days')"
	issue_date="$(date '+%F %H:%M:%S')"
	cat <<-EOF
	INSERT INTO Tokens
	(UUID, Title, Username, UserID, ExpireDate, IssueDate, IssuerIp) VALUES
	('$OPENTUBE_TOKEN', 'add_all_script_xxx', '$username', $user_id, '$expire_date', '$issue_date', '127.0.0.1');
	EOF
}

cleanup_tmp_tokens() {
	sqlite3 db/opentube.db < <(echo "DELETE FROM Tokens WHERE Title = 'add_all_script_xxx';")
}

add_tmp_token_for_user() {
	local username="$1"
	sqlite3 db/opentube.db < <(insert_token_query "$username")
}

for user_path in videos/users/*/
do
	[[ -d "$user_path" ]] || continue

	username="$(basename "$user_path")"

	echo "[$username] generating tmp token $OPENTUBE_TOKEN ..."
	add_tmp_token_for_user "$username"

	for filepath in "$user_path"*.mp4
	do
		[[ -f "$filepath" ]] || continue

		title="$(basename "$filepath" .mp4)"
		filename="$(basename "$filepath")"
		[[ "$filename" = "" ]] && { echo "[-] Error: filename empty. (path: $filepath)"; exit 1; }
		[[ "$title" = "" ]]    && { echo "[-] Error: title empty. (path: $filepath)"; exit 1; }

		echo "[$username] $title"
		curl 'http://localhost/OpenTube/php/add_video.php' \
			-X POST \
			-H 'Content-Type: application/x-www-form-urlencoded' \
			--data-raw "title=$title&description=todo&filepath=$filename&token=$OPENTUBE_TOKEN&username=$username"
	done
done

echo "[done] cleaning up tmp tokens ..."
cleanup_tmp_tokens

